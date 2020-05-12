// import style css
import "../css/groupe-prix.scss";

const $ = require('jquery');
global.$ = global.jQuery = $;

let nbr_dates = 0;
let products = [];
let factories = [];
let selectedSites = [];
let selectedFactories = [];
let updatedPrices = {};

let display_local_prices = function () {
    $.post('ajax/localprices', {sites: selectedSites}, function(response){
        response = JSON.parse(response);
        let sites = response.sites;
        let products = response.products;
    });
};

let display_main_prices = function () {
    $.post('/pricesfactories/ajax/getprices', {factories: selectedFactories}, function(response){
        // les ids des prix se retrouvent nécessairement dans toutes les colonnes "actuelle" (le cas échéant)
        // les ids des modifications attendues se retrouvent dans toutes les autres colonnes à date (le cas échéant)

        response = JSON.parse(response);
        nbr_dates = response.nbr_dates;
        factories = response.factories;
        products = response.products;

        let view = {head: '', body: ''};
        let rowspan = '';
        let colspan = '';
        let memory = {}; //previousPrices
        
        if(nbr_dates > 1) {
            rowspan = ' rowspan="2"';
            colspan = ' colspan="' + nbr_dates + '"';
        }

        view.head += '<tr><th' + rowspan + ' ><p class="th-title"> Produit </p></th>';
        view.head += '<th' + rowspan + '><p class="th-title">Form.</p></th>';
        view.head += '<th' + rowspan + '><p class="th-title">Catégorie</p></th>';
        view.head += '<th' + rowspan + '><p class="th-title">Type</p></th>';
        view.head += '<th' + rowspan + '><p class="th-title">TAGS</p></th>';

        $.each(factories, function(i, factory){
            // let nbr_cells = Object.keys(factory.prices).length;
            view.head += '<th' + colspan + ' class="group-prix" >' + factory.name + '</th>';
        });
        view.head += '</tr>';

        if(nbr_dates > 1) {
            
            view.head += '<tr>';
            $.each(factories, function(i, factory) {
                for(const date in factory.prices) {
                    view.head += '<td class="col'+i+'">' + date + '</td>';
                }
            });
            view.head += '</tr>';
        }

        for(const product_id in products) {
            if (products.hasOwnProperty(product_id)) {
                let product = products[product_id];
                view.body += '<tr><td>' + product.name + '</td>';
                view.body += '<td>' + product.amount+ '</td>';
                view.body += '<td>' + product.category + '</td>';
                view.body += '<td>' + product.pricetype + '</td><td>';
                $.each(product.tags, function(j, tag){
                    view.body += '<span class="container-tags">' + tag + '</span>';
                });
                view.body += '</td>';

                $.each(factories, function(j, factory) {
                    for(const date in factory.prices) {
                        let price_id_injection = ''; let price_value = '0.00';
                        let prices = factory.prices[date];
                        // let tdclass = date === 'actuelle' ? ' class="actu"' : '';

                        if (typeof prices[product_id] !== 'undefined') {
                            price_value = prices[product_id].day;
                            price_id_injection = ' data-price="' + prices[product_id].price  + '"';
                            if(date !== 'actuelle') {
                                price_id_injection = ' class="changed col-td'+j+'" data-change="' + prices[product_id].change  + '"';
                            }
                        } else {
                            if (typeof memory[factory.id][product_id] !== 'undefined') {
                                price_value = memory[factory.id][product_id];
                            }
                        }
                        view.body += '<td '+ price_id_injection +' class="col-td'+j+'" ><input'+ price_id_injection +' type="text"  name="factories[' + factory.id + '][' + date + '][' + product_id + '][day]" value="' + price_value + '"></td>';

                        if(typeof memory[factory.id] === 'undefined') {
                            memory[factory.id] = {};
                        }
                        memory[factory.id][product_id] = price_value;

                    }
                });
                view.body += '</tr>';
            }
        }

        $('#display_prices table').html(view.head + view.body);
        $('#display_prices').css('display', 'block');

    });
};

$(function() {

    $("#list tbody").on("change", "input.factory", function() {
        let inputName = $(this).prop('name');
        let searchID = inputName.substring(10);
        let factory_id = searchID.substr(0, searchID.length -1);
        let is_checked = $(this).prop('checked');

        if(is_checked && !selectedFactories.includes(factory_id)) {
            selectedFactories.push(factory_id);
        }
        if(!is_checked && selectedFactories.includes(factory_id)) {
            let index = selectedFactories.indexOf(factory_id);
            selectedFactories.splice(index,1);
        }

        if(selectedFactories.length === 1) {
            $('#edit').prop('disabled', false);
        } else {
            $('#edit').prop('disabled', true);
        }

    });

    $("#list tbody").on("change", "input.site", function() {
        let inputName = $(this).prop('name');
        let startIdx = inputName.indexOf('[');
        let endIdx = inputName.indexOf(']');
        let site_id = inputName.substring(startIdx + 1, endIdx);
        let is_checked = $(this).prop('checked');

        if(is_checked && !selectedSites.includes(site_id)) {
            selectedSites.push(site_id);
        }
        if(!is_checked && selectedSites.includes(site_id)) {
            let index = selectedSites.indexOf(site_id);
            selectedSites.splice(index,1);
        }
        console.log(selectedSites);
    });

    $('#edit').click(function(){
        window.location = '/pricesfactory/edit/' + selectedFactories[0];
    });

    $('#display_prices').click(function(){
        if(selectedFactories.length > 1) {
            display_main_prices();
        } else {
            display_local_prices();
        }
    });

    $('#display_prices form table').on("change", "input", function(){
        let name = $(this).prop('name');
        let params = name.split('[');

        let factory = params[1].substr(0, params[1].length -1);
        let date = params[2].substr(0, params[2].length -1);
        let product = params[3].substr(0, params[3].length -1);

        if(typeof updatedPrices[factory] === 'undefined') {
            updatedPrices[factory] = {};
        }

        if(typeof updatedPrices[factory][date] === 'undefined') {
            updatedPrices[factory][date] = {};
        }

        if(typeof updatedPrices[factory][date][product] === 'undefined') {
            updatedPrices[factory][date][product] = {};
        }

        if(typeof $(this).data('price') !== 'undefined') {
            updatedPrices[factory][date][product]['price_id'] = $(this).data('price');
        }

        if(typeof $(this).data('change') !== 'undefined') {
            if($(this).data('change') !== 'New') {
                updatedPrices[factory][date][product]['change_id'] = $(this).data('change');
            }

            let row = $(this).parents('tr');
            // let row_index = $('#display_prices tr').index(row);
            let col = $(this).parent();
            let col_index = row.children().index(col);

            let result = null; // correspondra aux nombres de cellules (td) qu'il faut remonter pour accéder aux prix de la factory (cellule immédiatement précédente non comprise)
            let title = '';
            let i = 5; // nombre de cellules correspondants aux informations du produit
            while (title !== 'actuelle') {
                title = $('#display_prices tr:nth-child(2)').children(':nth-child(' + (col_index - i) + ')').html(); // Si aucune date future ==> sélection = tr:nth-child(1)
                result = i - 5;
                i++;
            }
            updatedPrices[factory][date][product]['price_id'] = row.children(':nth-child(' + (col_index - result) + ')').children().data('price');
        }

        updatedPrices[factory][date][product]['day'] = $(this).val();
        console.log(updatedPrices);
    });

    $('#prices_validate').click(function(e){
        e.preventDefault();
        console.log(updatedPrices);
        $.post('/pricesfactories/ajax/saveprices', {factories: updatedPrices}, function(response){
            console.log(response);
        });
    });

    $('#add_date').click(function(){
        let newDate = '12-06-2020';
        let nbr_product_columns = 5;

        // CSS add column in factory header
        let factories_head = $('#display_prices tr:nth-child(1)').children(':gt(' + (nbr_product_columns - 1) + ')');
        factories_head.prop('colspan', nbr_dates + 1);

        // Ajout des nouvelles cellules de date dans le head
        $.each(factories, function(i, factory) {
            let target = $('#display_prices tr:nth-child(2)').children(':nth-child(' + (nbr_dates + i*nbr_dates + i) + ')');
            $('<td class="col'+col_table+'">' + newDate + '</td>').insertAfter(target);
        });

        // Ajout des nouvelles cellules du formulaire dans le body
        let nbr_products = Object.keys(products).length;
        let i = 0;
        for(const product_id in products) {
            i++;
            $.each(factories, function(j, factory) {
                console.log((2+i), nbr_dates + nbr_product_columns + j*nbr_dates + j);
                let target = $('#display_prices tr:nth-child('+ (2 + i) + ')').children(':nth-child(' + (nbr_dates + nbr_product_columns + j*nbr_dates + j) + ')');
                console.log(target);
                let price_value = target.children().val();
                let input = '<input data-change="New" type="text" name="factories[' + factory.id + '][' + newDate + '][' + product_id + '][day]" value="' + price_value + '">';
                $('<td>' + input + '</td>').insertAfter(target);
            });
        }

        nbr_dates++;
    });

    
      

    // $("#display_prices ").each(".group-prix" ,function() {
    //     //On change la couleur de fond au hasard
    //     $(this).css("background-color", '#'+(Math.random({hue:'red'})*0xFFFFFF<<0).toString(16));
    // })

});

function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
      color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

    $(".group-prix").each(function(){
        $(this).css("background-color", getRandomColor());
    })
    
