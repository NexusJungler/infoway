import Tool from "../../Tool";
import SubTool from "../../SubTool";

class MediaDeletingButtonHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__mediasToDelete = [];
        this.__$container = $(".popup_delete_medias_container");
        this.__$location = $(".popup_delete_medias");
    }

    onClickOnDeletingButtonShowPopup(active)
    {

        if(active)
        {
            $('.delete_media_btn').on('click.onClickOnDeletingButtonShowPopup', e => {

                const mediaName = $('.media_name_container .media_name').text() || $('.media_name_container .media_name').val();

                $(`<li>${mediaName}</li>`).appendTo( this.__$location.find('.media_to_delete_list_container .media_to_delete_list') );

                let mediaId = null;

                if( $('.medias_list_container').length > 0 )
                {

                    this.__parent.getMediasContainer().find('.select_media_input_container .select_media_input:checked').each( (index, input) => {

                        mediaId = $(input).parents('.card').attr('id').replace('media_', '');
                        this.__mediasToDelete.push({ id: mediaId });

                    } )

                }
                else
                {
                    mediaId = $('.media_miniature_container').data('media_id');
                    this.__mediasToDelete.push({ id: mediaId });
                }

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
        this.onClickOnDeletingButtonShowPopup(true)
            .onClickOnPopupCloseButton(true)
            .onClickOnConfirmationButtonDeleteMedia(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnDeletingButtonShowPopup(false)
            .onClickOnPopupCloseButton(false)
            .onClickOnConfirmationButtonDeleteMedia(false)
        ;
    }

}

export default MediaDeletingButtonHandler;