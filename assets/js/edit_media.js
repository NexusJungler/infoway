require("../css/popups/popup.scss");
require("../css/media/edit_media.scss");

import ToolBox from "./class/Tools/ToolBox";

const toolBox = new ToolBox();

toolBox.activeTool("PopupHandler")
       .activeTool("MediathequeActionButtonHandler")
;

toolBox.getTool("PopupHandler").activeSubTool("MediaExpandedMiniatureDisplayHandler")
                                        .activeSubTool("MediaReplacementPopupHandler")
                                        .activeSubTool("AssociationPopupHandler", ["all"])
;

toolBox.getTool("MediathequeActionButtonHandler").activeAllSubTools();