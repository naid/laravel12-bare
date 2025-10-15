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
    host: "0.0.0.0", // Listen on all interfaces (required for Docker)
    port: 5173,
    strictPort: true,
    hmr: {
      host: "localhost", // HMR (Hot Module Replacement) connects via localhost
    },
    watch: {
      usePolling: true, // Required for file watching in Docker
    },
  },
});
