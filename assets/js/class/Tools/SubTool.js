import ToolBox from "./ToolBox";
import Tool from "./Tool";
import ParentTool from "./ParentTool";

class SubTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__toolBox = null;
        this.__parent = null;
        this.__subTools = [];
    }

    setParent(parent)
    {
        /*console.log(parent)
        debugger*/
        if( !(parent instanceof ParentTool) )
            throw new Error(`Parameter of ${ this.__name }.setParent() must be instance of Tool, but '${typeof parent}' given !`);

        this.__parent = parent;
    }

    getParent()
    {
        return this.__parent;
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

export default SubTool;