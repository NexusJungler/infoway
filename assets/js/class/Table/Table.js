
class Table  {


    constructor (){
        
        $('#selectAll').on("click",(e)=>this.onClickSelectAll(e));
        
        $(".delete-table").on("click",(e)=>this.onClickDeleteTable(e));

        $('.display-content-poste input[type="checkbox"]').on("change",(e)=>this.onClickDeletecol(e));

        $('.table-custome .tbody input[name=select]').on('click',(e)=>this.onClickInputSelect(e));
    }

    onClickSelectAll(e){

        if($(e.currentTarget).is(":checked")){
            $('.table-custome .tbody input[name=select]').prop('checked',true);
        }else{
            $('.table-custome .tbody input[name=select]').prop('checked',false);
        }

    }

    onClickInputSelect(){

        if (!$(this).prop("checked")) {
            $("#selectAll").prop("checked", false);
        }
    }

    /**  DELETE LIST  **/
    onClickDeleteTable(e){
        
        // let allVals = []
        let tableClass = $(e.currentTarget).data('tablelistdelete');
        
         $('.table-'+tableClass+' tbody tr').find('input').each(function(){


            if ($(this).is(":checked")) {
                $(this).parents(".table-custome tr").remove();
            }  else{

            }
        });

        // if(allVals.length = 0){
        //     alert("select");
        // }else{
        //     console.log("text")
        //     $.each(allVals, function( index, value ) {
        //         $(this).parents(".table-custome tr").remove();
        //     });
              
        //     $(this).parents(".table-custome tr").remove();
        // }

        // AJAX Request
        $.ajax({
            url : '',
            type : 'post',

            success : function(){
                alert("succes");
            },
            error : function(){
                alert("fails");
            }
        })
    }

    /**  DELETE COL  **/
    onChangeDeleteCol(e){

        let $checkBox = $(e.currentTarget) ;

        let data_table_deleteCol = $(e.currentTarget).data("tabledeletecol")

        if( $checkBox. is( ':checked' ) ){
            $('.table-custome .table-info tr th.'+data_table_deleteCol).show();
            $('.table-custome .tbody tr td.'+data_table_deleteCol).show();
        } else{
            $('.table-custome .table-info tr th.'+data_table_deleteCol).hide();
            $('.table-custome .tbody tr td.'+data_table_deleteCol).hide();
        }
    }

    table() {	
        
        
        
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