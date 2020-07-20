class filterTableSite{

    enable(){
        this.onChangeFilter(true);
    }
    disable(){
        this.onChangeFilter(false);
    }

    onChangeFilter(active) {
        if(active){

            $('.site-container .filter_city').change(function(){

                $('.site-container table tbody tr').hide();

                let cityValue = $('.filter_city').val();

                $('.site-container table.table-custome tr').each(function() {

                    if( cityValue == 0 || cityValue == $(this).find('td.city').data( 'format' ) ){
                        if(  cityValue == 0 ){ $('.site-container table tbody tr').show(); }
                        $(this).show();
                    }
                });
            });

        }else{
            $(".site-container .filter-city").off('click.onChangeFilter');
        }
    }

}

export default filterTableSite;