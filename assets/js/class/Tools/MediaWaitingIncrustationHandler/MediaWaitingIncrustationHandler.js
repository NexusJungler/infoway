import Tool from "../Tool";

class MediaWaitingIncrustationHandler extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
    }

    onClickOnWaitingListButtonOpenModal(active)
    {
        if(active)
        {

            $('#show-media-waiting-incrustation').on('click.onClickOnWaitingListButtonOpenModal', e => {

                if($('.popup_medias_waiting_incrustation_container').hasClass('is_open'))
                    $('.popup_medias_waiting_incrustation_container').removeClass('is_open');

                else
                    $('.popup_medias_waiting_incrustation_container').addClass('is_open');

            })
        }
        else
        {
            $('#show-media-waiting-incrustation').off('click.onClickOnWaitingListButtonOpenModal');
        }

        return this;
    }

    enable()
    {
        super.enable();
        this.onClickOnWaitingListButtonOpenModal(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnWaitingListButtonOpenModal(false)
        ;
    }

}

export default MediaWaitingIncrustationHandler;