import ParentTool from "../../../ParentTool";
import SubTool from "../../../SubTool";

class UploadMediaDiffSubTool extends SubTool {

    constructor()
    {
        super();
        this.__name = this.constructor.name;
    }

    setParent(parent)
    {
        if( !(parent instanceof ParentTool) )
            throw new Error(`Attempt to set parent to ${this.__name}, but parentTool must be instance of ParentTool`);

        this.__parent = parent;
    }

    getParent()
    {
        return this.__parent;
    }

    enable()
    {

    }

    disable()
    {

    }

}

export default UploadMediaDiffSubTool;