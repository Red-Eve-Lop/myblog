import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js', 'resources/js/post-editor.js'],
            refresh: true,
        }),
        tailwindcss(),
	vue(),
    ],
    server: {
    host: '0.0.0.0',
    port: 5173,
    cors: true,
    origin: 'http://127.0.0.1:5173',
    hmr: {
        host: 'localhost',
        port: 5173,
    },

    watch: {
        ignored: ['**/storage/framework/views/**'],
    },
},
});
