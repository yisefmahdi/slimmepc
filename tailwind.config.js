import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './public/assets/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                brand: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },
                primary: {
                    from: '#075be8',
                    to: '#064bd7',
                    DEFAULT: '#075be8',
                },
                accent: {
                    400: '#a3e635',
                    500: '#84cc16',
                    600: '#65a30d',
                },
                navy: '#071b46',
            },

            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
            },

            boxShadow: {
                navbar: '0 12px 35px rgba(15, 23, 42, 0.22)',
                menu: '0 20px 50px rgba(15, 23, 42, 0.20)',
                card: '0 25px 80px rgba(37,99,235,0.13)',
                'card-dark': '0 25px 80px rgba(0,0,0,0.45)',
                'btn-glow': '0 12px 25px rgba(0,91,234,0.25)',
                'btn-glow-hover': '0 16px 30px rgba(0,91,234,0.32)',
                soft: '0 15px 45px rgba(30, 64, 175, 0.08)',
                panel: '0 28px 75px rgba(30, 64, 175, 0.11)',
                button: '0 16px 38px rgba(37, 99, 235, 0.28)',
                image: '0 30px 55px rgba(30, 64, 175, 0.18)',
            },

            backgroundImage: {
                'brand-gradient': 'linear-gradient(to right, #075be8, #064bd7)',
            },

            keyframes: {
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                blob: {
                    '0%, 100%': { transform: 'translate(-50%, -50%) scale(1)' },
                    '50%': { transform: 'translate(-50%, -52%) scale(1.05)' },
                },
                'spin-slow': {
                    to: { transform: 'rotate(360deg)' },
                },
            },

            animation: {
                'fade-in-up': 'fade-in-up 0.5s ease-out both',
                blob: 'blob 8s ease-in-out infinite',
                'spin-slow': 'spin-slow 1.1s linear infinite',
            },
        },
    },

    plugins: [forms],
};

