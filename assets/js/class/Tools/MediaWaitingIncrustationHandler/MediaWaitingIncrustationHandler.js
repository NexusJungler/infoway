import Tool from "../Tool";

class MediaWaitingIncrustationHandler extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_medias_waiting_incrustation_container');
        this.__$location = $('.popup_medias_waiting_incrustes');
        this.__$mediasList = $('.archived_medias_list');
        this.__mediasNamesRegistered = [];
    }

    getAllMediasNamesRegistered()
    {
        return this.__mediasNamesRegistered;
    }

    registerAllMediasNames()
    {
        this.__$mediasList.find('.media_name_edit_input').each( (index, media_name_input) => {

            media_name_input = $(media_name_input);

            const mediaName = media_name_input.val();

            if(mediaName === '')
                media_name_input.addClass('invalid');

            else
            {

                if(!this.mediaNameIsAlreadyRegistered(mediaName))
                {
                    this.registerMediaName(media_name_input.data('media'), mediaName)
                    console.log(this.getAllMediasNamesRegistered())
                }
                else
                {
                    media_name_input.addClass('invalid');
                }

            }

        } )

        return this;
    }

    registerMediaName(mediaId, mediaName)
    {
        this.__mediasNamesRegistered.push( {media: mediaId, value: mediaName } );
    }

    mediaNameIsAlreadyRegistered(mediaName)
    {
        return this.getRegisteredMediaNameIndex(mediaName) !== -1;
    }

    getRegisteredMediaNameIndex(mediaName)
    {
        return this.__mediasNamesRegistered.findIndex( mediaNameRegistered => mediaNameRegistered.value === mediaName );
    }

    onClickOnWaitingListButtonOpenModal(active)
    {
        if(active)
        {

            $('#show-media-waiting-incrustation').on('click.onClickOnWaitingListButtonOpenModal', e => {

                this.__$container.addClass('is_open');

            })
        }
        else
        {
            $('#show-media-waiting-incrustation').off('click.onClickOnWaitingListButtonOpenModal');
        }

        return this;
    }

    onClickOnPopupCloseButton(active)
    {
        if(active)
        {
            this.__$location.find('.close_modal_button').on('click.onClickOnPopupCloseButton', e => {

                if(this.__$mediasList.find('.media_name_edit_input.unregistered').length > 0)
                {

                    if(confirm("Certaines de vos modifications n'ont pas enregistrés. Voulez-vous vraiment fermer cet fenetre et perdres vos modifications ?"))
                        this.__$container.removeClass('is_open');

                }

                else
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