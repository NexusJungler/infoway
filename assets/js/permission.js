
import PermissionHandlerTool from "./class/Tools/PermissionHandlerTool/PermissionHandlerTool";

const $ = require('jquery');
global.$ = global.jQuery = $;

const permissionHandlerTool = new PermissionHandlerTool();
permissionHandlerTool.enable();