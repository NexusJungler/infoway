import SubTool from "../../SubTool";

class MediaDuplicatingButtonHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__mediasToDuplicate = [];
        this.__$container = $(".popup_duplicate_medias_container");
        this.__$location = $(".popup_duplicate_medias");
        this.__btnLocation = "";
        this.__mediaType = "";
    }

    onClickOnDuplicateButton(active)
    {

        if(active)
        {

            $('.duplicate_media_btn ').on('click.onClickOnDuplicateButton', e => {

                let mediaToDuplicateNames = [];

                let mediaId = null;

                if( $('.medias_list_container').length > 0 )
                {

                    this.__parent.getMediasContainer().find('.select_media_input_container .select_media_input:checked').each( (index, input) => {

                        if($(input).parents('.card').hasClass('synchro'))
                        {
                            this.__mediaType = "synchro";
                            mediaId = $(input).parents('.card').attr('id').replace('card_', '');
                            mediaToDuplicateNames.push( $(input).parents('.card').find('.synchro_name_container .synchro_name').text() );
                        }
                        else
                        {
                            this.__mediaType = "media";
                            mediaId = $(input).parents('.card').attr('id').replace('card_', '');
                            mediaToDuplicateNames.push( $(input).parents('.card').find('.media_name_container .media_name').text() );
                        }

                        this.__mediasToDuplicate.push( mediaId );

                    } )

                }
                else
                {

                    this.__btnLocation = "editPage";
                    mediaToDuplicateNames.push( $('.media_name_container .media_name').val() );
                    mediaId = $('.media_miniature_container').data('media_id');
                    this.__mediasToDuplicate.push( mediaId );

                }

                mediaToDuplicateNames.forEach( mediaName => {

                    $(`<li>${mediaName}</li>`).appendTo( this.__$location.find('.media_to_duplicate_list_container .media_to_duplicate_list') );

                } );

                this.__$container.addClass('is_open');

            })

        }
        else
        {
            $('.duplicate_media_btn ').off('click.onClickOnDuplicateButton')
        }

        return this;
    }

    onClickOnConfirmationButton(active)
    {

        if(active)
        {
            this.__$location.find('.media_duplicating_confirmation_btn').on('click.onClickOnConfirmationButton', e => {

                console.log(this.__mediasToDuplicate); //debugger

                let mediaDuplicated = 0;
                let mediasToDuplicatingNumber = this.__mediasToDuplicate.length;

                super.changeLoadingPopupText( "Duplication du ou des media(s) en cours..." );
                super.showLoadingPopup();

                $.ajax({
                    url: (this.__mediaType === "media") ? `/duplicate/media` : `/duplicate/synchro`,
                    type: "POST",
                    data: (this.__mediaType === "media") ? { mediasToDuplicateId: this.__mediasToDuplicate } : { synchrosToDuplicateId: this.__mediasToDuplicate },
                    success: (response) => {

                        if(response.status === "200 OK")
                        {

                            this.duplicateCards(response.ids);

                            mediaDuplicated++;

                            if(mediaDuplicated === mediasToDuplicatingNumber)
                            {

                                this.resetPopup();

                                this.__$container.removeClass('is_open');

                                if(this.__parent.getMediasContainer().find(".select_media_input:checked").length === 0)
                                    $('.media_action_btn').attr('disabled', true);

                            }

                        }
                        else
                        {
                            //console.log(response); debugger
                            if(this.__mediaType === "media")
                                alert(`Erreur durant la duplication des du ou des media(s)`)

                            else
                                alert(`Erreur durant la duplication des du ou des synchro(s)`)

                        }

                    },
                    error: (response, status, error) => {

                        //console.error(response); debugger
                        if(this.__mediaType === "media")
                            alert(`Erreur durant la duplication des du ou des media(s)`)

                        else
                            alert(`Erreur durant la duplication des du ou des synchro(s)`)

                    },
                    complete: () => {

                        super.hideLoadingPopup();

                    }
                });

            })
        }
        else
        {
            this.__$location.find('.media_duplicating_confirmation_btn').off('click.onClickOnConfirmationButton');
        }

        return this;

    }

    /**
     *
     * @param {int[]} newIds
     */
    duplicateCards(newIds)
    {

        this.__mediasToDuplicate.forEach( (mediaToDuplicateId, index) => {

            let duplicateCard = $(`#card_${mediaToDuplicateId}`).clone();

            let oldMediaName = duplicateCard.find('.media_name').text() || duplicateCard.find('.synchro_name').text();

            duplicateCard.attr('id', `card_${newIds[index].id}`);
            duplicateCard.html( duplicateCard.html().replace( (new RegExp(mediaToDuplicateId, "gi")), newIds[index].id ) );
            duplicateCard.html( duplicateCard.html().replace( (new RegExp(oldMediaName, "gi")), newIds[index].name ) );

/*            console.log(oldMediaName);
            console.log(duplicateCard.html());
            console.log(duplicateCard);debugger;*/

            duplicateCard.appendTo( $(".medias_list_container") );

        } )

    }

    resetPopup()
    {
        this.__mediasToDuplicate = [];
        this.__$location.find('.media_to_duplicate_list_container .media_to_duplicate_list').empty();
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

    enable()
    {
        super.enable();
        this.onClickOnDuplicateButton(true)
            .onClickOnPopupCloseButton(true)
            .onClickOnConfirmationButton(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnDuplicateButton(false)
            .onClickOnPopupCloseButton(false)
            .onClickOnConfirmationButton(false)
        ;
    }

}

export default MediaDuplicatingButtonHandler;