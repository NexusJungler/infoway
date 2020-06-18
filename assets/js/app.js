/** SCSS **/
import '../css/general/reset.scss';
import '../css/app.scss';
import "@fortawesome/fontawesome-free/css/all.css";

// css
require('../css/custom-style.css');
require('../css/class/managers/clockManager.css');
require('../css/debug.css');

// import JS
import '@fortawesome/fontawesome-free/js/all.js';


import {ClockManager} from "./class/Managers/ClockManager/ClockManager";
import {Tabmenu} from "./class/Tabmenu/Tabmenu";
import {Changeimage} from "./class/image/Changeimage";
import {Checkbox} from "./class/checkbox/checkbox";
import {Table} from "./class/Table/Table";
import {Navbar} from "./class/Navbar/Navbar";

import {Form} from "./class/Form/Form";
import ToolBox from "./class/Tools/ToolBox";
import popupConfirmation from "./popup/popup_confirmation";


const $ = require('jquery');
global.$ = global.jQuery = $;


//jqueryValidat
require('../js/jqueryValidate/jquery.validate.js');

require('../js/tags.js');

let popupconfirmation = new popupConfirmation();
popupconfirmation.enable();

// Tabs Menu
let tab_menu = new Tabmenu();
tab_menu.tabmenu();

let change_image= new Changeimage();
change_image.changeimage();

let table_products= new Table();
table_products.table();

let nav_bar= new Navbar();
nav_bar.navbarleft();

let form = new Form();
form.FormValidate();

// let chech_box = new Checkbox();
// chech_box.chech();

const clock= new ClockManager();
clock.enable();

const toolBox = new ToolBox();
toolBox.activeTool("CustomerCreatorHandler");

$(".enseigne select#enseigne").on("change", e => {

       const selectedCustomer = $(e.currentTarget).val();
   
       if(selectedCustomer !== "")
       {
           $.ajax({
               url: "/update/user/current/customer",
               type: "POST",
               data: { 'customer': selectedCustomer },
           })
       }
   
   });

