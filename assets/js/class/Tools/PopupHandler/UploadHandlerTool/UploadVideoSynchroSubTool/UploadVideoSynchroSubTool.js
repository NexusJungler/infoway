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
        this.__$container= $('.popup_upload_container');
        this.__$location = $('.popup_upload');
        this.__synchroHtml = "";
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

    /**
     * @param {object} synchroElement
     * @returns {UploadVideoSynchroSubTool}
     */
    saveSynchroElement(synchroElement)
    {

        this.__synchroHtml += `
        
        <div class="synchro_element">

            <div class="synchro_element_preview_container">
                <video>
                    <source src="/miniatures/${ synchroElement.customer }/video/${ synchroElement.mediaType }/low/${ synchroElement.id }.mp4" type="video/mp4" />
                </video>
            </div>

            <div class="synchro_element_name_container">
                <input type="text" class="synchro_element_name" title="nom du média" value="${ synchroElement.name }">
            </div>

            <div class="synchro_element_position_container">
                <input type="text" class="synchro_element_position" title="position" value="${ synchroElement.position }">
            </div>

        </div>
        
        `;

        return this;
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

    showSynchros()
    {

    }

    onClickOnSynchroActionButton(active)
    {

        if(active)
        {

            this.__$location.on('click.onClickOnSynchroActionButton', '.synchro_action_button_icon', e => {

                const icon = $(e.currentTarget);

                if(icon.hasClass('fa-play'))
                {
                    console.log("play videos"); //debugger

                    this.__$location.find('video').map( (index, video) => {

                        video.play();

                    } )

                    // show pause icon
                    icon.removeClass('fa-play').addClass('fa-pause');

                    // add backward button (restart videos)
                    if(this.__$location.find('.restart_video_btn').length === 0)
                        $('<button type="button" class="synchro_action_button restart_video_btn"><i class="fas fa-step-backward synchro_action_button_icon"></i></button>').appendTo( icon.parents('.synchro_action_button_container') )
                }

                else if(icon.hasClass('fa-pause'))
                {
                    console.log("pause videos"); //debugger

                    this.__$location.find('video').map( (index, video) => {

                        video.pause();

                    } )

                    this.__$location.find('i.fa-pause').removeClass('fa-pause').addClass('fa-play');

                }

                else if(icon.hasClass('fa-step-backward'))
                {
                    console.log("restart videos"); //debugger

                    this.__$location.find('video').map( (index, video) => {

                        video.pause();
                        video.currentTime = 0;

                    } )

                    this.__$location.find('.restart_video_btn').remove();

                    this.__$location.find('i.fa-pause').removeClass('fa-pause').addClass('fa-play');

                }

                else
                {
                    console.log(icon); debugger
                }

            })
        }
        else
        {
            this.__$location.off('click.onClickOnSynchroActionButton', '.synchro_action_button');
        }

        return this;

    }



    enable()
    {
        super.enable();
        this.onClickOnSynchroActionButton(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnSynchroActionButton(false)
        ;
    }

}

export default UploadVideoSynchroSubTool;