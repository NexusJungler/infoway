import Media from "../Media";
import Synchro from "./Synchro/Synchro";

class Video extends Media
{

    constructor()
    {
        super();
        this.__className = this.constructor.name;
        this.__codec = null;
    }

    getCodec()
    {
        return this.__codec;
    }

    setCodec(codec)
    {

        if(typeof codec !== "string")
            throw new Error(`${ this.__className }.codec must be instance of string, but '${typeof codec}' given !`);

        this.__codec = codec;

        return this;
    }

}

export default Video;