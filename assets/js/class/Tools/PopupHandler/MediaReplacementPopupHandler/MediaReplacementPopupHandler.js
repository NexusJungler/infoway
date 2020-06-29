import SubTool from "../../SubTool";

class MediaReplacementPopupHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_media_replacement_container');
        this.__$location = $('.popup_media_replacement');
    }

    onClickOnReplacementButton(active)
    {
        if(active)
        {
            $('.media_replacement_btn').on("click.onClickOnMediaReplacementButton", e => {

                this.__$container.addClass('is_open');

            })
        }
        else
        {
            $('.media_replacement_btn').off("click.onClickOnMediaReplacementButton");
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
        this.onClickOnReplacementButton(true)
            .onClickOnPopupCloseButton(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnReplacementButton(false)
            .onClickOnPopupCloseButton(false)
        ;
    }

}

export default MediaReplacementPopupHandler;