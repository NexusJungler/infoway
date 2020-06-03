import SubTool from "../../SubTool";

class FilterMediasByCharacteristicsSubTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$mediasContainer = $(".medias-list-container");
        this.__$container = $(".filter-by-characteristics-container");
        this.__characteristics = ['category', 'product', 'criterion', 'tag'];
    }

    onClickOnFilterValidationButton(active)
    {

        if(active)
        {
            this.__$container.find(".filter-validation-btn").on("click.onClickOnFilterValidationButton", e => {

                e.preventDefault();

                const categorySelected = $('#filter-by-category').val();
                const productSelected = $('#filter-by-product').val();
                const criterionSelected = $('#filter-by-criterion').val();
                const tagSelected = $('#filter-by-tag').val();

                this.registerCharacteristicsFiltersInParent();

                let filters = this.getActivedFilters();

                if(categorySelected === '' && productSelected === '' && criterionSelected === '' && tagSelected === '' )
                {
                    if(!this.__parent.isAnFilterIsActive())
                        this.__$mediasContainer.find(`.card`).removeClass("hidden");

                    else
                        this.__$mediasContainer.find(`.card${filters}`).removeClass("hidden");
                }
                else
                {

                    this.__$mediasContainer.find(`.card`).addClass("hidden");

                    if(!this.__parent.isAnFilterIsActive())
                    {

                        let findQuery = '';

                        this.__characteristics.forEach( characteristic => {

                            findQuery += `[data-${characteristic}*='${$(`#filter-by-${characteristic}`).val()}']`;

                        } )

                        if(findQuery === '')
                            throw new Error(`'${this.__name}'::__characteristics must contain characteristics names but it is empty !`);

                        this.__$mediasContainer.find(`.card${findQuery}`).removeClass("hidden");

                    }
                    else
                    {

                        //console.log(filters); debugger

                        this.__$mediasContainer.find(`.card${ filters }`).removeClass("hidden");

                    }

                    this.__parent.__anFilterIsActive = true;
                }

            })
        }
        else
        {
            this.__$container.find(".filter-validation-btn").off("click.onClickOnFilterValidationButton")
        }

        return this;
    }

    registerCharacteristicsFiltersInParent()
    {

        this.__characteristics.forEach( characteristic => {

            const filter = { 'property': 'data-' + characteristic, 'value': $(`#filter-by-${characteristic}`).val() };

            if(this.__parent.findFilterByProperty(filter.property))
                this.__parent.replaceAnRegisteredFilter(filter);

            else
                this.__parent.registerNewFilter(filter);

        } )
        
    }

    getActivedFilters()
    {
        let filters = '';

        //let filters = `[data-categories*='${categorySelected}'][data-products*='${productSelected}'][data-criterions*='${criterionSelected}'][data-tags*='${tagSelected}']`;

        this.__characteristics.forEach( characteristic => {

            const filterIsRegistered = this.__parent.findFilterByProperty('data-' + characteristic);
            if(!filterIsRegistered)
                filters += `[data-${characteristic}*='${$(`#filter-by-${characteristic}`).val()}']`;

        } )

        const activeFilters = this.__parent.getActiveFilters();

        activeFilters.forEach( (activeFilter) => {

            filters += `[${activeFilter.property}*='${activeFilter.value}']`;

        } );

        return filters;
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

export default FilterMediasByCharacteristicsSubTool;