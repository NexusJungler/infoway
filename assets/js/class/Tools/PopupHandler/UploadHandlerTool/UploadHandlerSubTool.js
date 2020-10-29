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
        this.__$fileToCharacterisationList = $('.file_to_characterisation_list');
        this.__mediaCardPrototype = '';

        this.initMediaCardPrototype();
    }

    initMediaCardPrototype()
    {
        throw new Error(`${ this.__name }.initMediaCardPrototype is not implement and override !`);
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
                preview = `<img class="preview" src="/miniatures/${mediaInfos.customer}/image/${mediaInfos.mediaType}/low/${mediaInfos.id}.png" alt="/miniatures/${mediaInfos.customer}/image/${mediaInfos.mediaType}/low/${mediaInfos.id}.png" />`;


            else
                preview = `<video class="preview" controls>
                                <source src="/miniatures/${mediaInfos.customer}/video/${mediaInfos.mediaType}/low/${mediaInfos.id}.mp4" type="${mediaInfos.mimeType}">
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

    showMediaEditingResume()
    {
        throw new Error(`${ this.__name }.showMediaEditingResume is not implement and override !`);
    }

    buildMediaCard(mediaInfos)
    {
        throw new Error(`${ this.__name }.buildMediaCard is not implement and override !`);
    }

    getDaysDiffBetweenDates(date1, date2)
    {
        date1 = ( date1 instanceof Date) ? date1 : new Date(date1);
        date2 = ( date2 instanceof Date) ? date2 : new Date(date2);
        const diffTime = Math.abs(date1 - date2);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
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

export default UploadHandlerSubTool;