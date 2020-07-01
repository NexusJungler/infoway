import Tool from "../Tool";
import MediaInfoSheetHandler from "./MediaInfoSheetHandler/MediaInfoSheetHandler";
import MediaWaitingIncrustationHandler from "./MediaWaitingIncrustationHandler/MediaWaitingIncrustationHandler";
import UploadHandlerTool from "./UploadHandlerTool/UploadHandlerTool";
import MediaDeletingHandler from "./MediaDeletingHandler/MediaDeletingHandler";
import AssociationPopupHandler from "./AssociationPopupHandler/AssociationPopupHandler";

class PopupHandler extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__subTools = [
            new MediaInfoSheetHandler(),
            new MediaWaitingIncrustationHandler(),
            new UploadHandlerTool(),
            new MediaDeletingHandler(),
            new AssociationPopupHandler(),
        ];

        this.__$mediasContainer = $(".medias_list_container");
    }

    getAgainMediaListContainer()
    {
        this.__$mediasContainer = $(".medias_list_container");
    }

    getMediasContainer()
    {
        return this.__$mediasContainer;
    }

    activeAllSubTools()
    {

        this.__subTools.map( subTool => {

            if(this.subToolIsRegistered(subTool.getName()))
            {
                subTool.setToolBox(this.getToolBox());
                subTool.setParent(this);
                subTool.enable();
            }

            else
                throw new Error(`'${subTool.getName()}' subTool is not registered !`);

        } );

        return this;

    }

    activeSubTool(subToolName, subToolToolsToActive = [])
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        this.__subTools[ this.getSubToolIndex(subToolName) ].setToolBox(this.getToolBox());
        this.__subTools[ this.getSubToolIndex(subToolName) ].setParent(this);
        this.__subTools[ this.getSubToolIndex(subToolName) ].enable();

        if(subToolToolsToActive.length > 0)
        {

            subToolToolsToActive.map( (subToolToolToActive) => {

                if(subToolToolToActive === "all")
                    this.__subTools[ this.getSubToolIndex(subToolName) ].activeAllSubTools();

                else
                    this.__subTools[ this.getSubToolIndex(subToolName) ].activeSubTool(subToolToolToActive);

            } )

        }

        return this;
    }

    getSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        return this.__subTools[ this.getSubToolIndex(subToolName) ];

    }

    subToolIsRegistered(subToolName)
    {
        return this.getSubToolIndex( subToolName ) !== -1;
    }

    getSubToolIndex(subToolName)
    {
        return this.__subTools.findIndex( subTool =>  subTool.getName() === subToolName );
    }

    enable()
    {
        super.enable();
    }

    disable()
    {
        super.disable();
    }

}

export default PopupHandler;