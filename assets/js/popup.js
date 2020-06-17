class popup{

    enable(){
        this.onClickOpenConfimationPopup(true);
    }
    disable(){
        this.onClickOpenConfimationPopup(false);

    }

    onClickOpenConfimationPopup(active) {
        if(active){
            $('.btn-popup-delete-criterion').on('click.onClickOpenPopup', function(){
                $('.add-popup-delete').addClass('is-open');
                return false;
            })

        }else{
            $(".btn-popup-delete-criterion").off('click.onClickOpenPopup');
        }
    }

}

export default popup;



