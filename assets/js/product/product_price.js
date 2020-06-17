// css
import '../../css/products/product_price.scss';

// jquery
const $ = require('jquery');
global.$ = global.jQuery = $;
$(function(){

    $('.add-table').click(function () {
        $('.add-popup').addClass('is-open');
        return false;
    });

    $('.btn-popupclose').click(function () {
        $('.add-popup').removeClass('is-open');
    });


})

