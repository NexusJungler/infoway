import SubTool from "../../../../SubTool";
import TagAssociationHandlerTool from "../TagAssociationHandlerTool";

class TagAssociationSubTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
    }

    setParent(parent)
    {
        if( !(parent instanceof TagAssociationHandlerTool) )
            throw new Error(`Parameter of ${ this.__name }.setParent() must be instance of TagAssociationHandlerTool, but '${typeof parent}' given !`);

        this.__parent = parent;
    }

    getParent() {
        return super.getParent();
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

export default TagAssociationSubTool;