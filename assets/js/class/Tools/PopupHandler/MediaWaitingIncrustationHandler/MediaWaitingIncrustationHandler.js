import Tool from "../../Tool";
import SubTool from "../../SubTool";

class MediaWaitingIncrustationHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_medias_waiting_incrustation_container');
        this.__$location = $('.popup_medias_waiting_incrustation');
        this.__$mediasList = $('.medias_waiting_incrustation_list');
    }

    onClickOnWaitingListButtonOpenModal(active)
    {
        if(active)
        {

            $('#show_media_waiting_incrustation').on('click.onClickOnWaitingListButtonOpenModal', e => {

                this.__$container.addClass('is_open');

            })
        }
        else
        {
            $('#show_media_waiting_incrustation').off('click.onClickOnWaitingListButtonOpenModal');
        }

        return this;
    }

    onClickOnPopupCloseButton(active)
    {
        if(active)
        {
            this.__$location.find('.close_modal_button').on('click.onClickOnPopupCloseButton', e => {

                this.__$container.removeClass('is_open');

            })
        }
        else
        {
            this.__$location.find('.close_modal_button').off('click.onClickOnPopupCloseButton');
        }

        return this;
    }

    onMediaSelectionAndDeselection(active)
    {
        if(active)
        {
            this.__$mediasList.find('.select_media').on('change.onMediaSelectionAndDeselection', e => {

                const mediasSelectedNumber = $('.select_media:checked').length;

                //console.log(mediasSelectedNumber); debugger

                if(mediasSelectedNumber === 0)
                {
                    if(!this.__$location.find('.redirect_to_module_incruste_button').hasClass('disabled'))
                        this.__$location.find('.redirect_to_module_incruste_button').addClass('disabled');

                    this.__$location.find('.redirect_to_module_incruste_button').prop('disabled', true);
                }

                else
                {
                    //console.log("remove"); debugger
                    this.__$location.find('.redirect_to_module_incruste_button').removeClass('disabled');
                    this.__$location.find('.redirect_to_module_incruste_button').prop('disabled', false);
                }



            })
        }
        else
        {
            this.__$mediasList.find('.select_media').off('change.onMediaSelectionAndDeselection');
        }

        return this;
    }


    enable()
    {
        super.enable();
        this.onClickOnWaitingListButtonOpenModal(true)
            .onClickOnPopupCloseButton(true)
            .onMediaSelectionAndDeselection(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnWaitingListButtonOpenModal(false)
            .onClickOnPopupCloseButton(false)
            .onMediaSelectionAndDeselection(false)
        ;
    }

}

export default MediaWaitingIncrustationHandler;