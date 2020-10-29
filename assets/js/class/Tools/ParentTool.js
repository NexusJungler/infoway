import Tool from "./Tool";
import SubTool from "./SubTool";

class ParentTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$mediasContainer = $(".medias_list_container");
        this.__subTools = [];

    }

    getAgainMediaListContainer()
    {
        this.__$mediasContainer = $(".medias_list_container");
    }

    getMediasContainer()
    {
        this.__$mediasContainer = $(".medias_list_container")
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
        //console.log(this.__subTools[ this.getSubToolIndex(subToolName) ]); debugger
        //console.log(this.getToolBox()); debugger
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

    disableSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered as UploadSubTool !`);

        if(this.__subTools[ this.getSubToolIndex(subToolName) ].isActive)
            this.__subTools[ this.getSubToolIndex(subToolName) ].disable();

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

}

export default ParentTool;