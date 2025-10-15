# Using Node.js, npm, and npx in Docker

Your Laravel Docker container now includes Node.js 20.x LTS, npm, and npx for running Vite, Tailwind CSS, and other JavaScript tools.

## Rebuild Your Container

After updating the Dockerfile, you need to rebuild your Docker container to get Node.js installed:

```bash
# Stop and remove existing containers
docker-compose down

# Rebuild the app container with the new Dockerfile
docker-compose build --no-cache app

# Start the containers
docker-compose up -d
```

**Note:** The `--no-cache` flag ensures Docker rebuilds from scratch and doesn't use cached layers.

## Verify Installation

Check that Node.js, npm, and npx are installed:

```bash
# Check Node.js version (should show v20.x.x)
docker exec -it laravel12_app node --version

# Check npm version
docker exec -it laravel12_app npm --version

# Check npx version
docker exec -it laravel12_app npx --version
```

## Running npm Commands

You have two options for running npm commands:

### Option 1: Execute Commands from Host (Recommended)

Run npm commands inside the container from your host machine:

```bash
# Install dependencies
docker exec -it laravel12_app npm install

# Install Tailwind CSS
docker exec -it laravel12_app npm install -D tailwindcss postcss autoprefixer

# Initialize Tailwind
docker exec -it laravel12_app npx tailwindcss init -p

# Run Vite dev server
docker exec -it laravel12_app npm run dev

# Build for production
docker exec -it laravel12_app npm run build
```

### Option 2: Execute Commands Inside Container

Enter the container's shell and run commands directly:

```bash
# Enter the container
docker exec -it laravel12_app bash

# Now you're inside the container, run commands normally:
npm install
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
npm run dev

# Exit the container
exit
```

## Setting Up Tailwind CSS in Docker

Follow these steps to set up Tailwind CSS:

### Step 1: Create package.json

Create `package.json` in your project root (on your host machine):

```json
{
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  },
  "devDependencies": {
    "laravel-vite-plugin": "^1.0",
    "vite": "^5.0"
  }
}
```

### Step 2: Install Dependencies Inside Container

```bash
docker exec -it laravel12_app npm install
docker exec -it laravel12_app npm install -D tailwindcss postcss autoprefixer
docker exec -it laravel12_app npx tailwindcss init -p
```

### Step 3: Run Vite Dev Server

```bash
docker exec -it laravel12_app npm run dev
```

Or if you need to run it in the background:

```bash
docker exec -d laravel12_app npm run dev
```

**Note:** Keep this running while developing. Your changes to CSS/JS will be automatically compiled.

## Exposing Vite Dev Server Port (Optional)

If you want to access Vite's dev server directly (for HMR - Hot Module Replacement), you can expose port 5173:

Update `docker-compose.yml`:

```yaml
services:
  app:
    # ... existing configuration ...
    ports:
      - "8000:80"
      - "5173:5173" # Add this line for Vite dev server
```

Then update `vite.config.js`:

```javascript
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true,
    }),
  ],
  server: {
    host: "0.0.0.0",
    port: 5173,
    hmr: {
      host: "localhost",
    },
  },
});
```

After making these changes, rebuild and restart:

```bash
docker-compose down
docker-compose up -d
docker exec -it laravel12_app npm run dev
```

## Troubleshooting

### Permission Issues

If you encounter permission errors with npm:

```bash
# Fix permissions for node_modules and package-lock.json
docker exec -it laravel12_app chown -R www-data:www-data /var/www/html/node_modules
docker exec -it laravel12_app chown www-data:www-data /var/www/html/package-lock.json
```

### npm install is slow

The first `npm install` might be slow because it downloads all packages. Subsequent installs will be faster.

### Changes not reflecting

Make sure:

1. `npm run dev` is running
2. Your Laravel views include `@vite(['resources/css/app.css', 'resources/js/app.js'])`
3. Clear your browser cache

### Container doesn't have Node.js

If Node.js is not found after rebuilding:

```bash
# Rebuild without cache
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## Quick Reference Commands

```bash
# Rebuild container after Dockerfile changes
docker-compose build --no-cache app

# Start containers
docker-compose up -d

# View logs
docker-compose logs -f app

# Stop containers
docker-compose down

# Enter container shell
docker exec -it laravel12_app bash

# Run npm commands from host
docker exec -it laravel12_app npm <command>

# Check Node.js version
docker exec -it laravel12_app node --version
```

## Workflow Example

Here's a typical workflow for working with Laravel + Tailwind in Docker:

```bash
# 1. Start your containers
docker-compose up -d

# 2. Install npm dependencies (first time only)
docker exec -it laravel12_app npm install

# 3. Install Tailwind (first time only)
docker exec -it laravel12_app npm install -D tailwindcss postcss autoprefixer
docker exec -it laravel12_app npx tailwindcss init -p

# 4. Start Vite dev server (keep running while developing)
docker exec -it laravel12_app npm run dev

# 5. Visit your app at http://localhost:8000

# 6. When done, stop the dev server (Ctrl+C) and containers
docker-compose down
```

## For Production

Before deploying to production, build your assets:

```bash
# Build optimized production assets
docker exec -it laravel12_app npm run build
```

This creates optimized CSS/JS files in the `public/build` directory.

---

**Remember:** All your project files are mounted as a volume, so changes you make on your host machine are immediately reflected in the container, and vice versa!
