const mix = require("laravel-mix");
require("laravel-mix-purgecss");
const path = require('path');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .js("resources/js/app.js", "public/js")
	.vue({version: 2, extractStyles: false})
	.postCss('resources/css/app.css', 'public/css', [
		require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
	])
	.extract();

if (mix.inProduction()) {
	mix.version();
}