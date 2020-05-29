import ToolBox from "./class/Tools/ToolBox/ToolBox";

const toolBox = new ToolBox();
toolBox.activeTool("MediaProductAssociationHandlerTool")
       .activeTool("MediaTagAssociationHandlerTool")
       .activeTool("PaginatorHandler")
       .activeTool("FilterMediasTool")
;

toolBox.getTool("FilterMediasTool").activeSubTool("FilterMediasByTypeSubTool")
    //.activeTool("FilterMediasByOrientation")
;