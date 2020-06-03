require("../css/popups/popup.scss");
require("../css/popups/popup_media_waiting_incrustation/popup_media_waiting_incrustation.scss");

import ToolBox from "./class/Tools/ToolBox/ToolBox";

const toolBox = new ToolBox();
toolBox.activeTool("MediaProductAssociationHandlerTool")
       .activeTool("MediaTagAssociationHandlerTool")
       .activeTool("PaginatorHandler")
       .activeTool("FilterMediasTool")
       .activeTool("MediaWaitingIncrustationHandler")
;

toolBox.getTool("FilterMediasTool").activeSubTool("FilterMediasByTypeSubTool")
                                            .activeSubTool("FilterMediasByOrientationSubTool")
                                            .activeSubTool("FilterMediasByCharacteristicsSubTool")
;