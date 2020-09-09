/*
** TailwindCSS Configuration File
**
** Docs: https://tailwindcss.com/docs/configuration
** Default: https://github.com/tailwindcss/tailwindcss/blob/master/stubs/defaultConfig.stub.js
*/
module.exports = {
  purge: [
    './src/components/**/*.vue',
    './includes/**/*.php',
  ],
  future: {
    removeDeprecatedGapUtilities: true,
  },
  theme: {
    fontFamily: {
      'sans': ["Inter var","BlinkMacSystemFont","Segoe UI","Roboto","Helvetica Neue","Arial","Noto Sans","sans-serif","Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji"],
    },
    extend: {
      colors: {
        'primary': '#0a192f',
        'secondary': '#0d1e36',
      },
    },
  },
  variants: {
    
  },
  plugins: []
}
