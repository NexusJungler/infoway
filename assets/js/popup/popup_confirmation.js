class popupConfirmation{

    enable(){
        this.onclickOpenPopupComfirmationDelete(true);
        this.onclickOpenPopupComfirmationEdit(true);
        this.onClickNameInputShow(true);
        this.onClickOpenComfirmationDuplicate(true);
        // this.onEachInputVerificationShowButton( true);
    }

    disable(){
        this.onclickOpenPopupComfirmationDelete(false);
        this.onclickOpenPopupComfirmationEdit(false);
        this.onClickNameInputShow(false);
        this.onClickOpenComfirmationDuplicate(false);
        // this.onEachInputVerificationShowButton(false);
    }

    nameInputShowfunction(){
         let nameInputShow = [];

        $.each($("#checkbox_verification input[type='checkbox']:checked"), (index, input) => {
            let selectedNameInput = $(input).parents("tr").find(".nom").text();
            if (nameInputShow .indexOf(selectedNameInput) === -1) {
                nameInputShow .push(selectedNameInput);
            }
        });

        nameInputShow.forEach(nameInputShow => {
            if ($(`.content-modal .selected-tags-list p[text="${nameInputShow}"]`).length === 0) {
                $("<p>" + nameInputShow + "</p>").appendTo($(".content-modal .selected-name-list"));
            }
        });
    }

    onclickOpenPopupComfirmationDelete(active){
        if(active){

            $(".popup_comfirmation_delete").on('click.onclickOpenPopupComfirmationDelete', () => {
                $('.add_popup_confirmation_delete').addClass('is-open');

                this.nameInputShowfunction();

                return false;
            })

        }else {

            $(".popup_comfirmation_delete").off('click.onclickOpenPopupComfirmationDelete');

        }
    }

    onclickOpenPopupComfirmationEdit(active){
        if(active){

            $(".popup_comfirmation_edit").on('click.onclickOpenPopupComfirmationEdit', () => {
                $('.add_popup_confirmation_edit').addClass('is-open');

                this.nameInputShowfunction();

                return false;
            })

        }else {

            $(".popup_comfirmation_edit").off('click.onclickOpenPopupComfirmationEdit');

        }
    }

    onClickOpenComfirmationDuplicate(active) {
        if(active){

            $('#duplicate').on('click.onClickOpenComfirmationDuplicate', () =>{
                $('.popup_product_duplicate').addClass('is-open');
                $('body').addClass('overflow_hide');

                this.nameInputShowfunction();

                return false;
            })

        }else{

            $('#duplicate').off('click.onClickOpenComfirmationDuplicate');

        }
    }


    onClickNameInputShow(active){
        if(active){
            let nameInputShow = [];

            $(".close_popup_confirmation_delete").on('click.onClickNameInputShow', () => {
                $('.add_popup_confirmation_delete').removeClass('is-open');

                $(".content-modal .selected-name-list ").empty();
                nameInputShow = [];

                return false;
            })

            $(".close_popup_confirmation_edit").on('click.onClickNameInputShow', () => {
                $('.add_popup_confirmation_edit').removeClass('is-open');

                $(".content-modal .selected-name-list ").empty();
                nameInputShow = [];

                return false;
            })

            $('.popup_product_duplicate_close').on('click.onClickNameInputShow', function(){
                $('.popup_product_duplicate').removeClass('is-open');
                $('body').removeClass('overflow_hide');

                $(".content-modal .selected-name-list ").empty();
                nameInputShow = [];

                return false;
            })


        }else {
            $(".close_popup_confirmation_delete").off('click.onClickNameInputShow');
            $(".close_popup_confirmation_edit").off('click.onClickNameInputShow');
            $(".popup_product_duplicate_close").off('click.onClickNameInputShow');
        }
    }

    // onEachInputVerificationShowButton(active){
    //     if(active){
    //
    //         $('#checkbox_verification input[type=\'checkbox\']:checked').each('.onEachInputVerificationShowButton', () =>{
    //
    //             if ($(this).prop("checked")) {
    //                 console.log("test");
    //                 $(".close_popup_confirmation_edit").removeClass("hide-btn");
    //                 $(".close_popup_confirmation_delete").removeClass("hide-btn");
    //                 $(".close_popup_confirmation_duplicate").removeClass("hide-btn");
    //             }
    //             console.log("test")
    //             return false;
    //         })
    //
    //     }else{
    //
    //         $('#duplicate').off('click.onClickOpenComfirmationDuplicate');
    //
    //     }
    // }


}

export default popupConfirmation;
