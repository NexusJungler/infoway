// sass
import '../css/general/reset.scss'
import '../css/app.scss';

// css
require('../css/app.css');
require('../css/custom-style.css');
require('../css/class/managers/clockManager.css');


// impoet font awesome
// require('../css/fontawesome/css/all.css');

// import JS
import {ClockManager} from "./class/Managers/ClockManager/ClockManager";
import {Tabmenu} from "./class/Tabmenu/Tabmenu";
import {Changeimage} from "./class/image/Changeimage";
import {Checkbox} from "./class/checkbox/checkbox";
import {Table} from "./class/Table/Table";
import {Navbar} from "./class/Navbar/Navbar";

// require('../js/fontawesome/js/all')


const $ = require('jquery');
global.$ = global.jQuery = $;

// Tabs Menu
let tab_menu = new Tabmenu();
tab_menu.tabmenu();

let change_image= new Changeimage();
change_image.changeimage();

let table_products= new Table();
table_products.table();



let nav_bar= new Navbar();
nav_bar.navbarleft();

// let chech_box = new Checkbox();
// chech_box.chech();


const clock= new ClockManager();
clock.enable();