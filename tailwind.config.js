/*
** TailwindCSS Configuration File
**
** Docs: https://tailwindcss.com/docs/configuration
** Default: https://github.com/tailwindcss/tailwindcss/blob/master/stubs/defaultConfig.stub.js
*/
module.exports = {
  purge: [
    './src/components/**/*.vue',
    './robust-user-search.php',
  ],
  future: {
    removeDeprecatedGapUtilities: true,
  },
  theme: {
    extend: {
      colors: {
        'primary': '#0a192f',
        'secondary': '#0d1e36',
      }
    }
  },
  variants: {
    
  },
  plugins: []
}
