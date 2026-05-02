# WakMusic Setup (New Laptop)

If the UI theme/assets look different or conversion features fail after cloning, it is usually a local setup issue (env, database, storage link, or missing ffmpeg/yt-dlp), not missing Git files.

## 1) First-time setup

Run from project root:

```powershell
composer run setup
```

This now runs:
- `composer install`
- create `.env` if missing
- `php artisan key:generate`
- `php artisan migrate --force`
- `php artisan db:seed --force`
- `php artisan storage:link`
- `npm install`
- `npm run build`

## 2) Start app

Use two terminals:

```powershell
php artisan serve
```

```powershell
npm run dev
```

Open `http://localhost:8000`.

## 3) Required tools for converter

- `yt-dlp` must be installed and available in PATH
- `ffmpeg` / `ffprobe` must be installed and available in PATH

Optional: set this in `.env` if ffmpeg is not in PATH:

```env
FFMPEG_PATH=C:\ffmpeg\bin
```

## 4) Uploaded video size limit

The logged-in dashboard converter has two main paths:

- URL: paste a YouTube/web URL, then choose MP3 or MP4.
- Video Source: choose a computer video file or paste a Google Drive/direct video link, then choose MP3 or MP4.

Computer and cloud source videos are temporary conversion sources. WakMusic saves the converted output in `storage/app/public/music/converted`, but it does not keep the original computer/cloud source file after conversion.

The dashboard accepts computer and cloud source videos up to 500 MB. If the browser shows `413 Request Entity Too Large`, the request was rejected before conversion started.

For Laragon, check the active PHP config:

```powershell
php --ini
```

Then make sure the loaded `php.ini` allows large posts:

```ini
upload_max_filesize = 2G
post_max_size = 2G
memory_limit = 512M
max_execution_time = 0
```

If you use Nginx, also set `client_max_body_size` high enough. Restart Laragon after changing server or PHP settings.

## 5) Why theme/branding looked different

Branding settings are stored in database (`system_settings`), and uploaded branding files are now stored as relative paths so they work across different laptop hosts/URLs.
