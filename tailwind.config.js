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
                sans: ['Arial', 'sans-serif'],
            },

            colors: {
                transparent: 'transparent',
                current: 'currentColor',
                white: '#FFFFFF',
                black: '#000000',

                bg: {
                    'base-default': '#FFFFFF',
                    'base-hover': '#F5F5F5',
                    'base-secondary': '#F5F5F5',
                    'base-secondary-hover': '#E6E6E6',
                    'base-tertiary': '#D9D9D9',
                    'base-tertiary-hover': '#B3B3B3',

                    'brand-default': '#2C2C2C',
                    'brand-hover': '#1E1E1E',
                    'brand-secondary': '#E6E6E6',
                    'brand-secondary-hover': '#D9D9D9',
                    'brand-tertiary': '#F5F5F5',
                    'brand-tertiary-hover': '#E6E6E6',

                    'neutral-default': '#5A5A5A',
                    'neutral-hover': '#434343',
                    'neutral-secondary': '#CDCDCD',
                    'neutral-secondary-hover': '#B2B2B2',
                    'neutral-tertiary': '#E3E3E3',
                    'neutral-tertiary-hover': '#CDCDCD',
                },

                txt: {
                    'base-default': '#1E1E1E',
                    'base-secondary': '#757575',
                    'base-tertiary': '#B3B3B3',

                    'brand-default': '#2C2C2C',
                    'brand-secondary': '#444444',
                    'brand-tertiary': '#757575',
                    'brand-on': '#F5F5F5',
                    'brand-on-secondary': '#1E1E1E',
                    'brand-on-tertiary': '#2C2C2C',

                    'neutral-default': '#303030',
                    'neutral-secondary': '#5A5A5A',
                    'neutral-tertiary': '#767676',
                    'neutral-on': '#F3F3F3',
                    'neutral-on-secondary': '#303030',
                    'neutral-on-tertiary': '#434343',
                },

                bor: {
                    'base-default': '#D9D9D9',
                    'base-secondary': '#757575',
                    'base-tertiary': '#383838',

                    'brand-default': '#2C2C2C',
                    'brand-secondary': '#444444',
                    'brand-tertiary': '#757575',

                    'neutral-default': '#303030',
                    'neutral-secondary': '#767676',
                    'neutral-tertiary': '#B2B2B2',
                },

                acc: {
                    'base-default': '#19A7CE',
                    'base-secondary': '#146C94',
                    'base-tertiary': '#',
                }
            },
        },
    },

    plugins: [forms],
};
