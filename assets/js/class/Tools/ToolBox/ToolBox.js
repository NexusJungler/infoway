import CustomerCreatorHandler from "../CustomerCreatorHandler/CustomerCreatorHandler";
import UploadHandlerTool from "../UploadHandlerTool/UploadHandlerTool";
import {ClockManager} from "../../Managers/ClockManager/ClockManager";
import ProductAssociationHandlerTool from "../ProductAssociationHandler/ProductAssociationHandlerTool";

class ToolBox
{

    constructor()
    {
        this.__tools = [];
        this.registerTools();
    }

    getTool(toolName)
    {
        if(!this.toolIsRegistered(toolName))
            throw new Error(`'${toolName}' tool is not registered !`);

        return this.__tools[ this.getToolIndex(toolName) ];
    }

    registerTools()
    {
        this.__tools = [
            new CustomerCreatorHandler(),
            new UploadHandlerTool(),
            new ProductAssociationHandlerTool()
        ];
    }

    activeTool(toolName)
    {

        if(typeof toolName !== 'string' || typeof toolName === "undefined" || toolName === "" || toolName === null)
            throw new Error("Invalid 'toolName' form ToolBox::loadTool()");

        if(this.toolIsRegistered(toolName))
        {
            this.__tools[ this.getToolIndex(toolName) ].setToolBox(this);
            this.__tools[ this.getToolIndex(toolName) ].enable();
        }

        else
            throw new Error(`'${toolName}' tool is not registered !`);

        return this;
    }

    activeAllTools()
    {
        this.__tools.map( tool => tool.enable() );
        return this;
    }

    disableTool(toolName)
    {
        if(typeof toolName !== 'string')
            throw new Error("Invalid 'toolName' form ToolBox::loadTool()");

        if(this.toolIsRegistered(toolName))
            this.__tools[ this.getToolIndex(toolName) ].disable();

        else
            throw new Error(`'${toolName}' tool is not registered !`);

        return this;
    }

    disableAllTools()
    {
        this.__tools.map( tool => (tool.toolIsActived()) ? tool.disable() : null );
        return this;
    }

    toolIsRegistered(toolName)
    {
        return this.getToolIndex( toolName ) !== -1;
    }

    getToolIndex(toolName)
    {
        return this.__tools.findIndex( tool =>  tool.getName() === toolName );
    }

}

export default ToolBox;