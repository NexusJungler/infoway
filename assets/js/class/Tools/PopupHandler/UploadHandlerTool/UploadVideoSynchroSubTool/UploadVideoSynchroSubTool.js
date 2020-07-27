import ParentTool from "../../../ParentTool";
import SubTool from "../../../SubTool";
import Synchro from "../../../../objects/Media/Video/Synchro/Synchro";

class UploadVideoSynchroSubTool extends SubTool {

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__synchros = [];
    }

    saveMediaInfos(synchroInfos)
    {

        const synchro = new Synchro();
        synchro.setId(synchroInfos['id'])
               .setName(synchroInfos['fileNameWithoutExtension'])
               .setPosition(this.__synchros.length)
               .setPreview();

        if(!this.synchroIsAlreadyRegistered(synchro))
        {
            synchro.addVideo(this);
            this.__synchros.push(synchro);
        }

        console.table(this.__synchros); debugger

        return this;
    }

    removeSynchro(synchro)
    {

        if( !(synchro instanceof Synchro) )
            throw new Error(`Parameter of ${ this.__className }.removeSynchro() must be instance of Synchro, but '${typeof synchro}' given !`);

        if(this.synchroIsAlreadyRegistered(synchro))
            this.__synchros.splice(this.getRegisteredVideoIndex(synchro) , 1);

        return this;
    }

    removeAllSynchros()
    {
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