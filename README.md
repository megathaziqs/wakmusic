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

## 4) Why theme/branding looked different

Branding settings are stored in database (`system_settings`), and uploaded branding files are now stored as relative paths so they work across different laptop hosts/URLs.
