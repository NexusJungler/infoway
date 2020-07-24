import Media from "../Media";
import Synchro from "./Synchro/Synchro";

class Video extends Media
{

    constructor()
    {
        super();
        this.__className = this.constructor.name;
        this.__synchros= [];
    }

    getSynchros()
    {
        return this.__synchros;
    }

    addSynchro(synchro)
    {

        if( !(synchro instanceof Synchro) )
            throw new Error(`Parameter of ${ this.__className }.addSynchro() must be instance of Synchro, but '${typeof synchro}' given !`);

        if(!this.synchroIsAlreadyRegistered(synchro))
        {
            synchro.addVideo(this);
            this.__synchros.push(synchro);
        }

        return this;
    }

    removeSynchro(synchro)
    {

        if( !(synchro instanceof Synchro) )
            throw new Error(`Parameter of ${ this.__className }.removeSynchro() must be instance of Synchro, but '${typeof synchro}' given !`);

        if(this.synchroIsAlreadyRegistered(synchro))
        {
            this.__synchros.splice(this.getRegisteredVideoIndex(synchro) , 1);

            synchro.removeVideo(this);
        }

        return this;

    }

    removeAllSynchros()
    {
        this.__synchros.map( synchro => {
            synchro.removeVideo(this);
        } );

        this.__synchros = [];

        return this;
    }

    synchroIsAlreadyRegistered(synchro)
    {
        return this.getRegisteredSynchroIndex(synchro) !== -1;
    }

    getRegisteredSynchroIndex(synchro)
    {
        return this.__synchros.findIndex( registeredSynchro =>  registeredSynchro.getName() === synchro.getName() );
    }

}

export default Video;