/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
global.moment = require('moment');

// require('bootstrap');
require('../theme/vendors/js/vendor.bundle.base.js');
require('../theme/js/off-canvas.js');
require('../theme/js/hoverable-collapse.js');
require('../theme/js/misc.js');
require( 'datatables.net-bs4' );
require('@ckeditor/ckeditor5-build-classic');
require('./datetime');
require('./main');



console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
