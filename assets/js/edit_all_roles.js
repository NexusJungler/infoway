require("../css/popups/popup.scss");
require("../css/settings/role/edit_all_roles.scss");

import ToolBox from "./class/Tools/ToolBox";

const toolBox = new ToolBox();

toolBox.activeTool("RolePermissionHandler");

toolBox.getTool("RolePermissionHandler").activeAllSubTools();