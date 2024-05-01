/** @type {import('tailwindcss').Config} */
export default {
  content: [
    // You will probably also need these lines
    './resources/**/**/*.blade.php',
    './resources/**/**/*.js',
    './app/View/Components/**/**/*.php',
    './app/Livewire/**/**/*.php',

    // Add mary
    './vendor/robsontenorio/mary/src/View/Components/**/*.php',
  ],
  theme: {
    extend: {
      keyframes: {
        popout: {
          '0%': { transform: 'scale(0)' },
          '50%': { transform: 'scale(1.2)' },
          '100%': { transform: 'scale(1)' },
        },
      },
      animation: {
        popout: 'popout .2s ease-in-out',
      },
    },
  },

  // Add daisyUI
  plugins: [require('daisyui')],
};
