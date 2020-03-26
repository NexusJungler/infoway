
class Table  {


    constructor (){
        
        $('#selectAll').on("click",(e)=>this.onClickSelectAll(e));
        
        $(".delete-table").on("click",(e)=>this.onClickDeleteTable(e));

        $('.display-content-poste input[type="checkbox"]').on("change",(e)=>this.onClickDeletecol(e));

        $('.table-custome .tbody input[name=select]').on('click',(e)=>this.onClickInputSelect(e));
    }

    /**  DELETE LIST  **/
    onClickDeleteTags(e){
        
        
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
        
    }
    
}
    
export {Table}