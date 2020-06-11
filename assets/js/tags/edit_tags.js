// // jquery 
// const $ = require('jquery');
// global.$ = global.jQuery = $;

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


// popop site 

// window.onbeforeunload

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


let siteInputPopup = [];
let siteInput = [];

$('.btn-add-site').on("click", function(){

    let $inputPopupIds = $('tr.popup__add-site__site td input');

    $inputPopupIds.each( (index, inputPopupId) => {
        if($(inputPopupId).is(":checked")){
            $(`.affected-site-container tr input[class=${$(inputPopupId).attr('class')}]`).parents('tr').removeClass('hidden');
            $(`.affected-site-container tr input[class=${$(inputPopupId).attr('class')}]`).prop('checked', true)
        }else{
            $(`.affected-site-container tr input[class=${$(inputPopupId).attr('class')}]`).parents('tr').addClass('hidden');
            $(`.affected-site-container tr input[class=${$(inputPopupId).attr('class')}]`).prop('checked', false)
        }
    })

    $('.add-popup-site').removeClass('is-open');
})

$('.tag__sites__site input[type="checkbox"]').on('change', function(){

    let $inputIds = $('tr.tag__sites__site td input');

    $inputIds.each( (index, inputId) => {
        if($(inputId).is(":checked")){
            $(`.affected-site-container-popup  tr input[class=${$(inputId).attr('class')}]`).prop('checked', true);
            // $(`.affected-site-container tr input[id=${$(inputPopupId).attr('id')}]`).parents('tr').removeClass('hidden');
        }else{
            $(`.affected-site-container-popup  tr input[class=${$(inputId).attr('class')}]`).prop('checked', false);
            // $(`.affected-site-container tr input[id=${$(inputPopupId).attr('id')}]`).parents('tr').addClass('hidden');
        }
    })
});


var val = $('.tags-color-edit').attr("value");
$('.input-color-edit').css("background-color", val);

$(".tags-color-edit").on('change', function(e){
    $('.input-color-edit').css("background-color",this.value);
})


// popup site

$('.btn-site').click(function () {
    $('.add-popup-site').addClass('is-open');
    return false;
  });

$('.btn-popupclose2').click(function () {
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