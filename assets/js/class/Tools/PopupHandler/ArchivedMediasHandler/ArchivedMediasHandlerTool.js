import Tool from "../../Tool";
import SubTool from "../../SubTool";

class ArchivedMediasHandlerTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_medias_archived_container');
        this.__$location = $('.popup_medias_archived');
        this.__$mediasList = $('.archived_medias_list');
    }

    onClickOnWaitingListButtonOpenModal(active)
    {
        if(active)
        {

            $('#show_archived_media_button').on('click.onClickOnWaitingListButtonOpenModal', e => {

                this.__$container.addClass('is_open');

            })
        }
        else
        {
            $('#show_archived_media_button').off('click.onClickOnWaitingListButtonOpenModal');
        }

        return this;
    }

    onClickOnPopupCloseButton(active)
    {
        if(active)
        {
            this.__$location.find('.close_modal_button').on('click.onClickOnPopupCloseButton', e => {

                this.__$container.removeClass('is_open');

            })
        }
        else
        {
            this.__$location.find('.close_modal_button').off('click.onClickOnPopupCloseButton');
        }

        return this;
    }

    onMediaSelectionAndDeselection(active)
    {
        if(active)
        {
            this.__$mediasList.find('.select_media').on('change.onMediaSelectionAndDeselection', e => {

                const mediasSelectedNumber = $('.select_media:checked').length;

                //console.log(mediasSelectedNumber); debugger

                if(mediasSelectedNumber === 0 || mediasSelectedNumber > 1)
                {
                    if(!this.__$location.find('.redirect_to_edit_media_button').hasClass('disabled'))
                        this.__$location.find('.redirect_to_edit_media_button').addClass('disabled');

                    this.__$location.find('.redirect_to_edit_media_button').prop('disabled', true);
                }

                else
                {
                    //console.log("remove"); debugger
                    this.__$location.find('.redirect_to_edit_media_button').removeClass('disabled');
                    this.__$location.find('.redirect_to_edit_media_button').prop('disabled', false);
                }



            })
        }
        else
        {
            this.__$mediasList.find('.select_media').off('change.onMediaSelectionAndDeselection');
        }

        return this;
    }


    onClickOnModificationButtonRedirectToEditPage(active)
    {
        if(active)
        {
            this.__$location.find('.redirect_to_edit_media_button').on('click.onClickOnModificationButtonRedirectToEditPage', e => {

                const id = this.__$location.find(".select_media:checked").data('media');
                //console.log(id); debugger
                window.location = `/edit/media/${id}`;

            })
        }
        else
        {
            this.__$location.find('.redirect_to_edit_media_button').off('click.onClickOnModificationButtonRedirectToEditPage')
        }

        return this;
    }


    enable()
    {
        super.enable();
        this.onClickOnWaitingListButtonOpenModal(true)
            .onClickOnPopupCloseButton(true)
            .onMediaSelectionAndDeselection(true)
            .onClickOnModificationButtonRedirectToEditPage(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnWaitingListButtonOpenModal(false)
            .onClickOnPopupCloseButton(false)
            .onMediaSelectionAndDeselection(false)
            .onClickOnModificationButtonRedirectToEditPage(false)
        ;
    }

}

export default ArchivedMediasHandlerTool;