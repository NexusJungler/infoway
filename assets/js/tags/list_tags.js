// jquery 
const $ = require('jquery');
global.$ = global.jQuery = $;

/** page list tags**/
let nameTags = [];

// btn modification
$(".modified-tag").addClass("hide-btn");

$(".content-tags .tags-poster .chkbox-tag").change( function(){
    let nb_input_tags = $(".tags-poster input[type='checkbox']:checked").length;
    
    /** Btn Modifaction tags **/

    if(  nb_input_tags === 1 ){
        $(".modified-tag").prop('disabled', false)
        $(".modified-tag").removeClass("hide-btn");
    }else{
        $(".modified-tag").prop('disabled', true)
        $(".modified-tag").addClass("hide-btn");
    }

})

//btn delete
$(".delete-tag-popup").addClass("hide-btn");

$(".content-tags .tags-poster .chkbox-tag").change( function(){
    let nb_input = $(".tags-poster input[type='checkbox']:checked").length;
    
    /** Btn delete tags **/
    if( nb_input > 0 ){
        $(".delete-tag-popup").prop('disabled', false)
        $(".delete-tag-popup").removeClass("hide-btn");
        

    }else{
        $(".delete-tag-popup").prop('disabled', true)
        $(".delete-tag-popup").addClass("hide-btn");

    }
})

// popup modification
$('.modified-tag').click(function () {
    $('.add-popup-edit').addClass('is-open');

    $.each($(".tags-poster input[type='checkbox']:checked"), (index, input) => {
        let selectedTagName = $(input).parents("td").find(".current-tags-name").text();
        if(nameTags.indexOf(selectedTagName) === -1){
            nameTags.push(selectedTagName);
        }
    });
    
    nameTags.forEach(nameTag =>{
        if($(`.content-modal .selected-tags-list p[text="${nameTag}"]`).length === 0 ){
            $("<p>" + nameTag + "</p>").appendTo($(".content-modal .selected-tags-list"));
        }
    });
    return false;
});

$('.btn-popupclose').click( () => {
    $('.add-popup-edit').removeClass('is-open');
    $(".content-modal .selected-tags-list ").empty();
    nameTags=[];
});


// popup delete
$('.delete-tag-popup').click(function () {
    $('.add-popup-delete').addClass('is-open');

    $.each($(".tags-poster input[type='checkbox']:checked"), (index, input) => {
        let selectedTagName = $(input).parents("td").find(".current-tags-name").text();
        if(nameTags.indexOf(selectedTagName) === -1){
            nameTags.push(selectedTagName);
        }
    });

    nameTags.forEach(nameTag =>{
        if($(`.content-modal .selected-tags-list p[text="${nameTag}"]`).length === 0 ){
            $("<p>" + nameTag + "</p>").appendTo($(".content-modal .selected-tags-list"));
        }
    });
    return false;
});

$('.btn-popupclose').click( () => {
    $('.add-popup-delete').removeClass('is-open');
    $(".content-modal .selected-tags-list ").empty();
    nameTags=[];
});