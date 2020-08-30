import SubTool from "../../SubTool";
import ParentTool from "../../ParentTool";
import Tool from "../../Tool";
import UploadHandlerTool from "./UploadHandlerTool";

class UploadHandlerSubTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$location = $('.popup_upload');
    }

    setParent(parent)
    {
        /*console.log(parent)
        debugger*/
        if( !(parent instanceof UploadHandlerTool) )
            throw new Error(`Parameter of ${ this.__name }.setParent() must be instance of UploadHandlerTool, but '${typeof parent}' given !`);

        this.__parent = parent;
    }

    getParent()
    {
        return this.__parent;
    }

    getMediaPreview(mediaInfos)
    {

        let preview = "";

        if(mediaInfos.miniatureExist)
        {

            if(mediaInfos.fileType === 'image')
                preview = `<img class="preview" src="/miniatures/${mediaInfos.customer}/image/${mediaInfos.mediaType}/low/${mediaInfos.name}.png" alt="/miniatures/${mediaInfos.customer}/image/${mediaInfos.mediaType}/low/${mediaInfos.name}.png" />`;


            else
                preview = `<video class="preview" controls>
                                <source src="/miniatures/${mediaInfos.customer}/video/${mediaInfos.mediaType}/low/${mediaInfos.name}.mp4" type="${mediaInfos.mimeType}">
                           </video>`;

        }
        else
            preview = `<img class="media_miniature miniature_${ mediaInfos.fileType }" src="/build/images/no-available-image.png" alt="/build/images/no-available-image.png">`;

        return preview;
    }

    showMediaInfoForEdit(mediaInfos)
    {
        throw new Error(`${ this.__name }.showMediaInfoForEdit is not implement and override !`);
    }

    enable()
    {

    }

    disable()
    {

    }

}

export default UploadHandlerSubTool;