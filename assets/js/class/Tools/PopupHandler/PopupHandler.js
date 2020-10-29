import Tool from "../Tool";
import MediaInfoSheetHandler from "./MediaInfoSheetHandler/MediaInfoSheetHandler";
import MediaWaitingIncrustationHandler from "./MediaWaitingIncrustationHandler/MediaWaitingIncrustationHandler";
import UploadHandlerTool from "./UploadHandlerTool/UploadHandlerTool";
import MediaDeletingButtonHandler from "../MediathequeActionButtonHandler/MediaDeletingButtonHandler/MediaDeletingButtonHandler";
import AssociationPopupHandler from "./AssociationPopupHandler/AssociationPopupHandler";
import ParentTool from "../ParentTool";
import MediaReplacementPopupHandler from "./MediaReplacementPopupHandler/MediaReplacementPopupHandler";
import MediaExpandedMiniatureDisplayHandler from "./MediaExpandedMiniatureDisplayHandler/MediaExpandedMiniatureDisplayHandler";
import ArchivedMediasHandlerTool from "./ArchivedMediasHandler/ArchivedMediasHandlerTool";

class PopupHandler extends ParentTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = "";
        this.__$location = "";
        this.__popupIsOpen = false;
        this.__subTools = [
            new MediaInfoSheetHandler(),
            new MediaWaitingIncrustationHandler(),
            new UploadHandlerTool(),
            new MediaReplacementPopupHandler(),
            new AssociationPopupHandler(),
            new MediaExpandedMiniatureDisplayHandler(),
            new ArchivedMediasHandlerTool()
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