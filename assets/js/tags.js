// import style css
import "../css/tags.scss";
import "../css/tags/create_tags.scss";
import "../css/tags/list_tags.scss";
import "../css/tags/edit_tags.scss";

// jquery 
const $ = require('jquery');
global.$ = global.jQuery = $;


$(".tags .modified-tag").click(function(){

    $.each($(".tags-poster input[type='checkbox']:checked"), function(){
        var id_tag = $(this).attr('data-tag');
        
        window.location.href= "/tags/"+ id_tag +"/edit"
        console.log(id_tag);
    })
})

$(".content-tags .tags-poster .chkbox-tag").change( function(){
    let nb_input = $(".tags-poster input[type='checkbox']:checked").length;
    $(".modified-tag").removeClass("hide-btn");

    if( nb_input > 1 || nb_input < 1 ){
        $(".modified-tag").prop('disabled', true)
        $(".modified-tag").addClass("hide-btn");
    }else{
        $(".modified-tag").prop('disabled', false)
        $(".modified-tag").removeClass("hide-btn");
    }
})

//delete 

$(".delete-tag").on('click', function() {
    $("#form_tags_action").submit();
   
})


$(".tags-color").on('change', function(e){
    $(this).parents('ul.tags_list').find( '.color_input' ).css("background-color",this.value);
})


//page edit tags
var val = $('.tags-color-edit').attr("value");
$('.input-color-edit').css("background-color", val);

$(".tags-color-edit").on('change', function(e){
    $('.input-color-edit').css("background-color",this.value);
})

$('.btn-site').click(function () {
    $('.add-popup-site').addClass('is-open');
    return false;
  });

$('.btn-popupclose2').click(function () {
    $('.add-popup-site').removeClass('is-open');
});



//Search Filterable table
$("#site-search").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".tbody-serach tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});

let addTagBtn = $('#add_tag')

addTagBtn.on('click', e =>{
    
    let list = $('.tags_list')

    let counter = list.children().length;

    let newWidget = list.attr('data-prototype');

    newWidget = newWidget.replace(/__name__/g, counter);
    counter++;
    list.data('widget-counter', counter);
    
    let newElem = $(list.attr('data-widget-tags')).html(newWidget);
    
    newElem.appendTo(list);

})




