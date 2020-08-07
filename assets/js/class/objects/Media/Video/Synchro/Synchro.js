import Video from "../Video";
import SynchroElement from "./SynchroElement";


class Synchro
{

    constructor()
    {
        this.__id = null;
        this.__name = "";
        this.__className = this.constructor.name;
        this.__synchroElements = [];

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
            //debugger
            this.__synchroElements.push(synchroElement);
            synchroElement.addSynchro(this);
        }
        //console.log(this.__synchroElements); debugger
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

    formatObjectToExport()
    {

        this.__synchroElements = Object.values(this.getSynchroElements()).map(synchroElement => {

            console.log(synchroElement.getSynchros()); debugger

            synchroElement.getSynchros().forEach((synchro, index) => {




                synchroElement.getSynchros()[index] = this.getName();

            })

            return synchroElement;

        });

        return this;
    }

}

export default Synchro;