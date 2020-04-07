
class Tool {

    constructor()
    {
        this.__name = null;
        this.__isActived = false;
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