/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  safelist: [
    'grid-cols-3',
    'xl:grid-cols-3',
    'xl:grid-cols-2',
    // Add more classes as needed
  ],
  theme: {
    extend: {
      colors: {
        primary: '#0F3D5C',
        secondary: '#E76727',
        accent: '#57575B',
        light: '#D9D9D9',
        main: '#081a27',
      },
      backgroundImage: {
        'gradient-primary': 'linear-gradient(240deg, rgba(15,61,92,1) 38%, rgba(87,87,91,1) 100%)',
        'gradient-guest': 'radial-gradient(circle, rgba(231,103,39,1) 0%, rgba(15,61,92,1) 100%)',
      },
    },
  },
  plugins: [],
  darkMode: 'media', 
}

