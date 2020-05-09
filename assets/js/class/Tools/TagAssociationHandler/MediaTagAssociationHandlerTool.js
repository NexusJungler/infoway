import Tool from "../Tool";

class MediaTagAssociationHandlerTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$mediasCollection = $('.medias_collection');
        this.__$location = $('.associate_tag-popup');
        this.__$tagsList = $('.tags-list');
        this.__currentMedia = null;
        this.__mediasAssociationInfo = [];
    }

    onClickOnTagAssociationButtonShowModal(active)
    {

        if(active)
        {

            $('.modal .edit_media_info').on("click.onClickOnTagAssociationButtonShowModal", ".associate-tag", e => {

                $('.add-popup').css({ 'z-index': '0' });
                this.__currentMedia = $(e.currentTarget).data('media');

                // check if media is already associated with tag (in this case, update popup)
                let registeredMediaInfosIndex = this.__mediasAssociationInfo.findIndex( mediaInfo =>  mediaInfo.media === this.__currentMedia );
                if(registeredMediaInfosIndex !== -1)
                {
                    this.__mediasAssociationInfo[ registeredMediaInfosIndex ].tags.forEach( (id, index) => {

                        this.__$tagsList.find(`tr[data-tag_id='${ id }'] .choice_tag`).prop('checked', true);

                    } )
                }

                this.__$location.find('.modal-title-container .media_name').text( this.__currentMedia );
                this.__$location.fadeIn();

            })

        }
        else
        {
            $('.modal .edit_media_info').off("click.onClickOnTagAssociationButtonShowModal", ".associate-tag");
        }

        return this;
    }

    onClickOnCloseButtonCloseTagAssociationModal(active)
    {

        if(active)
        {
            this.__$location.find('.close').on('click.onClickOnCloseButtonCloseTagAssociationModal', e => {

                $('.add-popup').css({ 'z-index': '' });
                $(".associate_tag-popup").fadeOut();

                // reset
                this.__$location.find('.modal-body .choice_tag').prop('checked', false);

                if( $(e.currentTarget).hasClass('cancel') )
                {
                    this.updateMediaAssociatedTags( { media: this.__currentMedia, products: [] } );
                }

            })
        }
        else
        {
            this.__$location.find('.close').off('click.onClickOnCloseButtonCloseTagAssociationModal');
        }

        return this;
    }

    onClickOnValidationAssociateTagsOnProduct(active)
    {

        if(active)
        {
            this.__$location.find('.validation-btn').on('click.onClickOnValidationAssociateTagsOnProduct', e => {

                let tagsToMedia = [];
                this.__$tagsList.find('.choice_tag').each( (index, element) => {

                    if( $(element).is(':checked') )
                    {
                        tagsToMedia.push( $(element).parents('tr').data('tag_id') );

                        if( $(`.edit_media_info .associated-tags-container span[data-tag_id='${ $(element).parents('tr').data('tag_id') }']`).length === 0 )
                        {
                            $(`<span>`, {
                                text: $(element).parents('tr').find('.tag-name').text(),
                            }).attr('data-tag_id', $(element).parents('tr').data('tag_id') )
                                .appendTo( $('.edit_media_info .associated-tags-container') );
                        }
                    }
                    else
                    {
                        $(`.edit_media_info .associated-tags-container span[data-tag_id='${ $(element).parents('tr').data('tag_id') }']`).remove();
                    }


                } );

                this.updateMediaAssociatedTags( { media: this.__currentMedia, tags: tagsToMedia } );

            })
        }
        else
        {
            this.__$location.find('.validation-btn').off('click.onClickOnValidationAssociateTagsOnProduct');
        }

        return this;
    }

    updateMediaAssociatedTags(mediaInfos)
    {

        let registeredMediaInfosIndex = this.__mediasAssociationInfo.findIndex( mediaInfo =>  mediaInfo.media === mediaInfos.media );

        if( registeredMediaInfosIndex === -1 )
            this.__mediasAssociationInfo.push( mediaInfos );

        else
            this.__mediasAssociationInfo[ registeredMediaInfosIndex ].tags = mediaInfos.tags;

        let mediaIndex = this.__$mediasCollection.find(`.media_name[value='${ mediaInfos.media }']`).parents('li').data('index');

        $(`#medias_list_medias_${mediaIndex}_tags input[type='checkbox']`).each( (index, input) => {

            $(input).attr('checked', ( mediaInfos.tags.indexOf( parseInt($(input).val()) ) !== -1 ) );

        } )

    }



    enable() {
        super.enable();
        this.onClickOnTagAssociationButtonShowModal(true)
            .onClickOnCloseButtonCloseTagAssociationModal(true)
            .onClickOnValidationAssociateTagsOnProduct(true)
        ;
    }

    disable() {
        super.disable();
        this.onClickOnTagAssociationButtonShowModal(false)
            .onClickOnCloseButtonCloseTagAssociationModal(false)
            .onClickOnValidationAssociateTagsOnProduct(false)
        ;
    }

}

export default MediaTagAssociationHandlerTool;