# Vite Dev Server Guide

Your Docker setup is now fully configured with Vite support! You can now use hot module replacement (HMR) for instant CSS/JS updates while developing.

## ✅ What's Configured

- ✅ Port 5173 exposed from Docker
- ✅ Vite config updated for Docker
- ✅ Hot Module Replacement (HMR) enabled
- ✅ File watching with polling

## 🚀 How to Use Vite

### Option 1: Dev Server with Hot Reload (Recommended for Development)

**Open a new terminal window** and run:

```bash
docker exec laravel12_app npm run dev
```

**Keep this terminal running while you develop!**

You'll see output like:
```
  VITE v5.4.20  ready in 135 ms

  ➜  Local:   http://localhost:5173/
  ➜  Network: use --host to expose

  LARAVEL v12.32.5  plugin v1.0.0

  ➜  APP_URL: http://localhost:8000
```

**Then:**
1. Visit http://localhost:8000 in your browser
2. Edit any CSS or Blade file
3. Save the file
4. **Automatically** see changes in browser! 🎉

**Benefits:**
- Instant updates when you save files
- No need to manually refresh
- See changes immediately

---

### Option 2: Production Build (For Deployment)

When you're done developing and want optimized files:

```bash
docker exec laravel12_app npm run build
```

This creates minified, production-ready assets in `public/build/`.

---

## 🔄 Workflow Example

### Starting Your Day:

```bash
# Terminal 1: Start Docker containers
docker-compose up -d

# Terminal 2: Start Vite dev server
docker exec laravel12_app npm run dev
# (Keep this running!)
```

### While Developing:

1. Edit files in your code editor
2. Save
3. See changes instantly in browser!

### Ending Your Day:

```bash
# Terminal 2: Stop Vite (Ctrl+C)

# Terminal 1: Stop Docker (optional)
docker-compose down
```

---

## 🎯 What Files to Edit

### CSS Changes:
Edit: `resources/css/app.css`
- Changes appear instantly with `npm run dev`

### JavaScript Changes:
Edit: `resources/js/app.js`
- Changes appear instantly with `npm run dev`

### Blade Template Changes:
Edit: Any `.blade.php` file
- Vite detects changes and auto-refreshes

---

## 🛠️ Troubleshooting

### Vite Shows "Network: use --host to expose"

This is normal! Vite is running inside Docker and exposing port 5173.
- Just visit http://localhost:8000 (not 5173)

### Changes Not Appearing?

1. **Hard refresh browser:** Cmd+Shift+R (Mac) or Ctrl+Shift+R (Windows)
2. **Check Vite is running:** You should see output in terminal
3. **Check file is saved:** Make sure you saved the file

### "Failed to load resource: net::ERR_CONNECTION_REFUSED"

This means Vite dev server is NOT running. Run:
```bash
docker exec laravel12_app npm run dev
```

### Port 5173 Already in Use?

Stop the existing Vite process:
1. Find the terminal running `npm run dev`
2. Press `Ctrl+C` to stop it
3. Run it again

---

## 📊 Ports Summary

| Port | Service | URL |
|------|---------|-----|
| 8000 | Laravel App | http://localhost:8000 |
| 5173 | Vite Dev Server | (Internal only) |
| 8080 | phpMyAdmin | http://localhost:8080 |
| 3306 | MySQL | localhost:3306 |

---

## 💡 Pro Tips

### 1. **Keep Vite Running**
Leave `npm run dev` running in a separate terminal while you work. Don't close it!

### 2. **Browser DevTools**
Open DevTools (F12) → Console to see HMR messages like:
```
[vite] connected.
[vite] hot updated: /resources/css/app.css
```

### 3. **Multiple Terminals**
Use 2 terminals:
- **Terminal 1**: Docker logs, Laravel commands
- **Terminal 2**: `npm run dev` (keep running)

### 4. **Production Builds**
Before deploying, always run:
```bash
docker exec laravel12_app npm run build
```

---

## 🔧 Configuration Files

### docker-compose.yml
```yaml
ports:
  - "8000:80"      # Laravel
  - "5173:5173"    # Vite
```

### vite.config.js
```javascript
server: {
  host: "0.0.0.0",        // Listen on all interfaces
  port: 5173,
  strictPort: true,
  hmr: {
    host: "localhost",    // HMR via localhost
  },
  watch: {
    usePolling: true,     // Required for Docker
  },
}
```

---

## 🎓 Understanding the Setup

### What is Vite?
A modern build tool that compiles your CSS and JavaScript extremely fast.

### What is HMR (Hot Module Replacement)?
When you save a file, Vite instantly updates your browser WITHOUT a full page reload. You see changes immediately!

### Why Port 5173?
- Vite dev server runs on port 5173 inside Docker
- Your browser connects to port 8000 (Laravel)
- Laravel's Vite plugin handles the connection to 5173 automatically

---

## 🚀 Quick Reference

```bash
# Start containers
docker-compose up -d

# Start Vite dev server (keep running!)
docker exec laravel12_app npm run dev

# Build for production
docker exec laravel12_app npm run build

# Stop Vite
# Press Ctrl+C in the terminal running npm run dev

# Stop containers
docker-compose down

# Restart after docker-compose.yml changes
docker-compose down && docker-compose up -d
```

---

**Happy coding with instant feedback! 🎉**

