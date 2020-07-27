class Media
{

    constructor()
    {
        this.__className = this.constructor.name;
        this.__id = null;
        this.__name = null;
    }

    getId()
    {
        return this.__id;
    }

    setId(id)
    {

        if( typeof id !== "number" )
            throw new Error(`${ this.__className }.id must be instance of number, but '${typeof id}' given !`);

        this.__id = id;

        return this;
    }

    getName() {
        return this.__name;
    }

    setName(name) {

        if(typeof name !== "string")
            throw new Error(`${ this.__className }.name must be instance of string, but '${typeof name}' given !`);

        this.__name = name;

        return this;
    }

}

export default Media;