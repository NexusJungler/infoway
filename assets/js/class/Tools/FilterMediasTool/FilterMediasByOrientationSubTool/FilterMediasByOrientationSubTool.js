import Tool from "../../Tool";
import SubTool from "../../SubTool";

class FilterMediasByOrientationSubTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$mediasContainer = $(".medias-list-container");
    }

    onClickOnMediaFilterByOrientationIcon(active)
    {
        if(active)
        {
            $('.filter-media-by-orientation').on("click.onClickOnMediaFilterByOrientationIcon", e => {

                this.__parent.getSubTool()

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

                    if($(e.currentTarget).hasClass("show-only-horizontal-media"))
                    {

                        $(".filter.filter-media-by-orientation.active").removeClass("active");
                        $(e.currentTarget).addClass("active")
                        this.__$mediasContainer.find(`.card[data-orientation='horizontal']`).removeClass("hidden");
                        this.__$mediasContainer.find(`.card[data-orientation!='horizontal']`).addClass("hidden");

                    }
                    else
                    {

                        $(".filter.filter-media-by-orientation.active").removeClass("active");
                        $(e.currentTarget).addClass("active")
                        this.__$mediasContainer.find(`.card[data-orientation='vertical']`).removeClass("hidden");
                        this.__$mediasContainer.find(`.card[data-orientation!='vertical']`).addClass("hidden");
                    }

                }

            })
        }
        else
        {
            $('.filter-media-by-orientation').off("click.onClickOnMediaFilterByOrientationIcon");
        }

        return this;
    }

    enable()
    {
        super.enable();
        this.onClickOnMediaFilterByOrientationIcon(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnMediaFilterByOrientationIcon(false)
        ;
    }

}

export default FilterMediasByOrientationSubTool;