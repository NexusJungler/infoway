import SubTool from "../../SubTool";

class MediaModificationButtonHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
    }

    onClickOnModificationButtonRedirectToEditPage(active)
    {
        if(active)
        {
            $('.modify_media_btn').on('click.onClickOnModificationButtonRedirectToEditPage', e => {

                const id = $(".select_media_input:checked").parents('.card').attr('id').replace('card_', '');

                window.location.href = ($('.select_media_input:checked').parents('.card').hasClass('synchro')) ? `/edit/synchro/${id}` : `/edit/media/${id}`;

            })
        }
        else
        {
            $('.modify_media_btn').off('click.onClickOnModificationButtonRedirectToEditPage');
        }

        return this;
    }

    enable()
    {
        super.enable();
        this.onClickOnModificationButtonRedirectToEditPage(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnModificationButtonRedirectToEditPage(false)
        ;
    }

}

export default MediaModificationButtonHandler;