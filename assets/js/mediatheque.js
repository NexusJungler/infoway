require("../css/popups/popup.scss");
require("../css/popups/popup_media_waiting_incrustation/popup_media_waiting_incrustation.scss");
require("../css/popups/popup_media_info_sheet/popup_media_info_sheet.scss")
require("../css/popups/popup_delete_media/popup_delete_media.scss")

import ToolBox from "./class/Tools/ToolBox";

const toolBox = new ToolBox();
toolBox.activeTool("PaginatorHandler")
       .activeTool("FilterMediasTool")
       .activeTool("PopupHandler")
       .activeTool("MediaDeletingHandler")
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