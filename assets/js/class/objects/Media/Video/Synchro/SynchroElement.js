import Video from "../Video";
import Synchro from "./Synchro";

class SynchroElement extends Video
{

    constructor()
    {
        super();
        this.__className = this.constructor.name;
        this.__preview = "";
        this.__position = 0;
        this.__synchros = [];
    }

    /**
     * @returns {string}
     */
    getPreview()
    {
        return this.__preview;
    }

    /**
     *
     * @param {string} preview
     * @returns {SynchroElement}
     */
    setPreview(preview) {

        if(typeof preview !== "string")
            throw new Error(`${ this.__className }.preview must be instance of string, but '${typeof preview}' given !`);

        this.__preview = preview;

        return this;
    }

    /**
     * @returns {number}
     */
    getPosition()
    {
        return this.__position;
    }

    /**
     * @param {number} position
     * @returns {SynchroElement}
     */
    setPosition(position)
    {

        if(typeof position !== "number")
            throw new Error(`${ this.__className }.position must be instance of number, but '${typeof position}' given !`);

        this.__position = position;

        return this;

    }

    /**
     * @returns {[]}
     */
    getSynchros()
    {
        return this.__synchros;
    }

    /**
     * @param {Synchro} synchro
     * @returns {SynchroElement}
     */
    addSynchro(synchro)
    {

        if( !(synchro instanceof Synchro) )
            throw new Error(`Parameter of ${ this.__className }.addSynchro() must be instance of Synchro, but '${typeof synchro}' given !`);

        if(!this.synchroIsAlreadyRegistered(synchro))
        {
            this.__synchros.push(synchro);
            synchro.addSynchroElement(this);
        }

        return this;
    }

    /**
     * @param {Synchro} synchro
     * @returns {SynchroElement}
     */
    removeSynchro(synchro)
    {

        if( !(synchro instanceof Synchro) )
            throw new Error(`Parameter of ${ this.__className }.removeSynchro() must be instance of Synchro, but '${typeof synchro}' given !`);

        if(this.synchroIsAlreadyRegistered(synchro))
        {
            this.__synchros.splice(this.getRegisteredSynchroIndex(synchro) , 1);

            synchro.removeSynchroElement(this);
        }

        return this;

    }

    /**
     * @returns {SynchroElement}
     */
    removeAllSynchros()
    {
        this.__synchros.map( synchro => {
            synchro.removeSynchroElement(this);
        } );

        this.__synchros = [];

        return this;
    }

    /**
     * @param {Synchro} synchro
     * @returns {boolean}
     */
    synchroIsAlreadyRegistered(synchro)
    {
        return this.getRegisteredSynchroIndex(synchro) !== -1;
    }

    /**
     * @param {Synchro} synchro
     * @returns {number}
     */
    getRegisteredSynchroIndex(synchro)
    {
        return this.__synchros.findIndex( registeredSynchro =>  registeredSynchro.getName() === synchro.getName() );
    }

    /**
     * @returns {SynchroElement}
     */
    buildHtml()
    {

        this.__html =  `
        
        <div class="synchro" id="synchro_video_${ this.__id }">
            
            <div class="synchro_preview_container">
                ${ this.__preview }
            </div>
            
            <div class="synchro_video_name_container">
                <p class="synchro_video_name"> ${ this.__name } </p>
            </div>
            
            <div class="synchro_video_position_container">
                <p class="synchro_video_position"> ${ this.__position } </p>
            </div>
            
        </div>
        
        `;

        return this;
    }

}

export default SynchroElement;