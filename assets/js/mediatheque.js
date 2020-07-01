require("../css/popups/popup.scss");
require("../css/media/show_media.scss");

import ToolBox from "./class/Tools/ToolBox";

const toolBox = new ToolBox();
toolBox.activeTool("PaginatorHandler")
       .activeTool("FilterMediasTool")
       .activeTool("PopupHandler")
       .activeTool("AnimateTool")
;

toolBox.getTool("PopupHandler").activeSubTool("MediaInfoSheetHandler")
                                        .activeSubTool("MediaWaitingIncrustationHandler")
                                        .activeSubTool("AssociationPopupHandler", ["all"])
                                        .activeSubTool("UploadHandlerTool")
                                        .activeSubTool("MediaDeletingHandler")
;


toolBox.getTool("FilterMediasTool").activeSubTool("FilterMediasByTypeSubTool")
                                            .activeSubTool("FilterMediasByOrientationSubTool")
                                            .activeSubTool("FilterMediasByAssociatedDataSubTool")
                                            .activeSubTool("FilterMediasWithSearchBarSubTool")
;


toolBox.getTool("AnimateTool").activeSubTool("ShowMediaFilter")

;