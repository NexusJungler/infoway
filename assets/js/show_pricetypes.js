const $ = require('jquery');
global.$ = global.jQuery = $;

let selectedPriceType = null;

$(function() {
    $("#list tbody").on( "change", "[type=checkbox]", function() {
        let inputName = $(this).prop('name');
        let searchID = inputName.substring(11);
        selectedPriceType = searchID.substr(0, searchID.length -1);
        console.log(selectedPriceType);
        $('#edit').prop('href','/pricetype/edit/' + selectedPriceType);
    });

    $("#delete").on("click", function() {
        $('form').submit();
    });
});