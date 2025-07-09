/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.ts", 
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Custom color scheme
        primary: {
          DEFAULT: '#5E8A57', // Deep green - Action elements
          foreground: '#ffffff',
        },
        secondary: {
          DEFAULT: '#F1EAD7', // Warm beige - Background/contrast
          foreground: '#212121',
        },
        accent: {
          DEFAULT: '#FFCA28', // Gold yellow - Highlights/warnings
          foreground: '#212121',
          2: '#90A4AE', // Cool blue-gray - Subtle UI elements
        },
        muted: {
          DEFAULT: '#90A4AE', // Cool blue-gray
          foreground: '#616161',
        },
        // Text colors
        text: {
          primary: '#212121', // Dark gray - Main text
          secondary: '#616161', // Medium gray - Secondary text
        },
        // Map/route colors
        map: {
          overlay: '#E8F5E9', // Light green transparent
        },
        // Dark mode colors
        dark: {
          background: '#1E1E1E', // Dark gray
          panel: '#2C2C2C', // Anthracite
          text: '#F5F5F5', // Light gray
          accent: '#FFB300', // Dark gold yellow
        },
        // Semantic colors
        success: '#5E8A57',
        warning: '#FFCA28',
        error: '#dc2626',
        info: '#90A4AE',
      },
    },
  },
  plugins: [],
} 