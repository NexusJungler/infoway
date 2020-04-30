/** SCSS **/
import '../css/general/reset.scss';
import '../css/app.scss';


// css
// require('../css/app.css');
require('../css/custom-style.css');
require('../css/class/managers/clockManager.css');

// impoet font awesome
//require('../css/fontawesome/css/all.css');

// import JS
import {ClockManager} from "./class/Managers/ClockManager/ClockManager";
import {Tabmenu} from "./class/Tabmenu/Tabmenu";
import {Changeimage} from "./class/image/Changeimage";
import {Checkbox} from "./class/checkbox/checkbox";
import {Table} from "./class/Table/Table";
import {Navbar} from "./class/Navbar/Navbar";

import {Form} from "./class/Form/Form";
import ToolBox from "./class/Tools/ToolBox/ToolBox";

//require('../js/fontawesome/js/all')

const $ = require('jquery');
global.$ = global.jQuery = $;

//jqueryValidate
require('../js/jqueryValidate/jquery.validate.js');

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
toolBox.activeTool("CustomerCreatorHandler")
       .activeTool("UploadHandlerTool")
;



let i = 2;
$(".form-content .btn-add .btn-add-critiere").click(function(){
       i++;
       var html = "";
       html += "<div class='row-criteres' id='row"+i+"'> <div class='selected'> ";
       html += "<label for='criterion_list_criterions_"+i+"_selected'> choix n°"+i+" </label><input type='checkbox' id='criterion_list_criterions_"+i+"_selected' name='criterion_list[criterions]['+i+'][selected]' class='checkbox-custome' value="+i+"></div> ";
       html += "<div><label for='criterion_list_criterions_"+i+"_name'> </label><input type='text' id='criterion_list_criterions_"+i+"_name' name='criterion_list[criterions]["+i+"][name]' class='input-custome'></div>";
       html += "<div><label for='criterion_list_criterions_"+i+"_description'>Description</label><input type='text' id='criterion_list_criterions_"+i+"_description' name='criterion_list[criterions]["+i+"][description]' class='input-custome input-custome-desc'></div>";
       html += "<div><button type='button' id='"+i+"' class='delete-row btn'>X</button></div></div>";
       
       $('.content-criteres-bloc').append(html);
       
});

$(".content-criteres .delete-row").click(function(){
       var button_id = $(this).attr("id");
       console.log(button_id);
       $('#row'+button_id+'').remove();   
});



// $(".delete-row").click(function(){
//        var count_input = $(".content-criteres-bloc").find('input[type="checkbox"]').length;
//        var checked_count =  $('[type="checkbox"]:checked').length;

//        if(count_input != checked_count){
//               console.log("2");
//               $(".content-criteres-bloc").find('input[type="checkbox"]').each(function(){
//                      if($(this).is(":checked"))
//                 {
//                     $(this).parents(".content-criteres-bloc row-criteres").remove();
//                 }
//               });
//        }else{
//               return false
//        }
// })

