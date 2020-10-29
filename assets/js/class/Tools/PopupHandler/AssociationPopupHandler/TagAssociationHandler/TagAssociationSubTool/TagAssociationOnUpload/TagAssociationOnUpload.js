import TagAssociationSubTool from "../TagAssociationSubTool";

class TagAssociationOnUpload extends TagAssociationSubTool
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

                if( $('.popup_upload_container.is_open').length > 0 )
                {

                    let mediaTagsAssociatedIds = [];

                    this.__parent.__currentMediaPos = $(e.currentTarget).parents('tr').data('index');
                    this.__parent.__currentMediaName = $(e.currentTarget).parents('tr').find('.media_name_container .media_name').val();

                    $(`.popup_upload tr[data-index='${ this.__currentMediaPos }'] .associated_tags_container input[type='checkbox']`).each( (index, input) => {

                        if( $(input).is(':checked') )
                        {

                            if( isNaN( parseInt($(input).val(), 10) ) )
                                throw new Error("Invalid product id !");

                            mediaTagsAssociatedIds.push( $(input).val() );
                        }

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

            this.__$tagsList.find('.choice_tag').on('change.onInputStateChange', e => {

                if( $('.popup_upload_container.is_open').length > 0 )
                {

                    const checkbox = $(e.currentTarget);
                    const checkboxParentTr = $(e.currentTarget).parents('tr');
                    const tag_id = $(e.currentTarget).parents('tr').data('tag_id');

                    checkboxParentTr.removeClass('new_association dissociated');

                    let tagIsAlreadyAssociatedWithCurrentMedia = $(`.popup_upload tr[data-index='${ this.__parent.__currentMediaPos }'] .associated_tags_container input[type='checkbox'][value='${ tag_id }']`).is(':checked');

                    if(checkbox.is(':checked') && !tagIsAlreadyAssociatedWithCurrentMedia)
                    {
                        checkbox.prop('checked', true);
                        checkboxParentTr.addClass('new_association');
                    }
                    else if( !(checkbox.is(':checked')) )
                    {
                        checkbox.prop('checked', false);

                        if(tagIsAlreadyAssociatedWithCurrentMedia)
                            checkboxParentTr.addClass('dissociated');
                    }

                    this.__parent.handleMediaInfosModification();

                }

            })

        }
        else
        {
            this.__$tagsList.off('click.onInputStateChange', '.container-rdo-tags');
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

export default TagAssociationOnUpload;