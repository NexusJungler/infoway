const $ = require('jquery');
global.$ = global.jQuery = $;

// require('../css/debug.css'); Si css spécifique à la page!

let selectedProduct = null;

$(function() {
    // Remplace la syntaxe: $(document).ready(function() {});
    $("#list tbody").on( "change", "[type=checkbox]", function() {
        let inputName = $(this).prop('name');
        let searchID = inputName.substring(9);
        selectedProduct = searchID.substr(0, searchID.length -1);
        console.log(selectedProduct);
        $('#edit').prop('href','/product/edit/' + selectedProduct);
    });

    $("#delete").on("click", function() {
        $('form').submit();
    });

    $("#category").on("change", function(){
        let id = $(this).children(':selected').val();
        /*
        $.post('/ajax/products/list/', {category: id}, function(list){
            // $('#table-list').html(list);
            console.log(list);
        });
        */

        $.get('/ajax/products/list/' + id, function(list){
            let response = JSON.parse(list);
            let html = '';
            $.each(response, function(i, product){
                html += '<tr><td><input type="checkbox" name="products[' + product.id + ']"></td>';
                html += '<td>' + product.name + '</td>';
                html += '<td>' + product.category.name + '</td>';
                html += '<td>' + product.priceType.name + '</td>';
                html += '<td>' + product.amount + '</td>';
                html += '<td>' + product.description + '</td>';
                html += '<td>' + product.note + '</td>';
                html += '<td></td>';
                html += '<td></td>';
                html += '<td>' + product.logo + '</td></tr>';
            });
            $('#list tbody').html(html);

        });
    });
});