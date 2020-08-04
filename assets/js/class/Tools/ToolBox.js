import CustomerCreatorHandler from "./CustomerCreatorHandler/CustomerCreatorHandler";
import UploadHandlerTool from "./PopupHandler/UploadHandlerTool/UploadHandlerTool";
import {ClockManager} from "../Managers/ClockManager/ClockManager";
import ProductAssociationHandlerTool from "./PopupHandler/AssociationPopupHandler/ProductAssociationHandler/ProductAssociationHandlerTool";
import TagAssociationHandlerTool from "./PopupHandler/AssociationPopupHandler/TagAssociationHandler/TagAssociationHandlerTool";
import ArchivedMediasHandlerTool from "./PopupHandler/ArchivedMediasHandler/ArchivedMediasHandlerTool";
import PaginatorHandler from "./PaginatorHandler/PaginatorHandler";
import FilterMediasTool from "./FilterMediasTool/FilterMediasTool";
import MediaWaitingIncrustationHandler from "./PopupHandler/MediaWaitingIncrustationHandler/MediaWaitingIncrustationHandler";
import PopupHandler from "./PopupHandler/PopupHandler";
import AnimateTool from "./AnimationTool/AnimateTool";
import MediathequeActionButtonHandler from "./MediathequeActionButtonHandler/MediathequeActionButtonHandler";
import MediaReplacementPopupHandler from "./PopupHandler/MediaReplacementPopupHandler/MediaReplacementPopupHandler";
import DraggableTool from "./DraggableTool/DraggableTool";

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
            //new ArchivedMediasHandlerTool(),
            new PaginatorHandler(),
            new FilterMediasTool(),
            new PopupHandler(),
            new MediathequeActionButtonHandler(),
            new AnimateTool(),
            new MediaReplacementPopupHandler(),
            new DraggableTool(),
        ];
    }

    activeTool(toolName)
    {

        if(typeof toolName !== 'string' || typeof toolName === "undefined" || toolName === "" || toolName === null)
            throw new Error(`Parameter of ToolBox.activeTool() must be instance of string, but '${typeof toolName}' given !`);

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
        this.__tools.map( tool => {

            if(this.toolIsRegistered(tool.getName()))
            {
                tool.setToolBox(this);
                tool.enable();
            }

            else
                throw new Error(`'${tool.getName()}' tool is not registered !`);

        } );

        return this;
    }

    disableTool(toolName)
    {
        if(typeof toolName !== 'string')
            throw new Error(`Parameter of ToolBox.disableTool() must be instance of string, but '${typeof toolName}' given !`);

        if(this.toolIsRegistered(toolName))
            this.__tools[ this.getToolIndex(toolName) ].disable();

        else
            throw new Error(`'${toolName}' tool is not registered !`);

        return this;
    }

    disableAllTools()
    {
        this.__tools.map( tool => (tool.isActive()) ? tool.disable() : null );
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