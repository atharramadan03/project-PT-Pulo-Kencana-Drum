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
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                pkdi: {
                    primary: '#2563EB',
                    'primary-hover': '#1D4ED8',
                    secondary: '#3B82F6',
                    accent: '#F59E0B',
                    navy: '#0F172A',
                    canvas: '#F7F9FC',
                    surface: '#FFFFFF',
                    'text-main': '#0F172A',
                    'text-sub': '#64748B',
                    border: '#E2E8F0',
                },
            },
        },
    },

    plugins: [forms],
};
