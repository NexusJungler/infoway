class Media
{

    constructor()
    {
        this.__className = this.constructor.name;
        this.__id = null;
        this.__name = "";
        this.__extension = "";
        this.__height = null;
        this.__width = null;
        this.__html = null;
        this.__orientation = "";
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

    getExtension()
    {
        return this.__extension;
    }

    setExtension(extension)
    {
        if(typeof extension !== "string")
            throw new Error(`${ this.__className }.extension must be instance of string, but '${typeof extension}' given !`);

        this.__extension = extension;

        return this;
    }

    getHeight()
    {
        return this.__height;
    }

    setHeight(height)
    {
        if(typeof height !== "number")
            throw new Error(`${ this.__className }.height must be instance of number, but '${typeof height}' given !`);

        this.__height = height;

        return this;
    }

    getWidth()
    {
        return this.__width;
    }

    setWidth(width)
    {
        if(typeof width !== "number")
            throw new Error(`${ this.__className }.width must be instance of number, but '${typeof width}' given !`);

        this.__width = width;

        return this;
    }

    getOrientation()
    {
        return this.__orientation;
    }

    setOrientation(orientation)
    {
        if(typeof orientation !== "string")
            throw new Error(`${ this.__className }.orientation must be instance of string, but '${typeof orientation}' given !`);

        this.__orientation = orientation;

        return this;
    }

    /**
     * @returns {Media}
     */
    buildHtml()
    {

        this.__html =  ``;

        return this;
    }

    /**
     * @returns {string}
     */
    getHtml()
    {
        return this.__html;
    }

}

export default Media;