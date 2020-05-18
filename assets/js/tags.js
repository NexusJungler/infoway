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
$(".content-criteres-bloc").on("click", ".delete-row", function(){
    var button_id = $(this).attr("id");
    console.log(button_id);
    $('#'+button_id+'').remove();   
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

$(".tags-color").on('change', function(){
    // $(".tags_list ul li div div:nth-child(2)").append( "<span class='bloc-color'></span>" );
    $(".create-tags .tags_list li div label").css("background-color",this.value);
    console.log(this.value);
})

