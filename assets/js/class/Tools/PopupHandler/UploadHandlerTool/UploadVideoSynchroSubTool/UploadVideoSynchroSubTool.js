import ParentTool from "../../../ParentTool";
import SubTool from "../../../SubTool";
import Synchro from "../../../../objects/Media/Video/Synchro/Synchro";
import SynchroElement from "../../../../objects/Media/Video/Synchro/SynchroElement";
import Utils from "../../../../Utils/Utils";

class UploadVideoSynchroSubTool extends SubTool {

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__synchro = (new Synchro()).setName("Synchro1");
        this.__synchroIsSend = false;
    }

    saveSynchroElement(element = { name: "" })
    {

        let lastItem = this.__synchro.getSynchroElements()[ this.__synchro.getSynchroElements().length-1 ];
        const synchroElement = new SynchroElement();
        synchroElement.setName(element.name);

        if( lastItem instanceof SynchroElement)
        {
            //console.log(lastItem); debugger

            // par défaut la position est à 0
            // on incremente la position de l'element si la position est déjà utilisé par le dernier synchroElement dans la liste
            if(lastItem.getPosition() === synchroElement.getPosition())
                synchroElement.setPosition( synchroElement.getPosition() +1 );

        }

        this.__synchro.addSynchroElement(synchroElement);

        console.log(this.__synchro); //debugger

    }

    getSynchros()
    {
        this.__synchroIsSend = true;

        let synchroToExport = this.__synchro.formatObjectToExport();

        //console.log(synchroToExport); debugger

        return JSON.stringify( synchroToExport );
    }

    getSynchroElementByName(name)
    {

        let element = null;

        this.__synchro.getSynchroElements().forEach( (synchroElement) => {

            if(synchroElement.getName() === name)
                element =  synchroElement;

        } )

        element.__synchros = this.__synchro.getName();

        return JSON.stringify(element);
    }

    synchroIsAlreadySend()
    {
        return this.__synchroIsSend;
    }

    synchroIsAlreadyRegistered(synchro)
    {
        return this.getRegisteredSynchroIndex(synchro) !== -1;
    }

    getRegisteredSynchroIndex(synchro)
    {
        return this.__synchros.findIndex( registeredSynchro =>  registeredSynchro.getName() === synchro.getName() );
    }

    showMediaInfoForEdit(mediaInfos, index)
    {

        let html = `
        
        <div class="synchro_resume_elements_container">
        
            <div class="synchro_resume_element top">
            
                <div class="synchro_name_container"> 
                    <span>Nom</span> 
                    <p title="${ mediaInfos.fileName }">${ mediaInfos.fileName }</p> 
                    <button type="button" class="play_synchro_button"><i class="fas fa-play"></i></button>
                </div>
                
                <div class="synchro_videos_container">
                    
                </div>
                
            
            </div>
            
            <div class="synchro_resume_element bottom">
            
            </div>
        
        </div>
        
        `;

        return html;

    }

    enable()
    {
        super.enable();
    }

    disable()
    {
        super.disable();
    }

}

export default UploadVideoSynchroSubTool;