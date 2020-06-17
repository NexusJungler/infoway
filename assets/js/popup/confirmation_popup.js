class confirmationPopup{

    enable(){
        this.onClickShowCriterionBase(true);
        this.onClickOpenPopup(true);
    }
    disable(){
        this.onClickShowCriterionBase(false);
        this.onClickOpenPopup(false);

    }

    onclickshowPopupComfirmationPopup(active){
        if(active){

        }else {

        }
    }

    onClickShowCriterionBase(active) {
        if(active){
            $("#criterions_list_basicCriterionUsed input[type=radio]").on('click.onClickShowCriterionBase', function(){
                if ($(this).val() === "1" ) {
                    $(".row-define-criterion").addClass("active");
                }else{
                    $(".row-define-criterion").removeClass("active");
                    $("#criterions_list_basicCriterionUsed_1").is(':checked');
                }
            })

        }else{
            $("#criterions_list_basicCriterionUsed input[type=radio]").off('click.onClickShowCriterionBase');
        }
    }

    onClickOpenPopup(active) {
        if(active){
            $('.btn-open-popup_product').on('click.onClickOpenPopup', function(){
                $('.add-popup-produits').addClass('is-open');
                return false;
            })
            $('.btn-open-popup_site').on('click.onClickOpenPopup', function(){
                $('.add-popup-site').addClass('is-open');
                return false;
            })

        }else{
            $(".btn-open-popup_product").off('click.onClickOpenPopup');
            $(".btn-open-popup_site").off('click.onClickOpenPopup');
        }
    }

}

export default confirmationPopups;
