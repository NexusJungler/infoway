require("../css/popups/popup.scss");
require("../css/media/media_image.scss");

import ToolBox from "./class/Tools/ToolBox";

const toolBox = new ToolBox();
toolBox.activeTool("PaginatorHandler")
       .activeTool("FilterMediasTool")
       .activeTool("PopupHandler")
       .activeTool("AnimateTool")
;

toolBox.getTool("PopupHandler").activeSubTool("MediaInfoSheetHandler")
                                        .activeSubTool("MediaWaitingIncrustationHandler")
                                        .activeSubTool("MediaProductAssociationHandlerTool")
                                        .activeSubTool("MediaTagAssociationHandlerTool")
                                        .activeSubTool("UploadHandlerTool")
                                        .activeSubTool("MediaDeletingHandler")
;


toolBox.getTool("FilterMediasTool").activeSubTool("FilterMediasByTypeSubTool")
                                            .activeSubTool("FilterMediasByOrientationSubTool")
                                            .activeSubTool("FilterMediasByCharacteristicsSubTool")
;


toolBox.getTool("AnimateTool").activeSubTool("ShowMediaFilter")

;