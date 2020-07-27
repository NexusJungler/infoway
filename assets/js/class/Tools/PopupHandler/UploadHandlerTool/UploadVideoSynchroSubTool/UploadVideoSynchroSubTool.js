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
        this.__synchro = new Synchro();
        this.__synchroIsSend = false;
    }

    saveSynchroElement(element = { name: "", position: 0 })
    {

        const synchroElement = new SynchroElement();
        synchroElement.setName(element.name)
                      .setPosition(element.position);

        this.__synchro.addSynchroElement(synchroElement);

    }

    getSynchros()
    {
        this.__synchroIsSend = true;

        //let zonesToExport = Object.values(this.interface.currentTemplate.getZones()).map(zone=>{
        //             let zoneObject = Object.assign({}, zone);
        //             let zoneChildrens = Object.values(zone.zoneChildrens).map(children => {
        //                 console.log(children);
        //                 children = Object.assign({},children)
        //                 children.zoneParent = children.zoneParent.id
        //                 return children
        //             })
        //             zoneObject.zoneChildrens = zoneChildrens
        //             if( typeof zoneObject.parentZone ==='object' && zoneObject.parentZone instanceof Zone )zoneObject.parentZone = zoneObject.parentZone.id
        //             return zoneObject
        //         }).filter(zoneToExportWihtoutChild => {
        //             return zoneToExportWihtoutChild.zoneParent === null
        //         })


        return JSON.stringify( this.__synchro, Utils.getCircularReplacer() );
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