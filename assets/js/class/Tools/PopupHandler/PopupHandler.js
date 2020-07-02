import Tool from "../Tool";
import MediaInfoSheetHandler from "./MediaInfoSheetHandler/MediaInfoSheetHandler";
import MediaWaitingIncrustationHandler from "./MediaWaitingIncrustationHandler/MediaWaitingIncrustationHandler";
import UploadHandlerTool from "./UploadHandlerTool/UploadHandlerTool";
import MediaDeletingButtonHandler from "../MediathequeActionButtonHandler/MediaDeletingButtonHandler/MediaDeletingButtonHandler";
import AssociationPopupHandler from "./AssociationPopupHandler/AssociationPopupHandler";
import ParentTool from "../ParentTool";
import MediaReplacementPopupHandler from "./MediaReplacementPopupHandler/MediaReplacementPopupHandler";

class PopupHandler extends ParentTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__subTools = [
            new MediaInfoSheetHandler(),
            new MediaWaitingIncrustationHandler(),
            new UploadHandlerTool(),
            //new MediaReplacementPopupHandler(),
            new AssociationPopupHandler(),
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

export default PopupHandler;