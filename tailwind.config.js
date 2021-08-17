module.exports = {
  purge: {
    enabled: true,
    content: ['./dist/**/*.html'],
    },
  darkMode: 'class', // or 'media' or 'class'
  theme: {
    debugScreens: {
    position: ['top', 'left'],
    },
    extend: {
      fontFamily: {
        headline: ['Oswald']
        },
        colors: {
          mainColour: '#1e293b'
        }
    },
  },
  variants: {
    extend: {},
  },
  plugins: [
    require('tailwindcss-debug-screens'),
    ]
}
