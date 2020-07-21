import SubTool from "../../SubTool";

class FilterMediasByAssociatedDataSubTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$currentContainer = $(".filters_by_associated_data_container");
        this.__characteristics = ['categories', 'products', 'criterions', 'tags'];
        this.__target = "card";
        //this.__$targetContainer = $('.medias_list_container');
    }

    setFilterTarget(target, $filtersContainer)
    {
        this.__target = target;

        this.__$currentContainer = $filtersContainer;

    }

    onClickOnFilterValidationButton(active)
    {

        if(active)
        {
            this.__$currentContainer.find(".filter_validation_btn").on("click.onClickOnFilterValidationButton", e => {

                e.preventDefault();

                let filters = this.getActivedFilters();

                console.log(filters); //debugger

                this.applyFilters(filters);

            })
        }
        else
        {
            this.__$container.find(".filter_validation_btn").off("click.onClickOnFilterValidationButton")
        }

        return this;
    }

    applyFilters(filters)
    {

        switch (this.__target)
        {

            case "card":
            default:
                this.__parent.getMediasContainer().find(`.card`).addClass("hidden");

                this.__parent.getMediasContainer().find(`.card${ filters }`).removeClass("hidden");
                break;

            case "association_popup_item":

                $('.association_popup_container.is_open').find(`tbody.list tr`).addClass("hidden");

                $('.association_popup_container.is_open').find(`tbody.list tr${ filters }`).removeClass("hidden");
                break;

        }

    }

    getActivedFilters()
    {

        this.__characteristics.forEach( characteristic => {

            if(this.__$currentContainer.find(`.filter_by_${characteristic}`).length > 0)
            {

                const filterIsRegistered = this.__parent.findFilterByProperty('data-' + characteristic);

                const value = this.__$currentContainer.find(`.filter_by_${characteristic}`).val();

                if(value !== '')
                {

                    if(!filterIsRegistered)
                        this.__parent.registerNewFilter({'property': 'data-' + characteristic, 'value': value});

                    else
                        this.__parent.replaceAnRegisteredFilter({'property': 'data-' + characteristic, 'value': value});

                }
                else
                    if(filterIsRegistered)
                        this.__parent.removeFilterByProperty('data-' + characteristic);

            }

        } )

        return this.__parent.getActivedFilters();
    }

    enable()
    {
        super.enable();
        this.onClickOnFilterValidationButton(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnFilterValidationButton(false)
        ;
    }

}

export default FilterMediasByAssociatedDataSubTool;