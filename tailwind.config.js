import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    light: '#F3E8FF',
                    DEFAULT: '#A855F7',
                    dark: '#7E22CE',
                },
                secondary: {
                    light: '#FCE7F3',
                    DEFAULT: '#EC4899',
                },
                neutral: {
                    text: '#374151',
                    muted: '#9CA3AF',
                    bg: '#F9FAFB',
                },
                info: {
                    light: '#DBEAFE',
                    DEFAULT: '#2563EB',
                },
                warning: {
                    light: '#FEF3C7',
                    DEFAULT: '#D97706',
                },
            },
        },
    },

    plugins: [forms],
};
