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

                const currentOrientationFilter = $(".filter.filter-media-by-orientation.active");
                if(currentOrientationFilter.length > 0)
                {

                    let filter = { 'property': 'data-orientation', 'value': (currentOrientationFilter.hasClass("show-only-horizontal-media")) ? 'horizontal' : 'vertical' };
                    if(this.__parent.isFilterAlreadyRegistered(filter))
                        this.__parent.removeFilter(filter);

                }

                let filters = this.getActivedFilters();

                if($(e.currentTarget).hasClass("active"))
                {
                    $(e.currentTarget).removeClass("active");

                    if(!this.__parent.isAnFilterIsActive())
                        this.__$mediasContainer.find(`.card`).removeClass("hidden");

                    else
                        this.__$mediasContainer.find(`.card${filters}`).removeClass("hidden");
                }
                else
                {

                    this.__parent.__anFilterIsActive = true;

                    this.__$mediasContainer.find(`.card`).addClass("hidden");

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

    getActivedFilters()
    {
        let filters = '';

        const activeFilters = this.__parent.getActiveFilters();

        activeFilters.forEach( (activeFilter) => {

            filters += `[${activeFilter.property}*='${activeFilter.value}']`;

        } );

        return filters;
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