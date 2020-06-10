import SubTool from "../../SubTool";

class FilterMediasByCharacteristicsSubTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $(".filter-by-characteristics-container");
        this.__characteristics = ['categories', 'products', 'criterions', 'tags'];
    }

    onClickOnFilterValidationButton(active)
    {

        if(active)
        {
            this.__$container.find(".filter-validation-btn").on("click.onClickOnFilterValidationButton", e => {

                e.preventDefault();

                let filters = this.getActivedFilters();

                console.log(filters); //debugger

                this.__parent.getMediasContainer().find(`.card`).addClass("hidden");

                this.__parent.getMediasContainer().find(`.card${ filters }`).removeClass("hidden");

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

            const applyFilterCheckbox = this.__$container.find(`.apply-filter[data-target='${characteristic}']`);

            if(applyFilterCheckbox.is(':checked'))
            {

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

            }
            else
            {
                if(filterIsRegistered)
                    this.__parent.removeFilterByProperty('data-' + characteristic);
            }

        } )

        return this.__parent.getActivedFilters();
    }

    onCharacteristicsSelectChangeCheckedValue(active)
    {

        if(active)
        {
            this.__$container.find('select').on('change.onCharacteristicsSelectChangeCheckedValue', e => {

                const select = $(e.currentTarget);

                if(select.val() === '')
                    select.parent().prev('.apply-filter-container').find('.apply-filter').prop('checked', false);

                else
                    select.parent().prev('.apply-filter-container').find('.apply-filter').prop('checked', true);

            })
        }
        else
        {
            this.__$container.find('select').off('change.onCharacteristicsSelectChangeCheckedValue');
        }

        return this;

    }

    enable()
    {
        super.enable();
        this.onClickOnFilterValidationButton(true)
            .onCharacteristicsSelectChangeCheckedValue(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnFilterValidationButton(false)
            .onCharacteristicsSelectChangeCheckedValue(false)
        ;
    }

}

export default FilterMediasByCharacteristicsSubTool;