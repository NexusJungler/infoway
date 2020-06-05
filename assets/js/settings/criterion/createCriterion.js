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
                if ($(this).is(":checked")) {
                    $(".row-define-criterion").addClass("active");
                }else{
                    $(".row-define-criterion").removeClass("active");
                }
            })
            
        }else{
            $("#criterions_list_basicCriterionUsed input[type=radio]").off('click.onClickShowCriterionBase');
        }
    }



    
}

export default createCriterion;