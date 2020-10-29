class filtreMediaCard{

    constructor()
    {
        this.__btnFiltre = $(".filter_validation_btn_container .filter_media_btn");
    }


    filtreSelectMediaCard(active){
        if(active){

            this.__btnFiltre.on("click", function () {

                let checkedSelectMedia = $(".filters_by_associated_data_container input:checked").length;

                if ( checkedSelectMedia >= 1){

                    $(".medias_list_container_cards .card ").each((index, card) => {

                        $(card).hide();

                        let menuboardValues = $(".filter_by_categories input:checkbox:checked").map( (index, input) => $(input).val() );
                        let produitValues = $(".filter_by_produit input:checkbox:checked").map( (index, input) => $(input).val() );
                        let criterionValues =  $(".filter_by_criterion input:checkbox:checked").map( (index, input) => $(input).val() );
                        let tagsValues = $(".filter_by_tag input:checkbox:checked").map( (index, input) => $(input).val() );
                        let counter = 0;

                        $('.filters_by_associated_data_container .display-content-poste').map( (index, filterCheckboxContainer) => {

                            if( $(filterCheckboxContainer).find('input:checkbox:checked').length > 0 )
                                counter++;
                        });

                        let i = 0;

                        if(menuboardValues.length > 0){

                            menuboardValues.each( (index,value) => {
                                if($(card).attr("data-categories").indexOf(value) >= 0)
                                    i++;
                            });
                        }

                        if(produitValues.length > 0){

                            produitValues.each( (index,value) => {
                                if($(card).attr("data-products").indexOf(value) >= 0)
                                    i++;
                            });
                        }

                        if(criterionValues.length > 0){

                            criterionValues.each( (index,value) => {
                                if($(card).attr("data-product_criterions").indexOf(value) >= 0)
                                    i++;
                            });
                        }

                        if(tagsValues.length > 0){
                            tagsValues.each( (index,value) => {
                                if($(card).attr("data-tags").indexOf(value) >= 0)
                                    i++;
                            });
                        }

                        if(i === counter)
                            $(card).show();

                    });

                }else{

                    $(".medias_list_container_cards .card").show();

                }

            })


        }else{
            this.__btnFiltre.off('click.chechboxFilterSelect');
        }
    }


    enable(){
        this.filtreSelectMediaCard(true);
    }

    disable(){
        this.filtreSelectMediaCard(false);
    }

}

export default filtreMediaCard;