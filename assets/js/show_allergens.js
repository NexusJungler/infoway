const $ = require('jquery');
global.$ = global.jQuery = $;

let selectedAllergen = null;

$(function() {
    $("#list tbody").on( "change", "[type=checkbox]", function() {
        let inputName = $(this).prop('name');
        let searchID = inputName.substring(10);
        selectedAllergen = searchID.substr(0, searchID.length -1);
        console.log(selectedAllergen);
        $('#edit').prop('href','/allergen/edit/' + selectedAllergen);
    });

    $("#delete").on("click", function() {
        $('form').submit();
    });
});
