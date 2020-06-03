// import style css
import "../css/products/edit_product.scss";


const $ = require('jquery');
global.$ = global.jQuery = $;

let selectedDates = [];
let newDate = null;

$(function() {

    $('#expected_change').click(function(){
        $('#popup_dates').css('display', 'block');
    });

    $('#popup_dates').on("change", "input[type=checkbox]", function() {
        let inputName = $(this).prop('name');
        let searchID = inputName.substring(5);
        let date_id = searchID.substr(0, searchID.length - 1);
        let is_checked = $(this).prop('checked');

        if (is_checked && !selectedDates.includes(date_id)) {
            selectedDates.push(date_id);
        }
        if (!is_checked && selectedDates.includes(date_id)) {
            let index = selectedDates.indexOf(date_id);
            selectedDates.splice(index, 1);
        }
    });

    $('#popup_dates').on("change", "input[type=date]", function() {
        newDate = $(this).val();
    });

    $('#change_validate').click(function() {
        let current_url = window.location.pathname;
        let chain = current_url.split('/');
        let instance = chain[chain.length-1];
        // document.location.href = '/expected_change_bis/product/' + instance;
        console.log($('#redirectToEdit'));
        $('#redirectToEdit').submit();
    });

    $("select").change(function(){
        var options = $(this).children();
        $.each(options, function(i, option){
            let value = 'false';
            // console.log($(this));
            console.log($(this).prop('selected'));
            if($(this).prop('checked')) {
                value = 'true';
            }
            // console.log('child ' + i + '= ' + value);
        });
    });

});
