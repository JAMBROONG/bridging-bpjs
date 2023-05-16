import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';
<<<<<<< HEAD
=======

>>>>>>> 3038bfcf046dad8ae0977ca0a7a7c42db2e75eef
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

<<<<<<< HEAD
    plugins: [forms,daisyui],
=======
    plugins: [daisyui],
>>>>>>> 3038bfcf046dad8ae0977ca0a7a7c42db2e75eef
};
