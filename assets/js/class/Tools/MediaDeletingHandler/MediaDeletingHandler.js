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

    onMediaSelectionAndDeselectionChangeDeleteButtonState(active)
    {
        if(active)
        {
            $(".select-media-input-container .select-media-input").on('change.onMediaSelectionAndDeselectionChangeDeleteButtonState', e => {

                if($(".select-media-input-container .select-media-input:checked").length === 0)
                    $('.delete_media_btn').attr('disabled', true);

                else
                    $('.delete_media_btn').removeAttr('disabled');

            })
        }
        else
        {
            $(".select-media-input-container .select-media-input:checked").off('change.onMediaSelectionAndDeselectionChangeDeleteButtonState');
        }

        return this;
    }

    onClickOnDeletingButtonShowPopup(active)
    {

        if(active)
        {
            $('.delete_media_btn').on('click.onClickOnDeletingButtonShowPopup', e => {

                $(".select-media-input-container .select-media-input:checked").each( (index, input) => {

                    const mediaId = $(input).parents('.card').attr('id').replace('media_', '');
                    const mediaName = $(input).parents('.card').find('.media-name-container .media-name').text();

                    $(`<li>${mediaName}</li>`).appendTo( this.__$location.find('.media_to_delete_list_container .media_to_delete_list') );

                    this.__mediasToDelete.push(mediaId);

                } )

                this.__$container.addClass('is_open');

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
            this.__$location.find('.media_deleting_confirmation_btn').on('click.onClickOnConfirmationButtonDeleteMedia', async(e) => {

                const allMediaIsSuccessfullyDeleted = await this.allMediasToDeleteIsDeleted();

                console.log(allMediaIsSuccessfullyDeleted); debugger

                if(allMediaIsSuccessfullyDeleted)
                {
                    this.resetPopup();

                    this.__$container.removeClass('is_open');
                }

            })
        }
        else
        {
            this.__$location.find('.media_deleting_confirmation_btn').off('click.onClickOnConfirmationButtonDeleteMedia');
        }

        return this;
    }

    allMediasToDeleteIsDeleted()
    {

        return new Promise( (resolve, reject) => {

            let mediaDeleted = 0;
            let mediasToDeleteNumber = this.__mediasToDelete.length;

            $.ajax({
                url: '/remove/media',
                type: "POST",
                data: {media: JSON.stringify(this.__mediasToDelete)},
                success: (response) => {

                    //$(`#media_${mediaId}`).remove();
                    mediaDeleted++;

                },
                error: (response, status, error) => {

                    console.error(response); //debugger
                    alert(`Erreur durant la suppression du media '${mediaId}'`)

                },
            });

            resolve( (mediaDeleted === mediasToDeleteNumber) );

        } )

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
        this.onMediaSelectionAndDeselectionChangeDeleteButtonState(true)
            .onClickOnDeletingButtonShowPopup(true)
            .onClickOnPopupCloseButton(true)
            .onClickOnConfirmationButtonDeleteMedia(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onMediaSelectionAndDeselectionChangeDeleteButtonState(false)
            .onClickOnDeletingButtonShowPopup(false)
            .onClickOnPopupCloseButton(false)
            .onClickOnConfirmationButtonDeleteMedia(false)
        ;
    }

}

export default MediaDeletingHandler;