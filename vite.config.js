import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/css/app.css',
                'resources/css/login.scss',
                'resources/css/header.scss',
                'resources/css/home.scss',
                'resources/js/app.js',
                'resources/js/patients.js',
                'resources/js/doctors.js',
                'resources/js/nurses.js',
                'resources/js/hmo.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
          //'@': fileURLToPath(new URL('./shms', import.meta.url))
        }
      }
});
