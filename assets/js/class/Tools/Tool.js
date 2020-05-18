import ToolBox from "./ToolBox/ToolBox";

class Tool {

    constructor()
    {
        this.__name = null;
        this.__isActived = false;
        this.__toolBox = null;
    }

    getName()
    {
        return this.__name;
    }

    setName(name)
    {

        if(typeof name !== 'string' || typeof name === "undefined" || name === "" || name === null)
            throw new Error("Invalid 'name' parameter for Tool::setName()");

        this.__name = name;

        return this;
    }

    setToolBox(toolBox)
    {

        if( !(toolBox instanceof ToolBox) )
            throw new Error("Invalid 'toolBox' parameter for Tool::setToolBox()");

        this.__toolBox = toolBox;
        console.log(`ToolBox is now added in '${this.__name}' !`);

        return this;
    }

    getToolBox()
    {
        return this.__toolBox;
    }

    toolIsActived()
    {
        return this.__isActived;
    }

    enable()
    {
        console.log(`'${this.__name}' is enabled now !`);
        this.__isActived = true;
    }

    disable()
    {
        console.log(`'${this.__name}' is disabled now !`);
        this.__isActived = false;
    }

}

export default Tool;