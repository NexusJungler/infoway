import Tool from "../../Tool";
import SubTool from "../../SubTool";

class MediaDeletingButtonHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__mediasToDelete = [];
        this.__$container = $(".popup_delete_container");
        this.__$location = $(".popup_delete");
        this.__btnLocation = "";
        this.__mediaType = "";
    }

    onClickOnDeletingButtonShowPopup(active)
    {

        if(active)
        {
            $('.delete_media_btn').on('click.onClickOnDeletingButtonShowPopup', e => {

                let mediaToDeleteNames = [];

                let mediaId = null;

                if( $('.medias_list_container').length > 0 )
                {

                    this.__btnLocation = "mediatheque";

                    this.__parent.getMediasContainer().find('.select_media_input_container .select_media_input:checked').each( (index, input) => {

                        if($(input).parents('.card').hasClass('synchro'))
                        {
                            this.__mediaType = "synchro";
                            mediaId = $(input).parents('.card').attr('id').replace('card_', '');
                            mediaToDeleteNames.push( $(input).parents('.card').find('.synchro_name_container .synchro_name').text() );
                        }
                        else
                        {
                            this.__mediaType = "media";
                            mediaId = $(input).parents('.card').attr('id').replace('card_', '');
                            mediaToDeleteNames.push( $(input).parents('.card').find('.media_name_container .media_name').text() );
                        }

                        this.__mediasToDelete.push({ id: mediaId });

                    } )

                }
                else
                {

                    this.__btnLocation = "editPage";

                    mediaToDeleteNames.push( $('.media_name_container .media_name').val() );
                    mediaId = $('.media_miniature_container').data('media_id');
                    this.__mediasToDelete.push({ id: mediaId });
                }

                //console.log(mediaToDeleteNames); debugger;

                mediaToDeleteNames.forEach( mediaName => {

                    $(`<li>${mediaName}</li>`).appendTo( this.__$location.find('.delete_list_container .delete_list') );

                } );

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
            this.__$location.find('.deleting_confirmation_btn').on('click.onClickOnConfirmationButtonDeleteMedia', e => {

                let mediaDeleted = 0;
                let mediasToDeleteNumber = this.__mediasToDelete.length;

                super.changeLoadingPopupText("Suppression du ou des média(s)...");
                super.showLoadingPopup();

                this.__mediasToDelete.forEach( (mediaToDelete, index) => {

                    const mediaId = mediaToDelete.id;

                    //super.showLoadingPopup();

                    $.ajax({
                       url: (this.__mediaType === "media") ? `/remove/media/${mediaId}` : `/remove/synchro/${mediaId}`,
                       type: "POST",
                       data: {},
                       success: (response) => {

                           if(response.status === "200 OK")
                           {

                               if(this.__btnLocation === "editPage")
                                   location.href = "/mediatheque/medias";

                               else
                               {

                                   $(`#card_${mediaId}`).remove();

                                   mediaDeleted++;

                                   if(mediaDeleted === mediasToDeleteNumber)
                                   {

                                       this.resetPopup();

                                       this.__$container.removeClass('is_open');

                                       if(this.__parent.getMediasContainer().find(".select_media_input:checked").length === 0)
                                           $('.media_action_btn').attr('disabled', true);

                                   }

                               }

                           }
                           else
                           {
                               console.log(response); debugger
                               alert(`Erreur durant la suppression du media n°${mediaId}`)
                           }

                        },
                       error: (response, status, error) => {

                            console.error(response); debugger
                            alert(`Erreur durant la suppression du media n°${mediaId}`)

                        },
                       complete: () => {

                           super.hideLoadingPopup();

                        }
                    });

                } );

            })
        }
        else
        {
            this.__$location.find('.deleting_confirmation_btn').off('click.onClickOnConfirmationButtonDeleteMedia');
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
        this.__$location.find('.delete_list_container .delete_list').empty();
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