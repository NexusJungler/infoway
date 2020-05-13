import "../css/critiers.scss";
// import "../css/settings/critiers.scss";

const $ = require('jquery');
global.$ = global.jQuery = $;

// checked input modification
$(".tab-content-criteres .modified-criterion").click(function(){

    $.each($(".row-criterion input[type='checkbox']:checked"), function(){
           var id_criterion = $(this).attr('data-criterion');
           
           window.location.href= "/criterions/"+ id_criterion +"/edit"
           console.log(id_criterion);
    })
})


let i = 2;
$(".form-content .btn-add .btn-add-critiere").click(function(){
       i++;
       var html = "";
       html += "<div class='row-criteres' id='row"+i+"'> <div class='selected'> ";
       html += "<label for='criterion_list_criterions_"+i+"_selected'> choix n°"+i+" </label><label class='container-input'><input type='checkbox' id='criterion_list_criterions_"+i+"_selected' name='criterion_list[criterions]['+i+'][selected]' class='checkbox-custome' value="+i+"><span class='container-rdo'></span></label></div> ";
       html += "<div><input type='text' id='criterion_list_criterions_"+i+"_name' name='criterion_list[criterions]["+i+"][name]' class='input-custome'></div>";
       html += "<div><textarea type='text' id='criterion_list_criterions_"+i+"_description' name='criterion_list[criterions]["+i+"][description]' class='input-custome-desc'></textarea></div>";
       html += "<div><button type='button' id='row"+i+"' class='delete-row btn'>X</button></div></div>";
       
       $('.content-criteres-bloc').append(html);
       
});


//delete 
$(".content-criteres-bloc").on("click", ".delete-row", function(){
    var button_id = $(this).attr("id");
    console.log(button_id);
    $('#'+button_id+'').remove();   
});


$(".row-criterion .chkbox-critere").change( function(){
    
    var nb_input = $(".row-criterion input[type='checkbox']:checked").length;
    $(".modified-criterion").removeClass("hide-btn");

    if( nb_input > 1 || nb_input < 1 ){
           
           $(".modified-criterion").prop('disabled', true)
           $(".modified-criterion").addClass("hide-btn");
    }else{
           $(".modified-criterion").prop('disabled', false)
           $(".modified-criterion").removeClass("hide-btn");
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
