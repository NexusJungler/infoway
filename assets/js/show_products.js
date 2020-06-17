// import style css
import "../css/products/show_product.scss";

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

    $('#edit').on("click", function(e) {
        if(selectedProduct === null) {
            e.preventDefault();
        }
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
                if(product.description === null) {
                    product.description = '';
                }
                if(product.note === null) {
                    product.note = '';
                }
                html += '<td>' + product.description + '</td>'; // (product.description === null) ? '' : product.description
                html += '<td>' + product.note + '</td>';
                html += '<td>' + product.start + '</td>';
                html += '<td>' + product.end + '</td>';
                html += '<td class="tag">';
                $.each(product.tags, function(j, tag){
                   html += '<span>' + tag.name + '</span>';
                });
                html += '</td>';
                // allergens
                html += '<td class="tag">';
                $.each(product.allergens, function(j, allergen){
                    html += '<span>' + allergen.name + '</span>';
                });
                html += '</td>';
                html += '<td><img src="/logo/' + product.logo + '"></td></tr>';
            });
            $('#list tbody').html(html);
        });
    });

    $('#duplicate').click(function(){
        $('#popup_properties').css('display', 'block');
        $('#popup_properties form').prop('action','/product/duplicate/' + selectedProduct);
    });

    $('#properties_validate').click(function(){
        /*
       $('#popup_properties').css('display', 'none');
       let keptProperties = [];
       let selectedBoxes = $(this).parent().find('input:checked');
       $.each(selectedBoxes, function(i, box){
           keptProperties.push($(box).prop('name'));
       });

       $.post('/ajax/product/duplicate', {product: selectedProducts[0]}, function(){

       });
       */

    });

    
   $('#duplicate').click(function () {
        $('.add-popup2').addClass('is-open');
        return false;
  });

    $('.btn-popupclose2').click(function () {
        $('.add-popup2').removeClass('is-open');
    });


    $('.content-product .display-content input[type="checkbox"]').on("change" , function (e) {

        let $checkBox = $(e.currentTarget);
        let table_hidden_Col = $(e.currentTarget).val();

        if( $checkBox. is( ':checked' ) ){
            $('.content-product .table-custome thead tr th.'+table_hidden_Col).show();
            $('.content-product .table-custome .tbody tr td.'+table_hidden_Col).show();
        } else{
            $('.content-product .table-custome thead tr th.'+table_hidden_Col).hide();
            $('.content-product .table-custome .tbody tr td.'+table_hidden_Col).hide();
        }
    });


});
