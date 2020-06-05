import SubTool from "../../SubTool";

class MediaInfoSheetHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_media_info_sheet_container');
    }

    onClickOnMediaMiniatureShowMediaInfoSheet(active)
    {
        if(active)
        {
            $('.media_miniature').on('click.onClickOnMediaMiniatureShowMediaInfoSheet', e => {

                if(!this.__$container.hasClass('is_open'))
                    this.__$container.addClass('is_open');

            })
        }
        else
        {
            $('.media_miniature').off('click.onClickOnMediaMiniatureShowMediaInfoSheet');
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

    enable()
    {
        super.enable();
        this.onClickOnMediaMiniatureShowMediaInfoSheet(true)
            .onClickOnPopupCloseButton(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnMediaMiniatureShowMediaInfoSheet(false)
            .onClickOnPopupCloseButton(false)
        ;
    }

}

export default MediaInfoSheetHandler;