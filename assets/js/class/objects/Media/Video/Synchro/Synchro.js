import Video from "../Video";
import SynchroElement from "./SynchroElement";


class Synchro extends Video
{

    constructor()
    {
        super();
        this.__className = this.constructor.name;
        this.__synchroElements = [];
        //this.__preview = "";

    }

    /*getPreview()
    {
        return this.__preview;
    }

    setPreview(preview) {

        if( typeof preview === 'undefined')
        {
            preview = this.buildHtml();
        }

        if(typeof preview !== "string")
            throw new Error(`${ this.__className }.preview must be instance of string, but '${typeof preview}' given !`);

        this.__preview = preview;

        return this;
    }*/

    getSynchroElements()
    {
        return this.__synchroElements;
    }

    addSynchroElement(synchroElement)
    {

        if( !(synchroElement instanceof SynchroElement) )
            throw new Error(`Parameter of ${ this.__className }.addVideo() must be instance of SynchroElement, but '${typeof synchroElement}' given !`);

        if(!this.synchroElementIsAlreadyRegistered(synchroElement))
        {
            this.__synchroElements.push(synchroElement);
            synchroElement.addSynchro(this);
        }

        return this;
    }

    removeSynchroElement(synchroElement)
    {

        if( !(synchroElement instanceof SynchroElement) )
            throw new Error(`Parameter of ${ this.__className }.addVideo() must be instance of SynchroElement, but '${typeof synchroElement}' given !`);

        if(this.synchroElementIsAlreadyRegistered(synchroElement))
        {
            this.__synchroElements.splice(this.getRegisteredSynchroElementIndex(synchroElement) , 1);

            synchroElement.removeSynchro(this);
        }

        return this;
    }

    removeAllSynchroElements()
    {
        this.__synchroElements.map( synchroElement => {
            synchroElement.removeSynchro(this);
        } );

        this.__synchroElements = [];

        return this;
    }

    synchroElementIsAlreadyRegistered(synchroElement)
    {
        return this.getRegisteredSynchroElementIndex(synchroElement) !== -1;
    }

    getRegisteredSynchroElementIndex(synchroElement)
    {
        return this.__synchroElements.findIndex( registeredSynchroElement =>  registeredSynchroElement.getName() === synchroElement.getName() );
    }

    buildHtml()
    {
        return ``;
    }

}

export default Synchro;