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
        this.__encodedMediaInfos = [];
    }

    /**
     * @param {object} element
     * @returns {UploadVideoSynchroSubTool}
     */
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

        return this;
    }

    /**
     * @param {object} encodedMediaInfos
     * @return {UploadVideoSynchroSubTool}
     */
    saveEncodedMediaInfos(encodedMediaInfos)
    {

        console.table(encodedMediaInfos);

        if(!this.encodedMediaInfosIsAlreadyRegistered(encodedMediaInfos))
        {
            this.__encodedMediaInfos.push( encodedMediaInfos );
        }

        console.table(this.__encodedMediaInfos);

        debugger

        return this;
    }

    /**
     * @return {array}
     */
    getAllEncodedMediasInfos()
    {
        return this.__encodedMediaInfos;
    }

    encodedMediaInfosIsAlreadyRegistered(encodedMediaInfos)
    {
        return this.getEncodedMediaInfosIndex(encodedMediaInfos) !== -1;
    }

    getEncodedMediaInfosIndex(encodedMediaInfos)
    {
        return this.__encodedMediaInfos.findIndex( mediaInfos => mediaInfos.fileName === encodedMediaInfos.fileName );
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

        let html =
        `    <form name="synchro_edit_form" id="synchro_edit_form" action="/save/synchro/infos" method="post">
            <div class="top">
            
                <div class="synchro_name_container container">

                    <div class="label_container"><label for="synchro_name">Nom</label></div>
                    <div class="input_container"><input type="text" id="synchro_name" name="synchro_edit_form[synchro_name]" placeholder="Nom de la synchro" class="synchro_name"></div>
                    <div class="synchro_action_button_container"><button type="button" class="synchro_action_button"><i class="fas fa-play synchro_action_button_icon"></i></button></div>
        
                </div>
       
            <div class="synchro_elements_container container">
        
        `;

        this.getAllEncodedMediasInfos().forEach( (mediaInfos, index) => {

            html += `
            
                <div class="synchro_element">

                    <div class="synchro_element_preview_container">
                        <video>
                            <source src="/miniatures/kfc/video/sync/low/${ mediaInfos.id }.mp4" type="video/mp4" />
                        </video>
                    </div>
    
                    <div class="synchro_element_name_container">
                        <span class="error hidden"></span>
                        <input type="text" class="synchro_element_name" title="nom du média" name="synchro_edit_form[synchros_elements][${index}][name]" value="${ mediaInfos.name }">
                    </div>
    
                    <div class="synchro_element_position_container">
                        <span class="error hidden"></span>
                        <input type="text" class="synchro_element_position" title="position" name="synchro_edit_form[synchros_elements][${index}][position]" value="${ index +1 }">
                    </div>
    
                </div>
            
            `;

        } );

        html += "</div></div></form>";

        html += `
        
            <div class="bottom">

                <div class="synchro_associated_data_container">
        
                    <table>
        
                        <thead>
                            <tr>
                                <th>Critères</th>
                                <th>Tags</th>
                                <th>Produits associés</th>
                            </tr>
                        </thead>
        
                        <tbody>
        
                            <tr>
                                <td class="synchro_criterions_container"></td>
                                <td class="synchro_tags_container"></td>
                                <td class="synchro_products_container"></td>
                            </tr>
        
                        </tbody>
        
                    </table>
        
                </div>
        
            </div>
        
        `;

        return html;

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

    synchroEditFormIsValid()
    {

        let form = $('#synchro_edit_form');

        // @TODO: vérifier validité du formulaire (span.error, ...)

    }

    onClickOnSynchroSaveButton(active)
    {

        if(active)
        {
            this.__$location.on('click.onClickOnSynchroSaveButton', '.save_synchro_edits_button', e => {

                if(this.synchroEditFormIsValid())
                {

                    let formData = new FormData( $('#synchro_edit_form')[0] );

                    super.showLoadingPopup();

                    $.ajax({
                        url: `/save/synchro/infos`,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: (response) => {

                            //console.log(response); //debugger

                            this.__$fileToCharacterisationList.find('.unregistered').removeClass('unregistered');

                        },
                        error: (response) => {

                            let error = response.responseJSON;

                            let subject = error.subject;

                            switch (error.text)
                            {

                                case "515 Duplicate File":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).text( this.__errors.duplicate_file ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .form_input.media_name`).addClass('invalid');
                                    break;

                                case "516 Invalid Filename":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).text( this.__errors.invalid_error ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container .form_input.media_name`).addClass('invalid');
                                    break;

                                case "517 Empty Filename":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).text( this.__errors.empty_error ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container .form_input.media_name`).addClass('invalid');
                                    break;

                                case "518 Too short Filename":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).text( this.__errors.too_short_error ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container .form_input.media_name`).addClass('invalid');
                                    break;

                                case "519.2 Invalid diffusion end date":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container span.error`).text( this.__errors.invalid_diffusion_end_date ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container .diffusion_dates.end`).addClass('invalid');
                                    break;

                                case "520 Position already used":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container span.error`).text( this.__errors.invalid_diffusion_end_date ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container .diffusion_dates.end`).addClass('invalid');
                                    break;

                                default:
                                    console.error(error); debugger

                            }
                        },
                        complete: () => {
                            super.hideLoadingPopup();
                        },

                    });

                }

            })
        }
        else
        {
            this.__$location.off('click.onClickOnSynchroSaveButton', '.save_synchro_edits_button');
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