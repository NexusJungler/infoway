import Tool from "../../Tool";

class FilterMediasByCharacteristicsSubTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$mediasContainer = $(".medias-list-container");
        this.__$container = $(".filter-by-characteristics-container");
    }

    onClickOnFilterValidationButton(active)
    {

        if(active)
        {
            this.__$container.find(".filter-validation-btn").on("click.onClickOnFilterValidationButton", e => {

                const categorySelected = $('#filter-by-category').val();
                const productSelected = $('#filter-by-product').val();
                const criterionSelected = $('#filter-by-criterion').val();
                const tagSelected = $('#filter-by-tag').val();

                this.__$mediasContainer.find(`.card`).addClass("hidden");

                this.__$mediasContainer.find(`.card[data-categories*='${categorySelected}'][data-products*='${productSelected}'][data-criterions*='${criterionSelected}'][data-tags*='${tagSelected}']`).removeClass("hidden");

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