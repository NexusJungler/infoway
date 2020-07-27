import ToolBox from "./ToolBox";
import Tool from "./Tool";
import ParentTool from "./ParentTool";

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
        /*console.log(parent)
        debugger*/
        if( !(parent instanceof ParentTool) )
            throw new Error(`Parameter of ${ this.__name }.setParent() must be instance of Tool, but '${typeof parent}' given !`);

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