import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/css/login.scss',
                'resources/css/header.scss',
                'resources/css/home.scss',
                'resources/js/app.js',
                'resources/js/patients.js',
                'resources/js/modals/patientModal.js',
                'resources/js/modals/sponsorModal.js',
                'resources/js/doctors.js',
                'resources/js/nurses.js',
                'resources/js/investigations.js',
                'resources/js/hmo.js',
                'resources/js/pharmacy.js',
                'resources/js/billing.js',
                'resources/js/adminSettings.js',
                'resources/js/visits.js',
                'resources/js/resources.js',
                'resources/js/vitalSignsMasksjs',
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
