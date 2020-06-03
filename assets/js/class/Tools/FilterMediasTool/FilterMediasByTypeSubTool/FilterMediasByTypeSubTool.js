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

                let filter = { 'property': 'data-media_type', 'value': ($(e.currentTarget).hasClass("show-only-images")) ? 'image' : 'video' };
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

                    this.__parent.registerFiltersInParent({property: 'data-media_type', value: ($(e.currentTarget).hasClass("show-only-images")) ? 'image' : 'video'});

                    $(".filter.filter-media-by-type.active").removeClass("active");

                    $(e.currentTarget).addClass("active");

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