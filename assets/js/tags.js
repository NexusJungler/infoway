// import style css
import "../css/tags/create_tags.scss";
import "../css/tags/list_tags.scss";
import "../css/tags/edit_tags.scss";

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

// recupere les couleur et afficher sur les tags
$("ul.tags_list").on('change', ".tags-color" , function(e){
    $(this).parents('ul.tags_list li ').find( '.color_input' ).css("background-color",this.value);
})

/** page create tags**/

// popup site

$('.btn-site').click(function () {
    $('.add-popup-site').addClass('is-open');
    return false;
  });

$('.btn-popupclose').click(function () {
    $('.add-popup-site').removeClass('is-open');
});

// popup site produits

$('.btn-produits').click(function () {
    $('.add-popup-produits').addClass('is-open');
    return false;
  });

$('.btn-popupclose2').click(function () {
    $('.add-popup-produits').removeClass('is-open');
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

    let $tagNameInput = $('#tag_list_tagToCreate_name') ;
    newElem.find('input.tags-name').val( $tagNameInput.val() );
    
    let $tagDescriptionInput  = $('#tag_list_tagToCreate_description')
    newElem.find('input.tags-desc').val( $tagDescriptionInput.val() );


    $('<span class="color_input">').insertBefore(newElem.find('.tags-name'));

    newElem.appendTo(list);

})

addTagBtn.of("click", e =>{

    if ($("#tag_list_tagToCreate_name") === "TEXT") {
        $("#tag_list_tagToCreate_name").value('');
    }
    
})


// page edit tags


var val = $('.tags-color-edit').attr("value");
$('.input-color-edit').css("background-color", val);

$(".tags-color-edit").on('change', function(e){
    $('.input-color-edit').css("background-color",this.value);
})

// popop site 
$('.tag__sites__site input[type="checkbox"]').on('change', function(){

    let $targetedCheckBox = $(this) ;

   let needChange = !(  ( $targetedCheckBox.is(':checked') && $targetedCheckBox.is(':visible') ) || ( !$targetedCheckBox.is(':checked') && !$targetedCheckBox.is(':visible') ) )

    if( ! needChange ) return ;

    let $inputContainer = $targetedCheckBox.parents('tr.tag__sites__site')
    let $sitesPopupCorrespondingCheckbox = $(`input#add_site_sites_${this.value}`) ;

    if( $(this).is(':checked') ) {

        $inputContainer.removeClass('hidden')
        $sitesPopupCorrespondingCheckbox.prop('checked', true)
        $sitesPopupCorrespondingCheckbox.parents('tr').addClass('hidden')
    }else{
        $inputContainer.addClass('hidden')
        $sitesPopupCorrespondingCheckbox.prop('checked', false)
        $sitesPopupCorrespondingCheckbox.parents('tr').removeClass('hidden')

    }
    // console.log( $sitesPopupCorrespondingCheckbox )
});


$('.popup__add-site__site input[type="checkbox"]').on('change' , function(){

    let $targetedCheckBox = $(this) ;

    let $inputContainer = $targetedCheckBox.parents('tr.popup__add-site__site')
    let $tagSitesCorrespondingCheckbox = $(`input#tag_sites_${this.value}`);

    let needChange = !(  ( $targetedCheckBox.is(':checked') &&  $inputContainer.css('display') === 'none' ) || ( ! $targetedCheckBox.is(':checked') &&  $inputContainer.css('display') !== 'none' ) )
    if( ! needChange )  return ;

    if( $(this).is(':checked') ) {
        $inputContainer.addClass('hidden')
        $tagSitesCorrespondingCheckbox.prop('checked', true)
        $tagSitesCorrespondingCheckbox.parents('tr').removeClass('hidden')
    }else{
        $inputContainer.removeClass('hidden')
        $tagSitesCorrespondingCheckbox.prop('checked', false)
        $tagSitesCorrespondingCheckbox.parents('tr').addClass('hidden')
    }
    // console.log( $tagSitesCorrespondingCheckbox )

})


//produits


$('.tag__products__product input[type="checkbox"]').on('change', function(){

    let $targetedCheckBox = $(this) ;

   let needChange = !(  ( $targetedCheckBox.is(':checked') && $targetedCheckBox.is(':visible') ) || ( !$targetedCheckBox.is(':checked') && !$targetedCheckBox.is(':visible') ) )

    if( ! needChange ) return ;
    
    let $inputContainer = $targetedCheckBox.parents('tr.tag__products__product')
    let $produtsPopupCorrespondingCheckbox = $(`input#add_product_products_${this.value}`) ;
    console.log($inputContainer);
    console.log($produtsPopupCorrespondingCheckbox);
    if( $(this).is(':checked') ) {

        $inputContainer.removeClass('hidden')
        $produtsPopupCorrespondingCheckbox.prop('checked', true)
        $produtsPopupCorrespondingCheckbox.parents('tr').addClass('hidden')
    }else{
        $inputContainer.addClass('hidden')
        $produtsPopupCorrespondingCheckbox.prop('checked', false)
        $produtsPopupCorrespondingCheckbox.parents('tr').removeClass('hidden')

    }
    // console.log( $produtsPopupCorrespondingCheckbox )
});


$('.popup__add-product__products input[type="checkbox"]').on('change' , function(){

    let $targetedCheckBox = $(this) ;

    let $inputContainer = $targetedCheckBox.parents('tr.popup__add-product__products')
    let $tagSitesCorrespondingCheckbox = $(`input#tag_products_${this.value}`);

    let needChange = !(  ( $targetedCheckBox.is(':checked') &&  $inputContainer.css('display') === 'none' ) || ( ! $targetedCheckBox.is(':checked') &&  $inputContainer.css('display') !== 'none' ) )
    if( ! needChange )  return ;

    if( $(this).is(':checked') ) {
        $inputContainer.addClass('hidden')
        $tagSitesCorrespondingCheckbox.prop('checked', true)
        $tagSitesCorrespondingCheckbox.parents('tr').removeClass('hidden')
    }else{
        $inputContainer.removeClass('hidden')
        $tagSitesCorrespondingCheckbox.prop('checked', false)
        $tagSitesCorrespondingCheckbox.parents('tr').addClass('hidden')
    }
    // console.log( $tagSitesCorrespondingCheckbox )

})




$("#selectAll").click(function() {
    $("input[type=checkbox]").prop("checked", $(this).prop("checked"));
});

$("input[type=checkbox]").click(function() {
    if (!$(this).prop("checked")) {
        $("#selectAll").prop("checked", false);
    }
});



