import Media from "../Media";
import Synchro from "./Synchro/Synchro";

class Video extends Media
{

    constructor()
    {
        super();
        this.__className = this.constructor.name;
    }

}

export default Video;