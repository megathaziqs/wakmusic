# 🚀 WakMusic - Windows Quick Start

**If `composer run dev` fails on Windows**, use this simpler method:

## ⚡ Option 1: Recommended (Simple & Fast)

### Terminal 1: Start Laravel
```powershell
cd c:\laragon\www\wakmusic
php artisan serve
```

### Terminal 2: Start Vite (in another terminal)
```powershell
cd c:\laragon\www\wakmusic
npm run dev
```

Then visit: **http://localhost:8000** 🎵

---

## ⚙️ Option 2: Use Fixed composer run dev

If you updated `composer.json`, now you can use:
```powershell
composer run dev
```

This starts both Laravel & Vite together!

---

## 🆘 If Still Having Issues

### Issue: Port 8000 already in use
```powershell
# Start Laravel on different port
php artisan serve --port=8001
```

### Issue: npm run dev fails
```powershell
# Install/reinstall dependencies
npm install

# Then try again
npm run dev
```

### Issue: vendor folder missing
```powershell
composer install
npm install
php artisan migrate
```

---

## ✅ When It Works

You should see:
```
[server]
   INFO  Server running on [http://127.0.0.1:8000].
   Press Ctrl+C to stop the server

[vite]
   VITE v7.0.0  ready in 234 ms
   ➜  local:   http://localhost:5173/
```

Visit: **http://localhost:8000** in your browser

---

## 🎵 Ready to Go!

Your WakMusic app is now running locally. Explore the 3 demo pages and test all features!

**Documentation**: Check README.md or START_HERE.md
