import SubTool from "../../SubTool";


class FilterMediasByTypeSubTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$mediasContainer = $(".medias-list-container");
    }

    getMediaContainer()
    {
        return this.__$mediasContainer;
    }

    setMediaContainer(mediaContainer)
    {
        this.__$mediasContainer = mediaContainer;

        return this;
    }

    getAgainMediaListContainer()
    {
        this.__$mediasContainer = $(".medias-list-container");
    }

    onClickOnMediaFilterByTypeIcon(active)
    {
        if(active)
        {
            $(".filter.filter-media-by-type").on("click.onClickOnMediaFilterByTypeIcon", e => {

                // // show all
                if($(e.currentTarget).hasClass("active"))
                {
                    $(e.currentTarget).removeClass("active");
                    if(!this.__parent.isAnFilterIsActive())
                    {
                        this.__parent.__anFilterIsActive = false;
                        this.__$mediasContainer.find(`.card`).removeClass("hidden");
                    }
                }
                else
                {

                    this.__parent.__anFilterIsActive = true;

                    if($(e.currentTarget).hasClass("show-only-images"))
                    {

                        $(".filter.filter-media-by-type.active").removeClass("active");
                        $(e.currentTarget).addClass("active")
                        this.__$mediasContainer.find(`.card[data-media_type='image']`).removeClass("hidden");
                        this.__$mediasContainer.find(`.card[data-media_type!='image']`).addClass("hidden");

                        this.__parent.registerNewFilter({property: 'data-media_type', value: 'image'});
                    }
                    else
                    {

                        $(".filter.filter-media-by-type.active").removeClass("active");
                        $(e.currentTarget).addClass("active");
                        this.__$mediasContainer.find(`.card[data-media_type='video']`).removeClass("hidden");
                        this.__$mediasContainer.find(`.card[data-media_type!='video']`).addClass("hidden");

                        this.__parent.registerNewFilter({property: 'data-media_type', value: 'video'});
                    }

                }

            })
        }
        else
        {
            $(".filter.filter-media-by-type").off("click.onClickOnMediaFilterByTypeIcon")
        }

        return this;
    }

    enable()
    {
        super.enable();
        this.onClickOnMediaFilterByTypeIcon(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnMediaFilterByTypeIcon(false)
        ;
    }

}

export default FilterMediasByTypeSubTool;