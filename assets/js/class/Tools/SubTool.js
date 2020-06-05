import ToolBox from "./ToolBox";
import Tool from "./Tool";

class SubTool extends Tool
{

    constructor()
    {
        super();
        this.__name = null;
        this.__toolBox = null;
        this.__parent = null;
    }

    setParent(parent)
    {
        if( !(parent instanceof Tool) )
            throw new Error(`Attempt to set parent to ${this.__name}, but parent must be instance of Tool`);

        this.__parent = parent;
    }

    getParent()
    {
        return this.__parent;
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

export default SubTool;