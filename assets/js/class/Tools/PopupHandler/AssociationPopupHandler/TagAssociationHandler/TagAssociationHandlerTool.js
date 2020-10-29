import Tool from "../../../Tool";
import SubTool from "../../../SubTool";
import TagAssociationOnUpload from "./TagAssociationSubTool/TagAssociationOnUpload/TagAssociationOnUpload";
import TagAssociationOnMedia from "./TagAssociationSubTool/TagAssociationOnMedia/TagAssociationOnMedia";
import TagAssociationOnProduct from "./TagAssociationSubTool/TagAssociationOnProduct/TagAssociationOnProduct";
import TagAssociationOnSite from "./TagAssociationSubTool/TagAssociationOnSite/TagAssociationOnSite";

class TagAssociationHandlerTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$mediasCollection = $('.medias_collection');
        this.__$container = $('.popup_associate_tag_container');
        this.__$location = $('.popup_associate_tag');
        this.__$tagsList = $('.tag_list');
        //this.__currentMedia = null;
        this.__currentMediaName = null;
        this.__currentMediaId = null;
        this.__mediasAssociationInfo = [];
        this.__isUpload = false;
        this.__currentMediaPos = null;
        this.__currentPage = "";
        this.__currentTagAssociationManager = null;
        this.__subTools = [
            new TagAssociationOnUpload(),
            new TagAssociationOnMedia(),
            new TagAssociationOnProduct(),
            new TagAssociationOnSite(),
        ];
    }

    activeAllSubTools()
    {

        this.__subTools.map( subTool => {

            subTool.setToolBox(this.getToolBox());
            subTool.setParent(this.getParent());
            subTool.enable();

        } );

        return this;

    }

    activeSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        //this.__currentTagAssociationManager = this.__subTools[ this.getSubToolIndex(subToolName) ];
        this.__subTools[ this.getSubToolIndex(subToolName) ].setToolBox(this.getToolBox());
        this.__subTools[ this.getSubToolIndex(subToolName) ].setParent(this);
        this.__subTools[ this.getSubToolIndex(subToolName) ].enable();

        return this;
    }

    disableSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        this.__subTools[ this.getSubToolIndex(subToolName) ].disable();
        this.__currentTagAssociationManager = null;

        return this;

    }

    activeTagAssociationSubTool()
    {

        if( $('.medias_list_container').length > 0 )
        {
            this.activeSubTool("TagAssociationOnUpload");
            this.activeSubTool("TagAssociationOnProduct");
        }

        else if( $('.sites_list_container').length > 0 )
            this.activeSubTool("TagAssociationOnSite");

        else
            this.activeSubTool("TagAssociationOnMedia");

    }

    disableTagAssociationSubTool()
    {

        this.__subTools.map( subTool => {

            subTool.disable();

        } )

    }

    disableAllSubTool()
    {
        this.__subTools.map( subTool => {

            subTool.disable();

        } );

        return this;
    }

    getSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        return this.__subTools[ this.getSubToolIndex(subToolName) ];

    }

    subToolIsRegistered(subToolName)
    {
        return this.getSubToolIndex( subToolName ) !== -1;
    }

    getSubToolIndex(subToolName)
    {

        return this.__subTools.findIndex( subTool =>  subTool.getName() === subToolName );
    }

    initializePopupContent()
    {
        //this.__$tagsList.find(`.choice_tag`).removeAttr('checked');
        const index = this.__mediasAssociationInfo.findIndex( mediaAssociationInfo => mediaAssociationInfo.media === this.__currentMediaName );

        if(index >= -1)
        {

            this.__mediasAssociationInfo[index].tags.forEach( (associatedTagId) => {

                this.__$tagsList.find(`tr[data-tag_id='${ associatedTagId }'] .choice_tag`).prop('checked', true)

            } );

        }

        this.__$location.find('.media_name').text( this.__currentMediaName );
        this.__$container.addClass('is_open');
        this.__parent.__popupIsOpen = true;

    }

    onClickOnTagAssociationButtonShowModal(active)
    {

        /*if(active)
        {

            $(document).on("click.onClickOnTagAssociationButtonShowModal", '.tag_association_btn', e => {


                let mediaTagsAssociatedIds = [];

                if( $('.popup_upload_container.is_open').length > 0 ) // association is used after upload
                {
                    this.__isUpload = true;
                    this.__currentMediaPos = $(e.currentTarget).parents('tr').data('index');
                    this.__currentMediaName = $(e.currentTarget).parents('tr').find('.media_name_container .media_name').val();

                    this.activeSubTool("TagAssociationOnUpload");

                    $(`.popup_upload tr[data-index='${ this.__currentMediaPos }'] .associated_tags_container input[type='checkbox']`).each( (index, input) => {

                        if( $(input).is(':checked') )
                        {

                            if( isNaN( parseInt($(input).val(), 10) ) )
                                throw new Error("Invalid product id !");

                            mediaTagsAssociatedIds.push( $(input).val() );
                        }

                    } )

                }
                else
                {
                    this.__isUpload = false;
                    this.__currentMediaName = $('.media_name').text();
                    this.__currentMediaId = $('.media_miniature_container').data('media_id');

                    $('.media_tags_container .tag').each( (index, tag) => {

                        if( !$(tag).hasClass('hidden') )
                            mediaTagsAssociatedIds.push( $(tag).data('tag_id') );

                    } )


                }

                const index = this.__mediasAssociationInfo.findIndex( mediaAssociationInfo => mediaAssociationInfo.media === this.__currentMediaName );

                if(index < 0)
                    this.__mediasAssociationInfo.push( { media: this.__currentMediaName, tags: mediaTagsAssociatedIds } );
                else
                    this.__mediasAssociationInfo[index].tags = mediaTagsAssociatedIds;

                //console.log(this.__mediasAssociationInfo);debugger
                this.initializePopupContent();

            })

        }
        else
        {
            $(document).on("click.onClickOnTagAssociationButtonShowModal", '.tag_association_btn');
        }*/

        return this;
    }

    onClickOnCloseButton(active)
    {

        if(active)
        {
            this.__$location.find('.close_modal_button').on('click.onClickOnCloseButton', e => {

                if( ( (this.__$tagsList.find('.new_association').length > 0 || this.__$tagsList.find('.dissociated').length > 0) && confirm("Vous n'avez pas validés vos dernières modifications ! Voulez-vous vraiment continuer ?") ) ||
                    (this.__$tagsList.find('.new_association').length === 0 && this.__$tagsList.find('.dissociated').length === 0)  )
                {

                    this.__$container.removeClass('is_open');
                    this.__parent.__popupIsOpen = false;

                    // reset
                    //this.__$tagsList.find('.choice_tag').removeAttr('checked');
                    this.__$tagsList.find('.choice_tag').prop('checked', false);
                    this.__$tagsList.find('tr').removeClass('new_association dissociated');

                    if( $(e.currentTarget).hasClass('cancel') && this.__isUpload )
                        this.__mediasAssociationInfo = [];

                }

            })
        }
        else
        {
            this.__$location.find('.close_modal_button').off('click.onClickOnCloseButton');
        }

        return this;
    }

    onInputStateChange(active)
    {
        /*if(active)
        {

            this.__$tagsList.find('.choice_tag').on('change.onInputStateChange', e => {

                const checkbox = $(e.currentTarget);
                const checkboxParentTr = $(e.currentTarget).parents('tr');
                const tag_id = $(e.currentTarget).parents('tr').data('tag_id');

                checkboxParentTr.removeClass('new_association dissociated');

                if( this.__isUpload )
                {

                    let tagIsAlreadyAssociatedWithCurrentMedia = $(`.popup_upload tr[data-index='${ this.__currentMediaPos }'] .associated_tags_container input[type='checkbox'][value='${ tag_id }']`).is(':checked');

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

                }
                else
                {
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
                }

                this.handleMediaInfosModification();

            })

        }
        else
        {
            this.__$tagsList.off('click.onInputStateChange', '.container-rdo-tags');
        }*/

        return this;
    }


    handleMediaInfosModification()
    {

        this.__$location.find('.validate_association_btn').attr('disabled', ( (this.__$tagsList.find('tr.dissociated').length === 0)
            && (this.__$tagsList.find('tr.new_association').length === 0)  ) );

    }


    onClickOnValidationAssociateTagsOnMedia(active)
    {

        if(active)
        {
            this.__$location.find('.validate_association_btn').on('click.onClickOnValidationAssociateTagsOnMedia', e => {

                let tagsToMedia = [];
                this.__$tagsList.find('.choice_tag').each( (index, element) => {

                    $(element).parents('tr').removeClass('new_association dissociated');
                    const tag_id = $(element).parents('tr').data('tag_id');

                    // association is used after upload
                    if( $('.popup_upload_container.is_open').length > 0 )
                    {

                        const tagCheckbox = $(`.popup_upload tr[data-index='${ this.__currentMediaPos }'] .associated_tags_container input[type='checkbox'][value='${ tag_id }']`);
                        const tagLabel = $(`.popup_upload tr[data-index='${ this.__currentMediaPos }'] .associated_tags_container label[for='${ tagCheckbox.attr('id') }']`);

                        if( $(element).is(':checked') )
                        {
                            tagCheckbox.attr('checked', true);
                            tagLabel.css( { 'display': 'inline-block' } );
                        }

                        else
                        {
                            tagCheckbox.removeAttr('checked');
                            tagLabel.css( { 'display': 'none' } );
                        }

                    }
                    else
                    {

                        $('.media_tags_container').find(`.tag[data-tag_id='${ tag_id }'] .tag_checkbox`).prop('checked', $(element).is(':checked'));

                        if($(element).is(':checked'))
                            $('.media_tags_container').find(`.tag[data-tag_id='${ tag_id }']`).removeClass('hidden');

                        else
                            $('.media_tags_container').find(`.tag[data-tag_id='${ tag_id }']`).addClass('hidden');

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
            this.__$location.find('.validate_association_btn').off('click.onClickOnValidationAssociateTagsOnMedia');
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

        this.activeTagAssociationSubTool();

        this.onClickOnTagAssociationButtonShowModal(true)
            .onClickOnCloseButton(true)
            .onClickOnValidationAssociateTagsOnMedia(true)
            .onInputStateChange(true)
        ;
    }

    disable() {
        super.disable();

        this.disableTagAssociationSubTool();

        this.onClickOnTagAssociationButtonShowModal(false)
            .onClickOnCloseButton(false)
            .onClickOnValidationAssociateTagsOnMedia(false)
            .onInputStateChange(false)
        ;
    }

}

export default TagAssociationHandlerTool;