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

                let filter = { 'property': 'data-orientation', 'value': ($(e.currentTarget).hasClass("show-only-horizontal-media")) ? 'horizontal' : 'vertical' };
                if(this.__parent.findFilterByProperty(filter.property))
                    this.__parent.replaceAnRegisteredFilter(filter);

                else
                    this.__parent.registerNewFilter(filter);

                let filters = this.__parent.getActivedFilters();

                this.__$mediasContainer.find(`.card`).addClass("hidden");

                if($(e.currentTarget).hasClass("active"))
                {

                    $(e.currentTarget).removeClass("active");

                    this.__parent.removeFilter(filter);

                    filters = this.__parent.getActivedFilters();

                    this.__$mediasContainer.find(`.card${filters}`).removeClass("hidden");

                }
                else
                {

                    this.__parent.registerFiltersInParent({property: 'data-orientation', value: ($(e.currentTarget).hasClass("show-only-horizontal-media")) ? 'horizontal' : 'vertical'});

                    $(".filter.filter-media-by-orientation.active").removeClass("active");

                    $(e.currentTarget).addClass("active");

                    if($(e.currentTarget).hasClass("show-only-horizontal-media"))
                        filters += "[data-orientation*='horizontal']";

                    else
                        filters += "[data-orientation*='vertical']";

                    //console.log(filters); debugger

                    this.__$mediasContainer.find(`.card${filters}`).removeClass("hidden");

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