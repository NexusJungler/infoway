import SubTool from "../../../SubTool";
import MediaReplacementSubTool from "../MediaReplacementSubTool";

class BasicRemplacementHandler extends MediaReplacementSubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
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

export default BasicRemplacementHandler;