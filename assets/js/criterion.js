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


$(".form-content .btn-add .btn-add-critiere").click(function(){
    $(".content-criteres-bloc").animate({scrollTop:100}, 'slow');
});


//delete 
$(".content-criteres-bloc").on("click", ".delete-row", function(){
    var button_id = $(this).attr("id");

    $('#criterions_list_criterions_'+button_id+'').parents('li').remove();   
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

})


$('.checkbox-criterion').change( e => {
    uniqueChoice();
})

$('#criterions_list_multiple').change( e => {
    uniqueChoice();
})

function uniqueChoice(){
    let selectChoix = $('#criterions_list_multiple').children("option:selected").val();

    if(selectChoix == 0){
        let nb_input = $(".checkbox-criterion:checked").length;
        if( nb_input === 1 ){
            $(".btn-create-critions").prop('disabled', false);
        }else {
            $(".btn-create-critions").prop('disabled', true);
            $(".btn-create-critions").addClass("hide-btn");
        }
        
    }else{
        console.log("multiple");
    }
}