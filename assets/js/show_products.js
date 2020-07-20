/** import style css  **/
// require('../css/debug.css'); Si css spécifique à la page!
import "../css/products/show_product.scss";
import  "../css/popups/popup_duplicate_product/popup_duplicate_product.scss"
const $ = require('jquery');
global.$ = global.jQuery = $;

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
        $('#form_delete_product').submit();
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
                html += '<tr>' +
                    '<td>' +
                        '<label class="container-input"> ' +
                            '<input type="checkbox" name="products[' + product.id + ']"> ' +
                            '<span class="container-rdo-tags"></span>' +
                        '</label>' +
                    '</td>';
                html += '<td>' +' <span class="bloc-icone"><i class="fas fa-spinner"></i></span> ' + '</td>';
                html += '<td>' + product.name + '</td>';
                html += '<td> ' + '<i class="fas fa-pen"></i> '  + '</td>';
                html += '<td>' + product.amount + '</td>';
                html += '<td>' + product.category.name + '</td>';
                if(product.description === null) {
                    product.description = '';
                }
                if(product.note === null) {
                    product.note = '';
                }
                html += '<td>' + product.description + '</td>'; // (product.description === null) ? '' : product.description
                html += '<td>' + product.note + '</td>';
                html += '<td class="tag">';
                $.each(product.tags, function(j, tag){
                   html += '<p class="container-tags"> ' +
                       '<span class="mini-cercle" style="background:'+ tag.color + ';"> ' +
                       '</span>' +  tag.name + '</p>';
                       // '<span>' + tag.name + '</span>';
                });
                html += '</td>';
                html += '<td>' + product.priceType.name + '</td>';
                // allergens
                html += '<td class="allergenes">';
                $.each(product.allergens, function(j, allergen){
                    html += '<span>' + allergen.name + '</span>';
                });
                html += '</td>';
                html += '<td>' + product.start + '</td>';
                html += '<td>' + product.end + '</td>';
                html += '<td><i class="fas fa-eye"></i></td>';
                // html += '<td> <img src="/logo/' + product.logo + '"></td></tr>';
                html += '</tr>';
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


    $('.content-product .display-content ').on("change","input[type='checkbox']" , function (e) {

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


    $(".popup_comfirmation_edit").addClass("btn_hidden");
//btn delete
    $(".popup_comfirmation_delete").addClass("btn_hidden");


    $("#checkbox_verification ").on("each","input[type='checkbox']" ,function () {
        if ($(this).prop("checked")) {
            $(".popup_comfirmation_edit").removeClass("btn_hidden");
            $(".popup_comfirmation_delete").removeClass("btn_hidden");
        }
        console.log($(this).prop("checked"));
    });

    $("#checkbox_verification ").on("change","input[type='checkbox']" ,function () {
        let nb_input_tags = $("#checkbox_verification input[type='checkbox']:checked").length;

        /** Btn Modifaction tags **/
        if (nb_input_tags === 1) {
            $(".popup_comfirmation_edit").prop('disabled', false);
            $(".popup_comfirmation_edit").removeClass("btn_hidden");

            $("#duplicate").prop('disabled', false);
            $("#duplicate").removeClass("btn_hidden");
        } else {
            $(".popup_comfirmation_edit").prop('disabled', true);
            $(".popup_comfirmation_edit").addClass("btn_hidden");

            $("#duplicate").prop('disabled', true);
            $("#duplicate").addClass("btn_hidden");
        }

        /** Btn delete tags **/
        if (nb_input_tags > 0) {
            $(".popup_comfirmation_delete").prop('disabled', false);
            $(".popup_comfirmation_delete").removeClass("btn_hidden");

        } else {
            $(".popup_comfirmation_delete").prop('disabled', true);
            $(".popup_comfirmation_delete").addClass("btn_hidden");
        }

    });

    $('.content-product-show-table table tbody tr').show();


    $('.filter').change(function(){

        $('.content-product-show-table table tbody tr').hide();

        let formatFlag = 0;
        let formatValue = $('#form').val();
        console.log(formatFlag);

        $('.content-product-show-table table.table-custome tr').each(function() {

            if(formatValue == 0){
                formatFlag  = 1;
            }
            else if(formatValue == $(this).find('td.format').data('format')){
                formatFlag  = 1;
            }
            else{
                formatFlag  = 0;
            }

            if(formatFlag  ){
                $(this).show();
            }

        });
    });


});
