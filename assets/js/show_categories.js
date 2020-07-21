import "../css/products/category/show_categories.scss"


const $ = require('jquery');
global.$ = global.jQuery = $;

let selectedCategory = null;

$(function() {
    $("#list tbody").on( "change", "[type=checkbox]", function() {
        let inputName = $(this).prop('name');
        let searchID = inputName.substring(11);
        selectedCategory = searchID.substr(0, searchID.length -1);
        console.log(selectedCategory);
        $('#edit').prop('href','/category/edit/' + selectedCategory);
    });

    $("#delete").on("click", function() {
        $('form').submit();
    });
});
