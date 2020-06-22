import SubTool from "../../SubTool";

class FilterMediasByAssociatedDataSubTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $(".filters_by_associated_data_container");
        this.__characteristics = ['categories', 'products', 'criterions', 'tags'];
        this.__$target = null;
    }

    setFilterTarget($target)
    {
        this.__$target = $target;
    }

    onClickOnFilterValidationButton(active)
    {

        if(active)
        {
            this.__$container.find(".filter_validation_btn").on("click.onClickOnFilterValidationButton", e => {

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

        switch (this.__$target)
        {

            case "card":
            default:
                this.__parent.getMediasContainer().find(`.card`).addClass("hidden");

                this.__parent.getMediasContainer().find(`.card${ filters }`).removeClass("hidden");
                break;

            case "association_popup_item":
                console.log(filters); debugger
                break;

        }

    }

    getActivedFilters()
    {

        this.__characteristics.forEach( characteristic => {

            const filterIsRegistered = this.__parent.findFilterByProperty('data-' + characteristic);

            const applyFilterCheckbox = this.__$container.find(`.apply_filter[data-target='${characteristic}']`);

            if(applyFilterCheckbox.is(':checked'))
            {

                let value = $(`#filter_by_${characteristic}`).val();

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
                    select.parent().prev('.apply_filter_container').find('.apply_filter').prop('checked', false);

                else
                    select.parent().prev('.apply_filter_container').find('.apply_filter').prop('checked', true);

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

export default FilterMediasByAssociatedDataSubTool;