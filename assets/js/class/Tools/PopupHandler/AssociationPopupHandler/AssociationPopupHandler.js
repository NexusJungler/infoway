import SubTool from "../../SubTool";
import ProductAssociationHandlerTool from "./ProductAssociationHandler/ProductAssociationHandlerTool";
import TagAssociationHandlerTool from "./TagAssociationHandler/TagAssociationHandlerTool";

class AssociationPopupHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__subTools = [
            new TagAssociationHandlerTool(),
            new ProductAssociationHandlerTool(),
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

            subTool.setToolBox(this.getToolBox());
            subTool.setParent(this.getParent());
            subTool.enable();

        } );

        return this;

    }

    activeSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        this.__subTools[ this.getSubToolIndex(subToolName) ].setToolBox(this.getToolBox());
        this.__subTools[ this.getSubToolIndex(subToolName) ].setParent(this.getParent());
        this.__subTools[ this.getSubToolIndex(subToolName) ].enable();

        return this;
    }

    disableSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        this.__subTools[ this.getSubToolIndex(subToolName) ].disable();

        return this;

    }

    disableAllSubTool()
    {
        this.__subTools.map( subTool => {

            subTool.disable();

        } );

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