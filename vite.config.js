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
                'resources/css/colourblink.scss',
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
                'resources/js/resources.js',
                'resources/js/vitalSignsMasks.js',
                'resources/js/users.js',
                'resources/js/deliveryNoteMasks.js',
                'resources/js/patientReports.js',
                'resources/js/medReports.js',
                'resources/js/investigationReports.js',
                'resources/js/pharmacyReports.js',
                'resources/js/hospitalAndOthersReports.js',
                'resources/js/resourceReports.js',
                'resources/js/accountReports.js',
                'resources/js/usersReports.js',
                'resources/js/thirdPartyServices.js',
                'resources/js/patientForm.js',
                'resources/js/walkIns.js',
                'resources/js/mortuaryService.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost'
        }
    },
    resolve: {
        alias: {
          //'@': fileURLToPath(new URL('./shms', import.meta.url))
        }
      }
});
