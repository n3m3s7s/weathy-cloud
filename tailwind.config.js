const defaultTheme = require("tailwindcss/defaultTheme");

module.exports = {
  purge: [
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./storage/framework/views/*.php",
    "./resources/views/**/*.blade.php",
    "./resources/js/components/**/*.vue"
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ["Ubuntu", "ui-sans-serif", "system-ui"],
        serif: ["Ubuntu", "ui-serif", "Georgia"],
        mono: ["Ubuntu", "ui-monospace", "SFMono-Regular"]
      }
    }
  },

  variants: {
    extend: {
      opacity: ["disabled"]
    }
  },

  plugins: [require("@tailwindcss/forms")]
};
