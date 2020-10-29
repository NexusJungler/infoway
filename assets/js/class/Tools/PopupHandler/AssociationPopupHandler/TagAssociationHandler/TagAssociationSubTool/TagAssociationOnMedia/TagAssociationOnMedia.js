import TagAssociationSubTool from "../TagAssociationSubTool";

class TagAssociationOnMedia extends TagAssociationSubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_associate_tag_container');
        this.__$location = $('.popup_associate_tag');
        this.__$tagsList = $('.tag_list');
    }

    onClickOnTagAssociationButtonShowModal(active)
    {

        if(active)
        {

            $(document).on("click.onClickOnTagAssociationButtonShowModal", '.tag_association_btn', e => {

                if( $('.popup_upload_container.is_open').length === 0 )
                {

                    let mediaTagsAssociatedIds = [];

                    this.__parent.__currentMediaName = $('.media_name').text();
                    this.__parent.__currentMediaId = $('.media_miniature_container').data('media_id');

                    $('.media_tags_container .tag').each( (index, tag) => {

                        if( !$(tag).hasClass('hidden') )
                            mediaTagsAssociatedIds.push( $(tag).data('tag_id') );

                    } )

                    const index = this.__parent.__mediasAssociationInfo.findIndex( mediaAssociationInfo => mediaAssociationInfo.media === this.__parent.__currentMediaName );

                    if(index < 0)
                        this.__parent.__mediasAssociationInfo.push( { media: this.__parent.__currentMediaName, tags: mediaTagsAssociatedIds } );
                    else
                        this.__parent.__mediasAssociationInfo[index].tags = mediaTagsAssociatedIds;

                    this.__parent.initializePopupContent();

                }

            })

        }
        else
        {
            $(document).on("click.onClickOnTagAssociationButtonShowModal", '.tag_association_btn');
        }

        return this;
    }

    onInputStateChange(active)
    {
        if(active)
        {

            this.__parent.__$tagsList.find('.choice_tag').on('change.onInputStateChange', e => {

                if( $('.popup_upload_container.is_open').length === 0 )
                {

                    const checkbox = $(e.currentTarget);
                    const checkboxParentTr = $(e.currentTarget).parents('tr');
                    const tag_id = $(e.currentTarget).parents('tr').data('tag_id');

                    checkboxParentTr.removeClass('new_association dissociated');

                    if(checkbox.is(':checked') && $('.media_tags_container').find(`.tag[data-tag_id='${ tag_id }'] .tag_checkbox:checked`).length === 0)
                    {
                        checkbox.attr('checked', true);
                        checkboxParentTr.addClass('new_association');
                    }
                    else if( !(checkbox.is(':checked')))
                    {
                        checkbox.removeAttr('checked');

                        if($('.media_tags_container').find(`.tag[data-tag_id='${ tag_id }'] .tag_checkbox:checked`).length > 0)
                            checkboxParentTr.addClass('dissociated');
                    }
                    else
                    {
                        checkboxParentTr.removeClass('new_association dissociated');
                    }

                    this.__parent.handleMediaInfosModification();

                }

            })

        }
        else
        {
            this.__parent.__$tagsList.off('click.onInputStateChange', '.container-rdo-tags');
        }

        return this;
    }

    enable()
    {
        super.enable();

        this.onClickOnTagAssociationButtonShowModal(true)
            .onInputStateChange(true)
        ;
    }

    disable()
    {
        super.disable();

        this.onClickOnTagAssociationButtonShowModal(false)
            .onInputStateChange(false)
        ;
    }

}

export default TagAssociationOnMedia;