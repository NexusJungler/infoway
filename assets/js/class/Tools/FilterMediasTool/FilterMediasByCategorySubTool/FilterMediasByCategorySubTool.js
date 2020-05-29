import Tool from "../../Tool";

class FilterMediasByCategorySubTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$mediasContainer = $(".medias-list-container");
    }

    enable()
    {
        super.enable();
    }

    disable()
    {
        super.disable();
    }

}

export default FilterMediasByCategorySubTool;