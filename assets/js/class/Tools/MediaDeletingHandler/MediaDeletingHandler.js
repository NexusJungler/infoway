import Tool from "../Tool";

class MediaDeletingHandler extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__mediasToDelete = [];
        this.__$container = $(".popup_delete_medias_container");
        this.__$location = $(".popup_delete_medias");
    }

    onMediaSelectionAndDeselectionChangeMediaActionsButtonsState(active)
    {
        if(active)
        {
            $(".medias-list-container").on('change.onMediaSelectionAndDeselectionChangeDeleteButtonState', ".select-media-input", e => {

                if($(".select-media-input-container .select-media-input:checked").length === 0)
                {
                    $('.media_action_btn').attr('disabled', true);
                }

                else
                {
                    $('.media_action_btn').removeAttr('disabled');
                }

            })
        }
        else
        {
            $(".medias-list-container").off('change.onMediaSelectionAndDeselectionChangeDeleteButtonState', ".select-media-input");
        }

        return this;
    }

    onClickOnDeletingButtonShowPopup(active)
    {

        if(active)
        {
            $('.delete_media_btn').on('click.onClickOnDeletingButtonShowPopup', e => {

                if($(".select-media-input-container .select-media-input:checked").length > 0)
                {
                    $(".select-media-input-container .select-media-input:checked").each( (index, input) => {

                        const mediaId = $(input).parents('.card').attr('id').replace('media_', '');
                        const mediaName = $(input).parents('.card').find('.media-name-container .media-name').text();

                        $(`<li>${mediaName}</li>`).appendTo( this.__$location.find('.media_to_delete_list_container .media_to_delete_list') );

                        this.__mediasToDelete.push({ id: mediaId });

                    } )

                    this.__$container.addClass('is_open');
                }

            })
        }
        else
        {
            $('.delete_media_btn').off('click.onClickOnDeletingButtonShowPopup');
        }

        return this;
    }

    onClickOnConfirmationButtonDeleteMedia(active)
    {
        if(active)
        {
            this.__$location.find('.media_deleting_confirmation_btn').on('click.onClickOnConfirmationButtonDeleteMedia', e => {

                let mediaDeleted = 0;
                let mediasToDeleteNumber = this.__mediasToDelete.length;

                this.__mediasToDelete.forEach( (mediaToDelete) => {

                    const mediaId = mediaToDelete.id;

                    $.ajax({
                       url: '/remove/media',
                        type: "POST",
                        data: {media: mediaId},
                        success: (response) => {

                           $(`#media_${mediaId}`).remove();
                           mediaDeleted++;

                            if(mediaDeleted === mediasToDeleteNumber)
                            {
                                this.resetPopup();

                                this.__$container.removeClass('is_open');
                            }

                        },
                        error: (response, status, error) => {

                            console.error(response); //debugger
                            alert(`Erreur durant la suppression du media '${mediaId}'`)

                        },
                    });

                } );

            })
        }
        else
        {
            this.__$location.find('.media_deleting_confirmation_btn').off('click.onClickOnConfirmationButtonDeleteMedia');
        }

        return this;
    }



    onClickOnPopupCloseButton(active)
    {
        if(active)
        {
            this.__$location.find('.close_modal_button').on('click.onClickOnPopupCloseButton', e => {

                this.resetPopup();

                this.__$container.removeClass('is_open');

            })

        }
        else
        {
            this.__$location.find('.close_modal_button').off('click.onClickOnPopupCloseButton');
        }

        return this;
    }

    resetPopup()
    {
        this.__mediasToDelete = [];
        this.__$location.find('.media_to_delete_list_container .media_to_delete_list').empty();
    }


    enable()
    {
        super.enable();
        this.onMediaSelectionAndDeselectionChangeMediaActionsButtonsState(true)
            .onClickOnDeletingButtonShowPopup(true)
            .onClickOnPopupCloseButton(true)
            .onClickOnConfirmationButtonDeleteMedia(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onMediaSelectionAndDeselectionChangeMediaActionsButtonsState(false)
            .onClickOnDeletingButtonShowPopup(false)
            .onClickOnPopupCloseButton(false)
            .onClickOnConfirmationButtonDeleteMedia(false)
        ;
    }

}

export default MediaDeletingHandler;