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

require('../js/tags.js');


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
       html += "<label for='criterion_list_criterions_"+i+"_selected'> choix n°"+i+" </label><label class='container-input'><input type='checkbox' id='criterion_list_criterions_"+i+"_selected' name='criterion_list[criterions]['+i+'][selected]' class='checkbox-custome' value="+i+"><span class='container-rdo'></span></label></div> ";
       html += "<div><input type='text' id='criterion_list_criterions_"+i+"_name' name='criterion_list[criterions]["+i+"][name]' class='input-custome'></div>";
       html += "<div><textarea type='text' id='criterion_list_criterions_"+i+"_description' name='criterion_list[criterions]["+i+"][description]' class='input-custome input-custome-desc'></textarea></div>";
       html += "<div><button type='button' id='row"+i+"' class='delete-row btn'>X</button></div></div>";
       
       $('.content-criteres-bloc').append(html);
       
});
$(".content-criteres-bloc").on("click", ".delete-row", function(){
       var button_id = $(this).attr("id");
       console.log(button_id);
       $('#'+button_id+'').remove();   
});


$(".tab-content-criteres .modified-criterion").click(function(){

       $.each($(".row-criterion input[type='checkbox']:checked"), function(){
              var id_criterion = $(this).attr('data-criterion');
              
              window.location.href= "/criterion/list/"+ id_criterion +"/edit"
              console.log(id_criterion);
       })
})

$(".row-criterion .chkbox-critere").change( function(){
    
       var nb_input = $(".row-criterion input[type='checkbox']:checked").length;

       if( nb_input > 1 || nb_input < 1 ){
              $(".modified-criterion").prop('disabled', true)
       }else{
              $(".modified-criterion").prop('disabled', false)
       }
})
     

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

