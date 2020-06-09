import "../css/settings/criterion/create_criterion.scss";
import "../css/settings/criterion/list_criterion.scss";
import "../css/settings/criterion/edit_criterion.scss";

import createCriterion from "./settings/criterion/createCriterion.js";

// checked input modification
$(".tab-content-criteres .modified-criterion").click(function(){

    $.each($(".row-criterion input[type='checkbox']:checked"), function(){
           var id_criterion = $(this).attr('data-criterion');
           
           window.location.href= "/criterions/"+ id_criterion +"/edit"
           console.log(id_criterion);
    })
})

//delete 
$(".content-criteres-bloc").on("click", ".delete-row", function(){
    var button_id = $(this).attr("id");

    $('#criterions_list_criterions_'+button_id+'_selected').parents('li').remove();   
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


let addTagBtn = $('#add_criterion')

addTagBtn.on('click', e =>{
    
    let list = $('.criterion_list')
    
    let counter = list.children().length;

    let newWidget = list.attr('data-prototype');
    newWidget = newWidget.replace(/__name__/g, counter);
    newWidget = "<div>" + newWidget + "<button type='button' id='"+ counter +"' class='btn delete-row' >x</button></div>"

    counter++;
    list.data('widget-counter', counter);
    
    let newElem = $(list.attr('data-widget-tags')).html(newWidget);

    newElem.appendTo(list);
    $(`#criterions_list_criterions_${counter-1} label[for='criterions_list_criterions_${counter-1}_selected']`).html("Choix n°" + counter );
})

$("#add_criterion").on("click", function() {
    console.log("1");
    $(".content-criteres-bloc .criterion_list ").animate({
        
        scrollTop: $('.content-criteres-bloc .criterion_list ').get(0).scrollHeight 
      }, 50); 
});


// $('.checkbox-criterion').change( e => {
//     uniqueChoice();
// })

// $('#criterions_list_multiple').change( e => {
//     uniqueChoice();
// })

// function uniqueChoice(){
//     let selectChoix = $('#criterions_list_multiple').children("option:selected").val();

//     if(selectChoix == 0){
//         console.log("unique");
//         let nb_input = $(".checkbox-criterion:checked").length;
//         if( nb_input === 1 ){
//             $(".btn-create-critions").prop('disabled', false);
//         }else {
//             $(".btn-create-critions").prop('disabled', true);
//             $(".btn-create-critions").addClass("hide-btn");
//         }
        
//     }else{
//         console.log("multiple");
//     }
// }
// $("#criterions_list_basicCriterionUsed_0").prop("required", false);

// $("#criterions_list_basicCriterionUsed input[type=radio]").on('click', function() {
  
//     if(this.previous){
//         this.checked = false
//         $(".row-define-criterion").removeClass("active");
//         $("#criterions_list_basicCriterionUsed_1").val($(this).is(':checked'));
//     }
    
//     this.previous = this.checked; 

//     // $("#criterions_list_basicCriterionUsed input[type=radio]").prop("checked", false);
// });

let createcriterion = new createCriterion();
createcriterion.enable();