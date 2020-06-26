import SubTool from "../../SubTool";
import MediaTagAssociationHandlerTool from "./TagAssociationHandler/MediaTagAssociationHandlerTool";
import MediaProductAssociationHandlerTool from "./ProductAssociationHandler/MediaProductAssociationHandlerTool";

class AssociationPopupHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__subTools = [
            new MediaTagAssociationHandlerTool(),
            new MediaProductAssociationHandlerTool(),
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

    activeSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        this.__subTools[ this.getSubToolIndex(subToolName) ].setToolBox(this.getToolBox());
        this.__subTools[ this.getSubToolIndex(subToolName) ].setParent(this);
        this.__subTools[ this.getSubToolIndex(subToolName) ].enable();

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

export default AssociationPopupHandler;