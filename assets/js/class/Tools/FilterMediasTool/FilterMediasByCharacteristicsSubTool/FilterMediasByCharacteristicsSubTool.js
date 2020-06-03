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

                let filters = this.getActivedFilters();

                console.log(filters); debugger

                this.__$mediasContainer.find(`.card`).addClass("hidden");

                this.__$mediasContainer.find(`.card${ filters }`).removeClass("hidden");

            })
        }
        else
        {
            this.__$container.find(".filter-validation-btn").off("click.onClickOnFilterValidationButton")
        }

        return this;
    }

    getActivedFilters()
    {

        this.__characteristics.forEach( characteristic => {

            const filterIsRegistered = this.__parent.findFilterByProperty('data-' + characteristic);

            let value = $(`#filter-by-${characteristic}`).val();

            if(value === '' && filterIsRegistered)
                this.__parent.removeFilterByProperty('data-' + characteristic);

            else
            {

                if(value !== '')
                {

                    if(!filterIsRegistered)
                        this.__parent.registerNewFilter({'property': 'data-' + characteristic, 'value': value});

                    else
                        this.__parent.replaceAnRegisteredFilter({'property': 'data-' + characteristic, 'value': value});

                }

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

export default FilterMediasByCharacteristicsSubTool;