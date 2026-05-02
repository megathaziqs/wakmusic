<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Song;
use App\Models\Album;


class MusicController extends Controller
{
    private const UPLOAD_VIDEO_MAX_KILOBYTES = 512000;
    private const UPLOAD_VIDEO_MAX_MEGABYTES = 500;

    private function getBinaryCommand(string $envKey, string $default): string
    {
        $customPath = env($envKey);
        if (!empty($customPath)) {
            return escapeshellarg($customPath);
        }

        return $default;
    }

    private function getFfmpegToolCommand(string $binary): string
    {
        $ffmpegPath = env('FFMPEG_PATH');
        if (empty($ffmpegPath)) {
            return $binary;
        }

        $normalizedPath = rtrim($ffmpegPath, "\\/");
        $binaryName = $binary . (str_ends_with(strtolower($binary), '.exe') ? '' : '.exe');

        if (is_dir($normalizedPath)) {
            $candidate = $normalizedPath . DIRECTORY_SEPARATOR . $binaryName;
            if (is_file($candidate)) {
                return escapeshellarg($candidate);
            }
        }

        if (is_file($normalizedPath)) {
            if (strtolower(pathinfo($normalizedPath, PATHINFO_FILENAME)) === strtolower(pathinfo($binary, PATHINFO_FILENAME))) {
                return escapeshellarg($normalizedPath);
            }

            $candidate = dirname($normalizedPath) . DIRECTORY_SEPARATOR . $binaryName;
            if (is_file($candidate)) {
                return escapeshellarg($candidate);
            }
        }

        return $binary;
    }

    private function isCommandAvailable(string $command): bool
    {
        exec($command . " --version 2>&1", $output, $returnCode);
        return $returnCode === 0;
    }

    private function safeTitle(string $title, string $fallbackPrefix = 'converted-video'): string
    {
        $safeTitle = preg_replace('/[^\w\- ]+/', '', $title);
        $safeTitle = trim((string) $safeTitle);
        $safeTitle = mb_substr($safeTitle, 0, 100);

        return $safeTitle !== '' ? $safeTitle : $fallbackPrefix . '-' . now()->format('Ymd-His');
    }

    private function convertedStorageFolder(): string
    {
        $storageFolder = storage_path("app/public/music/converted");
        if (!file_exists($storageFolder)) {
            mkdir($storageFolder, 0755, true);
        }

        return $storageFolder;
    }

    private function tempConverterFolder(): string
    {
        $tempFolder = storage_path("app/temp/converter");
        if (!file_exists($tempFolder)) {
            mkdir($tempFolder, 0755, true);
        }

        return $tempFolder;
    }

    private function mediaContentType(string $filename): string
    {
        return match (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            'mp4' => 'video/mp4',
            default => 'audio/mpeg',
        };
    }

    private function buildFfmpegConvertCommand(string $ffmpeg, string $inputPath, string $outputPath, string $format): string
    {
        if ($format === 'mp4') {
            return $ffmpeg . " -y -i " . escapeshellarg($inputPath)
                . " -c:v libx264 -preset veryfast -crf 23 -c:a aac -b:a 192k -movflags +faststart "
                . escapeshellarg($outputPath)
                . " 2>&1";
        }

        return $ffmpeg . " -y -i " . escapeshellarg($inputPath)
            . " -vn -ar 44100 -ac 2 -b:a 192k "
            . escapeshellarg($outputPath)
            . " 2>&1";
    }

    private function convertLocalVideoSource(
        string $videoPath,
        string $title,
        string $sourceLabel,
        ?string $albumId,
        string $format
    ): array {
        $safeTitle = $this->safeTitle($title);
        $ext = $format === 'mp4' ? 'mp4' : 'mp3';
        $storageFolder = $this->convertedStorageFolder();
        $outputPath = $storageFolder . DIRECTORY_SEPARATOR . $safeTitle . '.' . $ext;
        $publicPath = "music/converted/{$safeTitle}.{$ext}";
        $ffmpeg = $this->getFfmpegToolCommand('ffmpeg');
        $ffprobe = $this->getFfmpegToolCommand('ffprobe');

        if (!$this->isCommandAvailable($ffmpeg) || !$this->isCommandAvailable($ffprobe)) {
            throw new \RuntimeException('ffmpeg or ffprobe is not available. Install ffmpeg or set FFMPEG_PATH in your .env.');
        }

        $ffprobeCmd = $ffprobe . " -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($videoPath);
        $duration = (int) shell_exec($ffprobeCmd);
        $ffmpegCmd = $this->buildFfmpegConvertCommand($ffmpeg, $videoPath, $outputPath, $format);

        exec($ffmpegCmd, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException('Conversion failed: ' . implode("\n", array_slice($output, -8)));
        }

        if (!file_exists($outputPath)) {
            throw new \RuntimeException('Output file was not created.');
        }

        $song = Song::firstOrCreate(
            ['file_path' => $publicPath],
            [
                'title' => $safeTitle,
                'source_url' => mb_substr($sourceLabel, 0, 255),
                'duration' => $duration,
                'album_id' => $albumId,
            ]
        );

        return [
            'id' => $song->id,
            'name' => $safeTitle . '.' . $ext,
            'url' => asset("storage/music/converted/" . rawurlencode($safeTitle . '.' . $ext)),
            'download_url' => route('music.download', ['filename' => $safeTitle . '.' . $ext]),
        ];
    }

    // Helper to clean YouTube URL
    private function cleanYoutubeUrl($url)
    {
        // If it's a standard youtube.com/watch?v=... link
        if (str_contains($url, 'youtube.com/watch')) {
            $parsedUrl = parse_url($url);
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $params);
                if (isset($params['v'])) {
                    return "https://www.youtube.com/watch?v=" . $params['v'];
                }
            }
        }
        
        // If it's a youtu.be/... link
        if (str_contains($url, 'youtu.be/')) {
            $path = parse_url($url, PHP_URL_PATH);
            $id = ltrim($path, '/');
            if ($id) {
                return "https://www.youtube.com/watch?v=" . $id;
            }
        }

        return $url;
    }

    // Get Video Info for Preview
    public function getInfo(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url']
        ]);

        $url = $this->cleanYoutubeUrl($request->input('url'));
        $ytDlp = $this->getBinaryCommand('YTDLP_PATH', 'yt-dlp');

        if (!$this->isCommandAvailable($ytDlp)) {
            return response()->json([
                'error' => 'yt-dlp is not available. Install yt-dlp or set YTDLP_PATH in your .env.'
            ], 500);
        }

        // Ambil info video guna yt-dlp.
        // On some Windows setups yt-dlp may print extra warning lines,
        // so we later extract the first valid JSON line.
        $command = $ytDlp . " --dump-single-json --no-playlist --skip-download " . escapeshellarg($url) . " 2>&1";
        $jsonOutput = shell_exec($command);
        
        if (!$jsonOutput) {
            return response()->json(['error' => 'Failed to get video info'], 500);
        }

        $info = json_decode($jsonOutput, true);
        if (!$info) {
            $lines = preg_split('/\r\n|\r|\n/', trim($jsonOutput));
            foreach ($lines as $line) {
                if (empty(trim($line))) {
                    continue;
                }
                $candidate = json_decode($line, true);
                if (is_array($candidate) && !empty($candidate['id'])) {
                    $info = $candidate;
                    break;
                }
            }
        }

        if (!$info) {
            return response()->json([
                'error' => 'Invalid video info',
                'debug' => mb_substr($jsonOutput, 0, 300),
            ], 500);
        }

        $thumbnail = $info['thumbnail'] ?? null;
        if (empty($thumbnail) && !empty($info['thumbnails']) && is_array($info['thumbnails'])) {
            $lastThumb = end($info['thumbnails']);
            if (is_array($lastThumb) && !empty($lastThumb['url'])) {
                $thumbnail = $lastThumb['url'];
            }
        }

        $videoId = $info['id'] ?? null;
        if (empty($thumbnail) && !empty($videoId)) {
            $thumbnail = 'https://i.ytimg.com/vi/' . $videoId . '/hqdefault.jpg';
        }

        return response()->json([
            'title' => $info['title'] ?? 'Unknown Title',
            'thumbnail' => $thumbnail,
            'duration' => $info['duration'] ?? 0,
            'uploader' => $info['uploader'] ?? 'Unknown Uploader',
            'video_id' => $videoId,
            'cleaned_url' => $url
        ]);
    }

    // Convert YouTube → MP3 / MP4
    public function convert(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url'],
            'album_id' => ['nullable', 'exists:albums,id'],
            'format' => ['sometimes', 'in:mp3,mp4']
        ]);

        $url = $this->cleanYoutubeUrl($request->input('url'));
        $albumId = $request->input('album_id');
        $format = $request->input('format', 'mp3');
        $ytDlp = $this->getBinaryCommand('YTDLP_PATH', 'yt-dlp');
        $storageFolder = $this->convertedStorageFolder();

        // Allow per-machine configuration and fall back to PATH binaries.
        $ffmpegPath = env('FFMPEG_PATH');

        if (!$this->isCommandAvailable($ytDlp)) {
            return response()->json([
                'error' => 'yt-dlp is not available. Install yt-dlp or set YTDLP_PATH in your .env.'
            ], 500);
        }

        // Temp log file
        $logFile = storage_path("app/public/music/convert_progress.txt");
        file_put_contents($logFile, "Starting conversion...\n");

        // Ambil info video
        $jsonOutput = shell_exec($ytDlp . " -j " . escapeshellarg($url) . " 2>&1");
        if (!$jsonOutput) {
            return response()->json(['error' => 'Failed to get video info'], 500);
        }

        $lines = explode("\n", trim($jsonOutput));
        $mp3Paths = [];

        foreach ($lines as $idx => $line) {
            if (empty($line)) continue;

            $info = json_decode($line, true);
            if (!$info) continue;
            
            $duration = $info['duration'] ?? null;
            $videoTitle = $info['title'] ?? 'unknown';
            $safeTitle = preg_replace('/[^\w\- ]+/', '', $videoTitle);
            $safeTitle = mb_substr($safeTitle, 0, 100);

            // Determine extension based on format
            $ext = ($format === 'mp4') ? 'mp4' : 'mp3';

            $outputPath = $storageFolder . DIRECTORY_SEPARATOR . $safeTitle . '.' . $ext;
            $publicPath = "music/converted/{$safeTitle}.{$ext}";

            file_put_contents(
                $logFile,
                "Converting video " . ($idx + 1) . " to {$format}: {$videoTitle}\n",
                FILE_APPEND
            );

            if ($format === 'mp4') {
                $cmd = $ytDlp . " -f \"bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best\" --embed-metadata --embed-thumbnail ";
                if (!empty($ffmpegPath)) {
                    $cmd .= "--ffmpeg-location " . escapeshellarg($ffmpegPath) . " ";
                }
                $cmd .= "--no-playlist "
                    . escapeshellarg($info['webpage_url'])
                    . " -o " . escapeshellarg($outputPath)
                    . " 2>&1";
            } else {
                $cmd = $ytDlp . " -x --audio-format mp3 --embed-metadata --embed-thumbnail --parse-metadata \"%(uploader)s:%(album)s\" ";
                if (!empty($ffmpegPath)) {
                    $cmd .= "--ffmpeg-location " . escapeshellarg($ffmpegPath) . " ";
                }
                $cmd .= "--no-playlist "
                    . escapeshellarg($info['webpage_url'])
                    . " -o " . escapeshellarg($outputPath)
                    . " 2>&1";
            }

            $process = popen($cmd, 'r');
            while (!feof($process)) {
                $lineOut = fgets($process);
                if ($lineOut) {
                    file_put_contents($logFile, trim($lineOut) . "\n", FILE_APPEND);
                }
            }
            pclose($process);

            // 🔥 PENTING: walaupun file dah wujud, DB tetap sync
            if (file_exists($outputPath)) {

                Song::firstOrCreate(
                    ['file_path' => $publicPath],
                    [
                        'title' => $safeTitle,
                        'source_url' => $url,
                        'duration' => $duration,
                        'album_id' => $albumId,
                    ]
                );


                $mp3Paths[] = [
                    'name' => $safeTitle . '.' . $ext,
                    'url' => asset("storage/music/converted/" . rawurlencode($safeTitle . '.' . $ext)),
                    'download_url' => route('music.download', ['filename' => $safeTitle . '.' . $ext])
                ];
            }
        }

        file_put_contents($logFile, "Conversion finished.\n", FILE_APPEND);

        return response()->json([
            'paths' => $mp3Paths
        ]);
    }

    // Convert Uploaded Video → MP3
    public function uploadVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,mkv,avi,mov,flv,wmv,webm,m4v|max:' . self::UPLOAD_VIDEO_MAX_KILOBYTES,
            'album_id' => 'nullable|exists:albums,id',
            'format' => ['sometimes', 'in:mp3,mp4'],
        ], [
            'video.max' => 'The selected video must not be larger than ' . self::UPLOAD_VIDEO_MAX_MEGABYTES . ' MB.'
        ]);

        $videoFile = $request->file('video');
        $videoPath = $videoFile->getRealPath();
        $originalName = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
        $format = $request->input('format', 'mp3');

        try {
            $song = $this->convertLocalVideoSource(
                $videoPath,
                $originalName,
                'Computer Video',
                $request->input('album_id'),
                $format
            );

            return response()->json([
                'success' => true,
                'song' => $song,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function convertSourceUrl(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url'],
            'album_id' => ['nullable', 'exists:albums,id'],
            'format' => ['sometimes', 'in:mp3,mp4'],
        ]);

        $url = $request->input('url');
        $albumId = $request->input('album_id');
        $format = $request->input('format', 'mp3');
        $ytDlp = $this->getBinaryCommand('YTDLP_PATH', 'yt-dlp');

        if (!$this->isCommandAvailable($ytDlp)) {
            return response()->json([
                'error' => 'yt-dlp is not available. Install yt-dlp or set YTDLP_PATH in your .env.'
            ], 500);
        }

        $logFile = storage_path("app/public/music/convert_progress.txt");
        file_put_contents($logFile, "Fetching source video...\n");

        $title = 'cloud-video-' . now()->format('Ymd-His');
        $infoOutput = shell_exec($ytDlp . " --dump-single-json --no-playlist --skip-download " . escapeshellarg($url) . " 2>&1");
        if ($infoOutput) {
            $info = json_decode($infoOutput, true);
            if (is_array($info) && !empty($info['title'])) {
                $title = $info['title'];
            }
        }

        $tempFolder = $this->tempConverterFolder();
        $tempPrefix = 'source-' . uniqid('', true);
        $tempTemplate = $tempFolder . DIRECTORY_SEPARATOR . $tempPrefix . '.%(ext)s';
        $downloadCmd = $ytDlp
            . " --no-playlist --max-filesize " . escapeshellarg(self::UPLOAD_VIDEO_MAX_MEGABYTES . 'M')
            . " -o " . escapeshellarg($tempTemplate)
            . " --print after_move:filepath "
            . escapeshellarg($url)
            . " 2>&1";
        exec($downloadCmd, $downloadOutput, $downloadCode);

        if ($downloadCode !== 0) {
            foreach (glob($tempFolder . DIRECTORY_SEPARATOR . $tempPrefix . '.*') ?: [] as $partialPath) {
                if (is_file($partialPath)) {
                    unlink($partialPath);
                }
            }

            return response()->json([
                'error' => 'Failed to fetch the source video. Make sure the Google Drive file is shared publicly, under 500 MB, or use a direct video link.',
                'details' => array_slice($downloadOutput, -8),
            ], 500);
        }

        $downloadedPath = null;
        foreach (array_reverse($downloadOutput) as $line) {
            $candidate = trim($line, "\" \t\n\r\0\x0B");
            if ($candidate !== '' && file_exists($candidate)) {
                $downloadedPath = $candidate;
                break;
            }
        }

        if (!$downloadedPath) {
            $matches = glob($tempFolder . DIRECTORY_SEPARATOR . $tempPrefix . '.*');
            $downloadedPath = $matches[0] ?? null;
        }

        if (!$downloadedPath || !file_exists($downloadedPath)) {
            foreach (glob($tempFolder . DIRECTORY_SEPARATOR . $tempPrefix . '.*') ?: [] as $partialPath) {
                if (is_file($partialPath)) {
                    unlink($partialPath);
                }
            }

            return response()->json(['error' => 'Downloaded source file was not found.'], 500);
        }

        try {
            file_put_contents($logFile, "Converting source video to {$format}...\n", FILE_APPEND);
            $song = $this->convertLocalVideoSource($downloadedPath, $title, $url, $albumId, $format);
            file_put_contents($logFile, "Conversion finished.\n", FILE_APPEND);

            return response()->json([
                'success' => true,
                'song' => $song,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        } finally {
            if (file_exists($downloadedPath)) {
                unlink($downloadedPath);
            }
        }
    }

    // Poll progress (GET request, tanpa validation url)
    public function progress()
    {
        $logFile = storage_path("app/public/music/convert_progress.txt");
        if (!file_exists($logFile)) return response()->json(['progress' => 'No conversion running.']);
        $content = file_get_contents($logFile);
        return response()->json(['progress' => $content]);
    }

    public function list()
    {
        try {
            $songs = Song::with('album')->orderBy('created_at', 'desc')->get();
            
            $data = $songs->map(function($song) {
                $filename = basename($song->file_path);
                return [
                    'id' => $song->id,
                    'name' => $filename,
                    'title' => $song->title ?: pathinfo($filename, PATHINFO_FILENAME),
                    'album' => ($song->album) ? $song->album->name : 'Single',
                    'duration' => $song->duration,
                    'url' => asset("storage/" . $song->file_path),
                    'download_url' => route('music.download', ['filename' => $filename]),
                    'created_at' => $song->created_at ? $song->created_at->format('M d, Y') : 'Unknown'
                ];
            });

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete($filename)
    {
        $safeName = preg_replace('/[^\w\-. ]+/', '', $filename);

        // Cari lagu dalam DB
        $song = Song::where('file_path', 'music/converted/' . $safeName)->first();

        if (!$song) {
            return response()->json(['error' => 'Song not found in database'], 404);
        }

        // Padam file jika wujud
        $filePath = storage_path('app/public/' . $song->file_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Padam DB
        $song->delete();

        return response()->json(['success' => true]);
    }

    public function recent()
    {
        try {
            // Ambil 10 lagu terbaru
            $recent = Song::with('album')->orderBy('created_at', 'desc')->take(10)->get();
            
            $data = $recent->map(function($song) {
                $filename = basename($song->file_path);
                return [
                    'id' => $song->id,
                    'name' => $filename,
                    'title' => $song->title ?: pathinfo($filename, PATHINFO_FILENAME),
                    'album' => ($song->album) ? $song->album->name : 'Single',
                    'url' => asset("storage/" . $song->file_path),
                    'download_url' => route('music.download', ['filename' => $filename]),
                    'created_at' => $song->created_at ? $song->created_at->diffForHumans() : 'Unknown'
                ];
            });

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([], 200); // Return empty array on error for recent
        }
    }

    public function stats()
    {
        $totalSongs = Song::count();
        $totalAlbums = Album::count();
        
        $musicFolder = storage_path('app/public/music/converted');
        $totalSize = 0;
        if (file_exists($musicFolder)) {
            $files = scandir($musicFolder);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $totalSize += filesize($musicFolder . '/' . $file);
                }
            }
        }
        
        // Convert to MB
        $sizeInMb = round($totalSize / (1024 * 1024), 2);
        
        return response()->json([
            'total_songs' => $totalSongs,
            'total_albums' => $totalAlbums,
            'storage_usage' => $sizeInMb . ' MB',
            'status' => 'Online'
        ]);
    }

    public function rename(Request $request)
    {
        $request->validate([
            'oldName' => 'required|string',
            'newName' => 'required|string'
        ]);

        $old = preg_replace('/[^\w\-. \(\)\[\]]+/', '', $request->oldName);
        $new = preg_replace('/[^\w\-. \(\)\[\]]+/', '', $request->newName);

        $oldPath = storage_path("app/public/music/converted/{$old}");

        // Check if old file exists
        if (!file_exists($oldPath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Preserve original extension
        $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
        if (!$extension) $extension = 'mp3'; // Default fallback

        // If new name doesn't have the correct extension, append it
        if (!str_ends_with(strtolower($new), '.' . strtolower($extension))) {
            $new .= '.' . $extension;
        }

        $newPath = storage_path("app/public/music/converted/{$new}");

        // Check if new filename already exists
        if (file_exists($newPath) && $oldPath !== $newPath) {
            return response()->json(['error' => 'A file with this name already exists'], 409);
        }

        // Rename the file
        if (rename($oldPath, $newPath)) {
            // Update database record
            // Use LIKE in case paths differ slightly, but usually it matches
            $song = Song::where('file_path', 'music/converted/' . $old)->first();
            if ($song) {
                $song->file_path = 'music/converted/' . $new;
                $song->title = pathinfo($new, PATHINFO_FILENAME);
                $song->save();
            }

            return response()->json([
                'success' => true,
                'newName' => $new,
                'newUrl' => asset("storage/music/converted/{$new}")
            ]);
        }

        return response()->json(['error' => 'Failed to rename file'], 500);
    }

    public function download($filename)
    {
        // 1. Try literal match first
        $decodedName = urldecode($filename);
        $safeName = preg_replace('/[^\w\-. \(\)\[\]]+/', '', $decodedName);
        $path = storage_path("app/public/music/converted/{$safeName}");

        // 2. If not found, try searching the database for a matching title or path
        if (!file_exists($path)) {
            $nameWithoutExt = pathinfo($safeName, PATHINFO_FILENAME);
            $song = Song::where('title', $nameWithoutExt)
                ->orWhere('file_path', 'like', "%{$safeName}%")
                ->first();
            
            if ($song && file_exists(storage_path('app/public/' . $song->file_path))) {
                $path = storage_path('app/public/' . $song->file_path);
                $safeName = basename($song->file_path);
            } else {
                // Last ditch effort: try the raw filename parameter
                $safeNameSimple = preg_replace('/[^\w\-. \(\)\[\]]+/', '', $filename);
                $pathSimple = storage_path("app/public/music/converted/{$safeNameSimple}");
                
                if (file_exists($pathSimple)) {
                    $path = $pathSimple;
                    $safeName = $safeNameSimple;
                } else {
                    abort(404, 'File not found. Please refresh the page and try again.');
                }
            }
        }

        return response()->download($path, $safeName, [
            'Content-Type' => $this->mediaContentType($safeName),
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }
}
