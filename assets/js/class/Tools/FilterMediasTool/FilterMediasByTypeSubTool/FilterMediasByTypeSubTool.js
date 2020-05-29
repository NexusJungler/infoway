import FilterMediasTool from "../FilterMediasTool";
import Tool from "../../Tool";


class FilterMediasByTypeSubTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$mediasContainer = $(".medias-list-container");
    }

    onClickOnMediaFilterByTypeIcon(active)
    {
        if(active)
        {
            $(".filter.filter-media-by-type").on("click.onClickOnMediaFilterByTypeIcon", e => {

                if($(e.currentTarget).hasClass("show-only-images"))
                {

                    if($(e.currentTarget).hasClass("active"))
                    {
                        $(e.currentTarget).removeClass("active");
                        this.__$mediasContainer.find(`.card[data-media_type='image']`).removeClass("hidden");
                        this.__$mediasContainer.find(`.card[data-media_type!='image']`).removeClass("hidden");
                    }
                    else
                    {
                        $(".filter.filter-media-by-type.active").removeClass("active");
                        $(e.currentTarget).addClass("active")
                        this.__$mediasContainer.find(`.card[data-media_type='image']`).removeClass("hidden");
                        this.__$mediasContainer.find(`.card[data-media_type!='image']`).addClass("hidden");
                    }

                }
                else
                {
                    if($(e.currentTarget).hasClass("active"))
                    {
                        $(e.currentTarget).removeClass("active");
                        this.__$mediasContainer.find(`.card[data-media_type='video']`).removeClass("hidden");
                        this.__$mediasContainer.find(`.card[data-media_type!='video']`).removeClass("hidden");
                    }
                    else
                    {
                        $(".filter.filter-media-by-type.active").removeClass("active");
                        $(e.currentTarget).addClass("active");
                        this.__$mediasContainer.find(`.card[data-media_type='video']`).removeClass("hidden");
                        this.__$mediasContainer.find(`.card[data-media_type!='video']`).addClass("hidden");
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