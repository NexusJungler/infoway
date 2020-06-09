class createCriterion{

    enable(){
        this.onClickShowCriterionBase(true);
    }
    disable(){
        this.onClickShowCriterionBase(false);
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

}

export default createCriterion;