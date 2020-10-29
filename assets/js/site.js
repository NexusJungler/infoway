import "../css/show_site.scss";

let filtertablesite = new filterTableSite();

import filterTableSite from "./site/FiltreTable/FiltreTable";

$(function(){
    filtertablesite.enable();



    // $(".display-content-poste").find('.drop-btn').on('click', function() {
    //     $(this).parent().find('.display-content-down').toggle();
    // });

    let nameFilter = [
        "filter_city",
        "th_zone",
    ];

    // $.each(nameFilter, function(i, val){
    //     $('.' + val).click(function(){
    //         console.log(val);
    //         let  table = $(this).parents('table').eq(0)
    //         let  rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
    //         this.asc = !this.asc
    //         if (!this.asc){rows = rows.reverse()}
    //         for (var i = 0; i < rows.length; i++){table.append(rows[i])}
    //         //
    //         // if(orderClass == 'desc' || orderClass == '') {
    //         // 	$(this).addClass('asc');
    //         // 	orderClass = 'asc';
    //         // } else {
    //         //     $(this).addClass('desc');
    //         //     orderClass = 'desc';
    //         // }
    //     });
    //     function comparer(index) {
    //         return function(a, b) {
    //             let valA = getCellValue(a, index), valB = getCellValue(b, index)
    //             return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
    //         }
    //     }
    //     function getCellValue(row, index){
    //         return $(row).children('td').eq(index).text()
    //     }
    // });

    var properties = [
        "filter_city",
        "th_zone",
    ];

    $.each( properties, function( i, val ) {
        console.log(properties);
        var orderClass = '';

        $("." + val).click(function(e){
            console.log(val);
            e.preventDefault();

            $('.filter__link.filter__link--active').not(this).removeClass('filter__link--active');
            $(this).toggleClass('filter__link--active');
            // $('.filter__link').removeClass('asc desc');
            //
            // if(orderClass == 'desc' || orderClass == '') {
            //     $(this).addClass('asc');
            //     orderClass = 'asc';
            // } else {
            //     $(this).addClass('desc');
            //     orderClass = 'desc';
            // }

            var parent = $(this).closest('.header__item');
            var index = $(".header__item").index(parent);
            console.log(index);
            var $table = $('.table-custome');
            var rows = $table.find('tbody tr').get();


            var isSelected = $(this).hasClass('filter__link--active');

            var isNumber = $(this).hasClass('filter__link--number');

            console.log(rows);
            rows.sort(function(a, b){
                console.log("go1")
                var x = $(a).find('td.t').eq(index).text();

                var y = $(b).find('td.t').eq(index).text();

                if(isNumber == true) {

                    if(isSelected) {
                        return x - y;
                    } else {
                        return y - x;
                    }

                } else {

                    if(isSelected) {
                        console.log("goo2");
                        if(x < y) return -1;
                        if(x > y) return 1;
                        return 0;
                    } else {
                        if(x > y) return -1;
                        if(x < y) return 1;
                        return 0;
                    }
                }
            });

            $.each(rows, function(index,row) {
                $table.append(row);
                console.log(row);
            });

            return false;
        });

    });



});