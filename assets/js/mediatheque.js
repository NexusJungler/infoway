require("../css/popups/popup.scss");
require("../css/popups/popup_media_waiting_incrustation/popup_media_waiting_incrustation.scss");

import ToolBox from "./class/Tools/ToolBox";

const toolBox = new ToolBox();
toolBox.activeTool("PaginatorHandler")
       .activeTool("FilterMediasTool")
       .activeTool("PopupHandler")
;

toolBox.getTool("PopupHandler").activeSubTool("MediaInfoSheetHandler")
                                        .activeSubTool("MediaWaitingIncrustationHandler")
                                        .activeSubTool("MediaProductAssociationHandlerTool")
                                        .activeSubTool("MediaTagAssociationHandlerTool")
                                        .activeSubTool("UploadHandlerTool")
;


toolBox.getTool("FilterMediasTool").activeSubTool("FilterMediasByTypeSubTool")
                                            .activeSubTool("FilterMediasByOrientationSubTool")
                                            .activeSubTool("FilterMediasByCharacteristicsSubTool")
;