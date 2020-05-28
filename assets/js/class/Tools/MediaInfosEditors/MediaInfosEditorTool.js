import Tool from "../Tool";
import MediaDiffusionDateEditor from "./MediaDiffusionDateEditor/MediaDiffusionDateEditor";

class MediaInfosEditorTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__subTools = [

        ];
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

export default MediaInfosEditorTool;