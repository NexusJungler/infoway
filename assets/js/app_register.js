require('../css/app_register.css');

const $ = require('jquery');
global.$ = global.jQuery = $;


$("form div:has(select)").css( { 'display': 'flex', 'flex-direction': 'column' } );
