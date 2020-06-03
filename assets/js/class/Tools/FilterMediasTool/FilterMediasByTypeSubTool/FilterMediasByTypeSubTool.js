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

                const currentTypeFilter = $(".filter.filter-media-by-type.active");
                if(currentTypeFilter.length > 0)
                {

                    let filter = { 'property': 'data-media_type', 'value': (currentTypeFilter.hasClass("show-only-images")) ? 'image' : 'video' };
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

                    this.__parent.registerFiltersInParent({property: 'data-media_type', value: ($(e.currentTarget).hasClass("show-only-images")) ? 'image' : 'video'});

                    $(".filter.filter-media-by-type.active").removeClass("active");

                    $(e.currentTarget).addClass("active");

                    if($(e.currentTarget).hasClass("show-only-images"))
                        filters += "[data-media_type*='image']";

                    else
                        filters += "[data-media_type*='video']";

                    //console.log(filters); debugger

                    this.__$mediasContainer.find(`.card${filters}`).removeClass("hidden");

                }

            })
        }
        else
        {
            $(".filter.filter-media-by-type").off("click.onClickOnMediaFilterByTypeIcon")
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