import Tool from "../Tool";

class MediaInfoSheetHandler extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
    }

    onClickOnMediaMiniatureShowMediaInfoSheet(active)
    {
        if(active)
        {

        }
        else
        {

        }

        return this;
    }

    enable()
    {
        super.enable();
        this.onClickOnMediaMiniatureShowMediaInfoSheet(true)

        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnMediaMiniatureShowMediaInfoSheet(false)

        ;
    }

}

export default MediaInfoSheetHandler;