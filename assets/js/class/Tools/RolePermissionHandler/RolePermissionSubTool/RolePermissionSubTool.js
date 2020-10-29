import SubTool from "../../SubTool";
import RolePermissionHandler from "../RolePermissionHandler";

class RolePermissionSubTool extends SubTool
{

    constructor() {
        super();
    }

    setParent(parent)
    {
        if( !(parent instanceof RolePermissionHandler) )
            throw new Error(`Parameter of ${ this.__name }.setParent() must be instance of RolePermissionHandler, but '${typeof parent}' given !`);

        this.__parent = parent;
    }

    getParent() {
        return super.getParent();
    }

    enable() {
        super.enable();
    }

    disable() {
        super.disable();
    }

}

export default RolePermissionSubTool;