import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                }
            },
            boxShadow: {
                'card': '0 2px 12px -2px rgba(0, 0, 0, 0.04), 0 4px 20px -2px rgba(0, 0, 0, 0.03)',
                'card-hover': '0 12px 32px -4px rgba(0, 0, 0, 0.08)',
            }
        },
    },

    plugins: [forms],
};

