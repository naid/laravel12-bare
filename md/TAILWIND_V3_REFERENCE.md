# Tailwind CSS v3 Quick Reference

This project uses **Tailwind CSS v3** with Laravel and Vite.

## ✅ Current Configuration

### Files Setup

**`resources/css/app.css`**

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

**`tailwind.config.js`**

```javascript
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
```

**`postcss.config.js`**

```javascript
export default {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
};
```

**`vite.config.js`**

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
    strictPort: true,
    hmr: {
      host: "localhost",
    },
    watch: {
      usePolling: true,
    },
  },
});
```

## 🚀 Usage

### Building Assets

**For production:**

```bash
docker exec laravel12_app npm run build
```

**For development (with hot reload):**

```bash
docker exec laravel12_app npm run dev
# Keep this running while developing
```

### Using Tailwind Classes

Just add utility classes to your HTML/Blade templates:

```blade
<div class="bg-blue-500 text-white p-4 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold">Hello World!</h1>
    <p class="mt-2">Styled with Tailwind CSS v3</p>
</div>
```

## 📚 Commonly Used Classes

### Layout

- `container` - Responsive container
- `flex` - Flexbox container
- `grid` - Grid container
- `block`, `inline-block`, `inline` - Display types

### Spacing

- `p-4` - Padding (all sides)
- `px-4` - Padding horizontal
- `py-4` - Padding vertical
- `m-4` - Margin (all sides)
- `mt-4`, `mb-4` - Margin top/bottom
- `gap-4` - Gap in flex/grid

### Colors

- `bg-blue-500` - Background color
- `text-white` - Text color
- `border-gray-300` - Border color

### Typography

- `text-sm`, `text-base`, `text-lg`, `text-xl`, `text-2xl` - Font sizes
- `font-bold`, `font-semibold` - Font weights
- `text-center`, `text-left` - Text alignment

### Effects

- `shadow-md`, `shadow-lg` - Box shadows
- `rounded-lg` - Border radius
- `hover:bg-blue-600` - Hover states
- `transition` - Smooth transitions

### Responsive Design

- `sm:` - Small screens (640px+)
- `md:` - Medium screens (768px+)
- `lg:` - Large screens (1024px+)
- `xl:` - Extra large screens (1280px+)

Example:

```blade
<div class="w-full md:w-1/2 lg:w-1/3">
    <!-- Full width on mobile, half on tablet, third on desktop -->
</div>
```

## 🎨 Example Components

### Button

```blade
<button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition">
    Click Me
</button>
```

### Card

```blade
<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-xl font-bold mb-2">Card Title</h3>
    <p class="text-gray-600">Card content goes here.</p>
</div>
```

### Alert

```blade
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
    <p class="font-bold">Success!</p>
    <p>Your action was completed.</p>
</div>
```

### Form Input

```blade
<input
    type="text"
    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
    placeholder="Enter text..."
>
```

### Navigation Link

```blade
<a href="#" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition">
    Menu Item
</a>
```

## 🔄 Workflow

### When You Make Changes:

1. **Edit your Blade file** (add/change classes)
2. **Rebuild CSS**:
   ```bash
   docker exec laravel12_app npm run build
   ```
3. **Refresh browser** (hard refresh: Cmd+Shift+R)

### For Faster Development:

Use the dev server:

```bash
docker exec laravel12_app npm run dev
```

Then changes auto-reload! (See VITE_GUIDE.md)

## 📖 Learning Resources

- **Official Docs**: https://v3.tailwindcss.com/
- **Cheat Sheet**: https://nerdcave.com/tailwind-cheat-sheet
- **Play CDN** (for testing): https://play.tailwindcss.com/

## 🆘 If Styles Don't Work

See [TROUBLESHOOTING_TAILWIND.md](TROUBLESHOOTING_TAILWIND.md) for the complete troubleshooting guide.

**Quick checks:**

1. Run `docker exec laravel12_app npm run build`
2. Hard refresh browser (Cmd+Shift+R)
3. Check that CSS file loads in Network tab
4. Verify `postcss.config.js` and `tailwind.config.js` exist

---

**Tailwind CSS v3 is now working perfectly in your Laravel project!** 🎉
