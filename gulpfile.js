var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir.config.assetsPath = './themes/default/';
elixir.config.publicPath = './themes/default/';

elixir(function(mix) {
    mix.sass('style.scss')
    	.sass('other.scss');
});
