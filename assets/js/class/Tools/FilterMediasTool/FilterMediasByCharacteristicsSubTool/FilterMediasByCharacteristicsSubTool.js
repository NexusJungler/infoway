import Tool from "../../Tool";

class FilterMediasByCharacteristicsSubTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$mediasContainer = $(".medias-list-container");
        this.__$container = $(".filter-by-characteristics-container");
        this.__currentCharacteristicsFiltersValues = [];
    }

    onClickOnFilterValidationButton(active)
    {

        if(active)
        {
            this.__$container.find(".filter-validation-btn").on("click.onClickOnFilterValidationButton", e => {

                console.log("filter validation"); debugger

            })
        }
        else
        {
            this.__$container.find(".filter-validation-btn").off("click.onClickOnFilterValidationButton")
        }

        return this;
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