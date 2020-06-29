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

                if( $('.medias_list_container').length > 0 )
                {
                    const id = this.__parent.getMediasContainer().find(".select_media_input:checked").parents('.card').attr('id').replace('media_', '');
                    //console.log(id); debugger
                    window.location = `/edit/media/${id}`;
                }
                else
                {
                    debugger
                }

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