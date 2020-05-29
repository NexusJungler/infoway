import Tool from "../Tool";
import FilterMediasByTypeSubTool from "./FilterMediasByTypeSubTool/FilterMediasByTypeSubTool";

class FilterMediasTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__subTools = [
            new FilterMediasByTypeSubTool()
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