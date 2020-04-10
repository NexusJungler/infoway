// css
import '../../css/products/product_price.scss';

// jquery
const $ = require('jquery');
global.$ = global.jQuery = $;

$('.add-table').click(function () {
    $('.prouct-price-content .add-popup').addClass('is-open');
    $('.header').addClass('popup-add');

    return false;
  });

$('.btn-popupclose').click(function () {
    $('.add-popup').removeClass('is-open');
});