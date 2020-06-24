import Tool from "../../Tool";
import SubTool from "../../SubTool";

class MediaDeletingHandler extends SubTool
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
            this.__parent.getMediasContainer().on('change.onMediaSelectionAndDeselectionChangeDeleteButtonState', ".select_media_input", e => {

                if(this.__parent.getMediasContainer().find(".select_media_input:checked").length === 0)
                    $('.media_action_btn').attr('disabled', true);

                else
                    $('.media_action_btn').removeAttr('disabled');

            })
        }
        else
        {
            this.__parent.getMediasContainer().off('change.onMediaSelectionAndDeselectionChangeDeleteButtonState', ".select_media_input");
        }

        return this;
    }

    onClickOnDeletingButtonShowPopup(active)
    {

        if(active)
        {
            $('.delete_media_btn').on('click.onClickOnDeletingButtonShowPopup', e => {

                if(this.__parent.getMediasContainer().find('.select_media_input_container .select_media_input:checked').length > 0)
                {
                    this.__parent.getMediasContainer().find('.select_media_input_container .select_media_input:checked').each( (index, input) => {

                        const mediaId = $(input).parents('.card').attr('id').replace('media_', '');
                        const mediaName = $(input).parents('.card').find('.media_name_container .media_name').text();

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

                $('.popup_loading_container').css({ 'z-index': 100000 }).addClass('is_open');

                this.__mediasToDelete.forEach( (mediaToDelete) => {

                    const mediaId = mediaToDelete.id;

                    $.ajax({
                       url: `/remove/media/${mediaId}`,
                        type: "POST",
                        data: {},
                        success: (response) => {

                           $(`#media_${mediaId}`).remove();
                           mediaDeleted++;

                            if(mediaDeleted === mediasToDeleteNumber)
                            {

                                $('.popup_loading_container').css({ 'z-index': '' }).removeClass('is_open');

                                this.resetPopup();

                                this.__$container.removeClass('is_open');
                            }

                        },
                        error: (response, status, error) => {

                            $('.popup_loading_container').css({ 'z-index': '' }).removeClass('is_open');

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