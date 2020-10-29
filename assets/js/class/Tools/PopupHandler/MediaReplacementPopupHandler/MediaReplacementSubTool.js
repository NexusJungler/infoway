import SubTool from "../../SubTool";
import UploadHandlerTool from "../UploadHandlerTool/UploadHandlerTool";
import MediaReplacementPopupHandler from "./MediaReplacementPopupHandler";

class MediaReplacementSubTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
    }

    setParent(parent)
    {
        /*console.log(parent)
        debugger*/
        if( !(parent instanceof MediaReplacementPopupHandler) )
            throw new Error(`Parameter of ${ this.__name }.setParent() must be instance of MediaReplacementPopupHandler, but '${typeof parent}' given !`);

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

export default MediaReplacementSubTool;