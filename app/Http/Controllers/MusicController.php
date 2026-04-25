<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Song;
use App\Models\Album;


class MusicController extends Controller
{
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

        // Ambil info video guna yt-dlp
        $command = "yt-dlp -j " . escapeshellarg($url);
        $jsonOutput = shell_exec($command);
        
        if (!$jsonOutput) {
            return response()->json(['error' => 'Failed to get video info'], 500);
        }

        $info = json_decode($jsonOutput, true);
        if (!$info) {
            return response()->json(['error' => 'Invalid video info'], 500);
        }

        return response()->json([
            'title' => $info['title'] ?? 'Unknown Title',
            'thumbnail' => $info['thumbnail'] ?? null,
            'duration' => $info['duration'] ?? 0,
            'uploader' => $info['uploader'] ?? 'Unknown Uploader',
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
        $storageFolder = storage_path("app/public/music/converted");
        
        if (!file_exists($storageFolder)) {
            mkdir($storageFolder, 0755, true);
        }

        // Allow per-machine configuration and fall back to PATH binaries.
        $ffmpegPath = env('FFMPEG_PATH');

        // Temp log file
        $logFile = storage_path("app/public/music/convert_progress.txt");
        file_put_contents($logFile, "Starting conversion...\n");

        // Ambil info video
        $jsonOutput = shell_exec("yt-dlp -j " . escapeshellarg($url));
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
                $cmd = "yt-dlp -f \"bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best\" --embed-metadata --embed-thumbnail ";
                if (!empty($ffmpegPath)) {
                    $cmd .= "--ffmpeg-location " . escapeshellarg($ffmpegPath) . " ";
                }
                $cmd .= "--no-playlist "
                    . escapeshellarg($info['webpage_url'])
                    . " -o " . escapeshellarg($outputPath)
                    . " 2>&1";
            } else {
                $cmd = "yt-dlp -x --audio-format mp3 --embed-metadata --embed-thumbnail --parse-metadata \"%(uploader)s:%(album)s\" ";
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
            'video' => 'required|file|mimes:mp4,mkv,avi,mov,flv,wmv|max:512000', // 500MB limit
            'album_id' => 'nullable|exists:albums,id'
        ]);

        $videoFile = $request->file('video');
        $videoPath = $videoFile->getRealPath();
        $originalName = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeTitle = preg_replace('/[^\w\- ]+/', '', $originalName);
        $safeTitle = mb_substr($safeTitle, 0, 100);

        $storageFolder = storage_path("app/public/music/converted");
        if (!file_exists($storageFolder)) {
            mkdir($storageFolder, 0755, true);
        }

        $outputPath = $storageFolder . DIRECTORY_SEPARATOR . $safeTitle . '.mp3';
        $publicPath = "music/converted/{$safeTitle}.mp3";

        // Get duration using ffprobe
        $ffprobeCmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($videoPath);
        $duration = (int) shell_exec($ffprobeCmd);

        // Convert using ffmpeg
        // We use absolute path for output to avoid issues
        $ffmpegCmd = "ffmpeg -y -i " . escapeshellarg($videoPath) . " -vn -ar 44100 -ac 2 -b:a 192k " . escapeshellarg($outputPath) . " 2>&1";
        
        exec($ffmpegCmd, $output, $returnCode);

        if ($returnCode !== 0) {
            return response()->json([
                'error' => 'Conversion failed', 
                'details' => $output,
                'command' => $ffmpegCmd
            ], 500);
        }

        if (file_exists($outputPath)) {
            $song = Song::firstOrCreate(
                ['file_path' => $publicPath],
                [
                    'title' => $safeTitle,
                    'source_url' => 'Uploaded Video',
                    'duration' => $duration,
                    'album_id' => $request->input('album_id'),
                ]
            );

            return response()->json([
                'success' => true,
                'song' => [
                    'id' => $song->id,
                    'name' => $safeTitle . '.mp3',
                    'url' => asset("storage/music/converted/" . rawurlencode($safeTitle . '.mp3')),
                    'download_url' => route('music.download', ['filename' => $safeTitle . '.mp3'])
                ]
            ]);
        }

        return response()->json(['error' => 'Output file was not created'], 500);
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
            'Content-Type' => 'audio/mpeg',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }
}