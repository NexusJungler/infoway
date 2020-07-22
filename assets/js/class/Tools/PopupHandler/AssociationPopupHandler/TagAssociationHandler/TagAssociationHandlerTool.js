import Tool from "../../../Tool";
import SubTool from "../../../SubTool";

class TagAssociationHandlerTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$mediasCollection = $('.medias_collection');
        this.__$container = $('.popup_associate_tag_container');
        this.__$location = $('.popup_associate_tag');
        this.__$tagsList = $('.tags_list');
        //this.__currentMedia = null;
        this.__currentMediaName = null;
        this.__currentMediaId = null;
        this.__mediasAssociationInfo = [];
        this.__isUpload = false;
    }

    initializePopupContent()
    {
        this.__$tagsList.find(`.choice_tag`).removeAttr('checked');
        const index = this.__mediasAssociationInfo.findIndex( mediaAssociationInfo => mediaAssociationInfo.media === this.__currentMediaName );

        if(index !== -1)
        {

            this.__mediasAssociationInfo[index].tags.forEach( (associatedTagId) => {

                this.__$tagsList.find(`tr[data-tag_id='${ associatedTagId }'] .choice_tag`).prop('checked', true)

            } );

        }

        this.__$location.find('.media_name').text( this.__currentMediaName );
        this.__$container.addClass('is_open');

    }

    onClickOnTagAssociationButtonShowModal(active)
    {

        if(active)
        {

            $('.tag_association_btn').on("click.onClickOnTagAssociationButtonShowModal", e => {

                if( $('.media_products_list').length > 0 )
                {

                    this.__currentMediaName = $('.tab_content_body_title_container .media_name').text();
                    let mediaTagsAssociatedIds = [];

                    $('.media_tags_list').find('.tag_container').each( (index, container) => {

                        if( !$(container).hasClass('hidden') )
                            mediaTagsAssociatedIds.push( $(container).data('tag_id') );

                    } )

                    this.__mediasAssociationInfo.push( { media: this.__currentMediaName, tags: mediaTagsAssociatedIds } );

                }
                else if( $('.popup_upload_container.is_open').length > 0 ) // association is used after upload
                {
                    this.__currentMediaName = $('.media_name_container .media_name').val();
                }

                this.initializePopupContent();

                /*$('.add-popup').css({ 'z-index': '0' });
                this.__currentMedia = $(e.currentTarget).data('media');
                this.__currentPos = $(e.currentTarget).parents('tr').attr('id');

                // check if media is already associated with tag (in this case, update popup)
                let registeredMediaInfosIndex = this.__mediasAssociationInfo.findIndex( mediaInfo =>  mediaInfo.media === this.__currentMedia );
                if(registeredMediaInfosIndex !== -1)
                {
                    this.__mediasAssociationInfo[ registeredMediaInfosIndex ].tags.forEach( (id, index) => {

                        this.__$tagsList.find(`tr[data-tag_id='${ id }'] .choice_tag`).prop('checked', true);

                    } )
                }

                this.__$location.find('.modal-title-container .media_name').text( this.__currentMedia );
                this.__$location.fadeIn();*/

            })

        }
        else
        {
            $('.tag_association_btn').off("click.onClickOnTagAssociationButtonShowModal");
        }

        return this;
    }

    onClickOnCloseButtonCloseTagAssociationModal(active)
    {

        if(active)
        {
            this.__$location.find('.close_modal_button').on('click.onClickOnCloseButtonCloseTagAssociationModal', e => {

                this.__$container.removeClass('is_open');

                // reset
                this.__$location.find('.choice_tag').removeAttr('checked');

                if( $(e.currentTarget).hasClass('cancel') && this.__isUpload )
                {
                    //this.updateMediaAssociatedTags( { media: this.__currentMedia, products: [] } );
                }

            })
        }
        else
        {
            this.__$location.find('.close_modal_button').off('click.onClickOnCloseButtonCloseTagAssociationModal');
        }

        return this;
    }


    onInputStateChange(active)
    {
        if(active)
        {

            this.__$tagsList.find('.choice_tag').on('change.onInputStateChange', e => {

                const tag_id = $( $(e.currentTarget) ).parents('tr').data('tag_id');
                const index = this.__mediasAssociationInfo.findIndex( mediaAssociationInfo => mediaAssociationInfo.media === this.__currentMediaName );

                if(index !== -1)
                {

                    if($(e.currentTarget).is(':checked') && $('.media_tags_list').find(`div[data-tag_id='${ tag_id }'] .tag_checkbox:checked`).length === 0)
                    {
                        $( $(e.currentTarget) ).parents('tr').addClass('new_association');
                    }
                    else if( !($(e.currentTarget).is(':checked')) && $('.media_tags_list').find(`div[data-tag_id='${ tag_id }'] .tag_checkbox:checked`).length > 0)
                    {
                        $( $(e.currentTarget) ).parents('tr').addClass('dissociated');
                    }
                    else
                    {
                        $( $(e.currentTarget) ).parents('tr').removeClass('new_association dissociated');
                    }

                    this.handleMediaInfosModification();

                }

            })
        }
        else
        {
            this.__$tagsList.find('.choice_tag').off('change.onInputStateChange');
        }

        return this;
    }


    handleMediaInfosModification()
    {

        this.__$location.find('.validate_association_btn').attr('disabled', ( (this.__$tagsList.find('tr.dissociated').length === 0)
            && (this.__$tagsList.find('tr.new_association').length === 0)  ) );

    }


    onClickOnValidationAssociateTagsOnProduct(active)
    {

        if(active)
        {
            this.__$location.find('.validate_association_btn').on('click.onClickOnValidationAssociateTagsOnProduct', e => {

                let tagsToMedia = [];
                this.__$tagsList.find('.choice_tag').each( (index, element) => {

                    const tag_id = $(element).parents('tr').data('tag_id');

                    // association is used after upload
                    if( $('.popup_upload_container.is_open').length > 0 )
                    {
                        debugger;
                    }
                    else
                    {

                        $('.media_tags_list').find(`div[data-tag_id='${ tag_id }'] .tag_checkbox`).prop('checked', $(element).is(':checked'));

                        if($(element).is(':checked'))
                            $('.media_tags_list').find(`div[data-tag_id='${ tag_id }']`).removeClass('hidden');

                        else
                            $('.media_tags_list').find(`div[data-tag_id='${ tag_id }']`).addClass('hidden');

                    }

                    this.__$location.find('.validate_association_btn').attr('disabled', true);

                    /*$(`.edit_media_info #${this.__currentPos}`).addClass('unregistered');

                    if( $(element).is(':checked') )
                    {
                        tagsToMedia.push( $(element).parents('tr').data('tag_id') );

                        if( $(`.edit_media_info #${this.__currentPos} .associated-tags-container span[data-tag_id='${ $(element).parents('tr').data('tag_id') }']`).length === 0 )
                        {
                            $(`<span>`, {
                                text: $(element).parents('tr').find('.tag-name').text(),
                            }).attr('data-tag_id', $(element).parents('tr').data('tag_id') )
                                .appendTo( $(`.edit_media_info #${this.__currentPos} .associated-tags-container`) );
                        }
                    }
                    else
                    {
                        $(`.edit_media_info #${this.__currentPos} .associated-tags-container span[data-tag_id='${ $(element).parents('tr').data('tag_id') }']`).remove();
                    }*/


                } );

                //this.updateMediaAssociatedTags( { media: this.__currentMedia, tags: tagsToMedia } );

            })
        }
        else
        {
            this.__$location.find('.validate_association_btn').off('click.onClickOnValidationAssociateTagsOnProduct');
        }

        return this;
    }

    updateMediaAssociatedTags(mediaInfos)
    {

        /*let registeredMediaInfosIndex = this.__mediasAssociationInfo.findIndex( mediaInfo =>  mediaInfo.media === mediaInfos.media );

        if( registeredMediaInfosIndex === -1 )
            this.__mediasAssociationInfo.push( mediaInfos );

        else
            this.__mediasAssociationInfo[ registeredMediaInfosIndex ].tags = mediaInfos.tags;

        let mediaIndex = this.__$mediasCollection.find(`.media_name[value='${ mediaInfos.media }']`).parents('li').data('index');

        $(`#medias_list_medias_${mediaIndex}_tags input[type='checkbox']`).each( (index, input) => {

            $(input).attr('checked', ( mediaInfos.tags.indexOf( parseInt($(input).val()) ) !== -1 ) );

        } )*/

    }



    enable() {
        super.enable();
        this.onClickOnTagAssociationButtonShowModal(true)
            .onClickOnCloseButtonCloseTagAssociationModal(true)
            .onClickOnValidationAssociateTagsOnProduct(true)
            .onInputStateChange(true)
        ;
    }

    disable() {
        super.disable();
        this.onClickOnTagAssociationButtonShowModal(false)
            .onClickOnCloseButtonCloseTagAssociationModal(false)
            .onClickOnValidationAssociateTagsOnProduct(false)
            .onInputStateChange(false)
        ;
    }

}

export default TagAssociationHandlerTool;