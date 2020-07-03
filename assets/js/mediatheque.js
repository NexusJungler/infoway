require("../css/popups/popup.scss");
require("../css/media/media_image.scss");

import ToolBox from "./class/Tools/ToolBox";

const toolBox = new ToolBox();
toolBox.activeTool("PaginatorHandler")
       .activeTool("FilterMediasTool")
       .activeTool("PopupHandler")
       .activeTool("AnimateTool")
       .activeTool("MediathequeActionButtonHandler")
;

toolBox.getTool("PopupHandler").activeSubTool("MediaInfoSheetHandler")
                                        .activeSubTool("MediaWaitingIncrustationHandler")
                                        .activeSubTool("AssociationPopupHandler", ["all"])
                                        .activeSubTool("UploadHandlerTool")
                                        .activeSubTool("MediaExpandedMiniatureDisplayHandler")
;


toolBox.getTool("FilterMediasTool").activeAllSubTools()
;

toolBox.getTool("MediathequeActionButtonHandler").activeAllSubTools();


toolBox.getTool("AnimateTool").activeSubTool("ShowMediaFilter")

;