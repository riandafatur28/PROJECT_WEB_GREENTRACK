import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    plugins: [vue()],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },
    build: {
        outDir: "public/build",
    },
    server: {
        proxy: {
            "/app": "http://localhost",
        },
    },
});
