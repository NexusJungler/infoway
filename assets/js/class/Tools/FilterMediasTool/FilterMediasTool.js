import Tool from "../Tool";
import FilterMediasByTypeSubTool from "./FilterMediasByTypeSubTool/FilterMediasByTypeSubTool";
import FilterMediasByOrientationSubTool from "./FilterMediasByOrientationSubTool/FilterMediasByOrientationSubTool";
import FilterMediasByCategorySubTool from "./FilterMediasByCategorySubTool/FilterMediasByCategorySubTool";
import FilterMediasByProductSubTool from "./FilterMediasByProductSubTool/FilterMediasByProductSubTool";
import FilterMediasByCriterionSubTool from "./FilterMediasByCriterionSubTool/FilterMediasByCriterionSubTool";
import FilterMediasByTagSubTool from "./FilterMediasByTagSubTool/FilterMediasByTagSubTool";
import FilterMediasByCharacteristicsSubTool
    from "./FilterMediasByCharacteristicsSubTool/FilterMediasByCharacteristicsSubTool";

class FilterMediasTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__subTools = [
            new FilterMediasByTypeSubTool(),
            new FilterMediasByOrientationSubTool(),
            new FilterMediasByCharacteristicsSubTool(),
        ]
    }

    activeSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        this.__subTools[ this.getSubToolIndex(subToolName) ].setToolBox(this.getToolBox());
        this.__subTools[ this.getSubToolIndex(subToolName) ].enable();

        return this;
    }

    getSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        return this.__subTools[ this.getSubToolIndex(subToolName) ];

    }

    subToolIsRegistered(subToolName)
    {
        return this.getSubToolIndex( subToolName ) !== -1;
    }

    getSubToolIndex(subToolName)
    {
        return this.__subTools.findIndex( subTool =>  subTool.getName() === subToolName );
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

export default FilterMediasTool;