class Table  {

    table() {	
        
        // delete ligne

        $('input[type="checkbox"]').change( e => {

            let $chechedchange = $(e.currentTarget);
            let indexCol = $chechedchange.parents('.product-select-col').index() + 2;
            console.log(indexCol);

            if($chechedchange.is(":checked")){
                $('.table-custome .table-info tr th:nth-child('+(indexCol)+')').show();
                $('.table-custome .tbody tr td:nth-child('+(indexCol)+')').show();
                // return;
            } else{
                $('.table-custome .table-info tr th:nth-child('+(indexCol)+')').hide();
                $('.table-custome .tbody tr td:nth-child('+(indexCol)+')').hide();
            }
        })


        // delete
        $(".delete-table").click(function(){
            $('.table-custome').find('input[name="select"]').each(function(){
                if ($(this).is(":checked")) {
                    // $('.delete-table').removeAttr('disabled');
                    $(this).parents(".table-custome tr").remove();
                }  else{
                    // $('.delete-table').attr('disabled', true);
                }
            });
        });

        // popup

        // $(".add-table").click(function(){
        //     $('.add-popup').show();
        // });

        // $('.btn-popupclose').click(function(){
        //     $('.add-popup').hide();
        // });

        $('.add-table').click(function () {
            $('.add-popup').addClass('is-open');
            return false;
          });
        
          $('.btn-popupclose').click(function () {
            $('.add-popup').removeClass('is-open');
          });


        // let tableId= [
        //     "f-name",
        //     "form",
        //     "f-tags",
        //     "prix",
        // ];

        // $.each(tableId, function(i, val){
        //     var orderClass = '';
            
        //     $("#" + val).click(function(e){
        //         e.preventDefault();
        //         $('.filter.filter-active').not(this).removeClass('.filter-active');
        //         $(this).toggleClass('.filter-active');
        //         $('.filter').removeClass('asc desc');

        //         if(orderClass == 'desc' || orderClass == '') {
        //             $(this).addClass('asc');
        //             orderClass = 'asc';
        //        } else {
        //            $(this).addClass('desc');
        //            orderClass = 'desc';
        //        }
        //        let parent = $(this).closest('.header__item');
        //         let index = ('.header__item').index(parent);
        //         let $table = $('.table-content');
        //         let rows = $table.find('.table-row').get();
        //         let isSelected = $(this).hasClass('filter-active');
        //         let isNumber = $(this).hasClass('filter-number');
                
        //         rows.sort(function(a, b){

        //             var x = $(a).find('.table-data').eq(index).text();
        //                 var y = $(b).find('.table-data').eq(index).text();
                        
        //             if(isNumber == true) {
                                
        //                 if(isSelected) {
        //                     return x - y;
        //                 } else {
        //                     return y - x;
        //                 }
        
        //             } else {
                    
        //                 if(isSelected) {		
        //                     if(x < y) return -1;
        //                     if(x > y) return 1;
        //                     return 0;
        //                 } else {
        //                     if(x > y) return -1;
        //                     if(x < y) return 1;
        //                     return 0;
        //                 }
        //             }
        //             });
        
        //         $.each(rows, function(index,row) {
        //             $table.append(row);
        //         });
        
        //         return false;
        //     })
        // })

        //   $('th').click(function(){

        //     var table = $(this).parents('table').eq(0)
        //     var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
        //     this.asc = !this.asc
        //     if (!this.asc){rows = rows.reverse()}
        //     for (var i = 0; i < rows.length; i++){table.append(rows[i])}

        //     if(orderClass == 'desc' || orderClass == '') {
    	// 		$(this).addClass('asc');
    	// 		orderClass = 'asc';
        //     } else {
        //         $(this).addClass('desc');
        //         orderClass = 'desc';
        //     }
        // })
        // function comparer(index) {
        //     return function(a, b) {
        //         var valA = getCellValue(a, index), valB = getCellValue(b, index)
        //         return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
        //     }
        // }
        // function getCellValue(row, index){ return $(row).children('td').eq(index).text() }

        
    }
    
}
    
export {Table}