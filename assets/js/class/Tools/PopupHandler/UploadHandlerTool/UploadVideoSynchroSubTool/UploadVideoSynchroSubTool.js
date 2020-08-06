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
        this.__$synchroContainer = $('.synchro_container');
        this.__errors = {
            empty_field: "Ce champ est obligatoire",
            duplicate_name: "Ce nom est déjà utilisé",
            duplicate_position : "Cette position est déjà utilisée"
        }
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

    updateSynchroElements(elements)
    {

        elements.forEach( (element, index) => {

            this.__synchro.getSynchroElements().forEach( (synchroElement, index) => {

                if(synchroElement.getName() === element.name)
                {
                    synchroElement.setId( element.id )
                                  .setWidth(element.width)
                                  .setHeight(element.height)
                                  .setExtension(element.extension)
                                  .setCodec( element.codec );
                }

            } )


        } )

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

        //console.table(this.__encodedMediaInfos);

        //debugger

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
                    <div class="input_container"><input type="text" id="synchro_name" name="synchro_edit_form[synchro_name]" placeholder="Nom de la synchro" class="synchro_name form_input"></div>
                    <div class="synchro_action_button_container"><button type="button" class="synchro_action_button"><i class="fas fa-play synchro_action_button_icon"></i></button></div>
        
                </div>
       
            <div class="synchro_elements_container container">
        
        `;

        this.getAllEncodedMediasInfos().forEach( (mediaInfos, index) => {

            html += `
            
                <div class="synchro_element" id="synchro_element_${ index }">

                    <div class="synchro_element_preview_container">
                        <video>
                            <source src="/miniatures/kfc/video/sync/low/${ mediaInfos.id }.mp4" type="video/mp4" />
                        </video>
                    </div>
    
                    <div class="synchro_element_name_container">
                        <span class="error hidden"></span>
                        <input type="text" class="synchro_element_name form_input" title="nom du média" name="synchro_edit_form[synchros_elements][${index}][name]" value="${ mediaInfos.name }">
                    </div>
    
                    <div class="synchro_element_position_container">
                        <span class="error hidden"></span>
                        <input type="text" class="synchro_element_position form_input" title="position" name="synchro_edit_form[synchros_elements][${index}][position]" value="${ index +1 }">
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

        this.__$synchroContainer.html(html);

        return this;

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

                }

                else if(icon.hasClass('fa-pause'))
                {
                    console.log("pause videos"); //debugger

                    this.__$location.find('video').map( (index, video) => {

                        video.pause();

                    } )

                }

                else if(icon.hasClass('fa-step-backward'))
                {
                    console.log("restart videos"); //debugger

                    this.__$location.find('video').map( (index, video) => {

                        video.pause();
                        video.currentTime = 0;

                    } )

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

    onClickOnDraggableButton(active)
    {
        if(active)
        {
            this.__$synchroContainer.on('click.onClickOnDraggableButton', '.drag_video_btn', e => {

                if($(e.currentTarget).hasClass('active'))
                {
                    //this.__$synchroContainer.find('.synchro_element').removeClass('draggable');
                    $(e.currentTarget).removeClass('active');
                    this.__$synchroContainer.find('.form_input').removeAttr('readonly');
                    this.__$synchroContainer.find('.synchro_element').removeClass('draggable');
                    this.onMouseDownOnSynchroElement(false);
                }
                else
                {
                    //this.__$synchroContainer.find('.synchro_element').addClass('draggable');
                    $(e.currentTarget).addClass('active');
                    this.__$synchroContainer.find('.synchro_element').addClass('draggable');
                    this.__$synchroContainer.find('.form_input').attr('readonly', true);
                    this.onMouseDownOnSynchroElement(true);
                }

            })
        }
        else
        {
            this.__$synchroContainer.off('click.onClickOnDraggableButton', '.drag_video_btn');
        }

        return this;
    }

    onMouseDownOnSynchroElement(active)
    {

        if(active)
        {

            this.__$synchroContainer.on('mousedown.onMouseDownOnSynchroElement', '.synchro_element', e => {

                // @see: https://stackoverflow.com/questions/4658300/jquery-sortable-without-jquery-ui
                // @see: https://jsfiddle.net/606bs750/16/

                if (e.which === 1)
                {

                    e.preventDefault();

                    let currentElementOnDrag = $(e.currentTarget);
                    let parentWidth = currentElementOnDrag.parent().innerWidth();
                    let left = (isNaN(parseInt(currentElementOnDrag.css('left')))) ? 0 : parseInt(currentElementOnDrag.css('left'));
                    let originalXPos = currentElementOnDrag.position().left;
                    let dragMinXPos = 0 - originalXPos;
                    let dragMaxXPos = parentWidth - originalXPos - currentElementOnDrag.outerWidth();
                    let dragStartXPos = e.clientX;
                    let myXPos = originalXPos;

                    $('.synchro_container .synchro_element').each( (index, element) => {
                        $(element).attr('data-order', (index + 1));
                    });

                    let prevSynchroElement = currentElementOnDrag.prev('.synchro_element');
                    let nextSynchroElement = currentElementOnDrag.next('.synchro_element');
                    let prevSynchroElementXPos = prevSynchroElement.length > 0 ? prevSynchroElement.position().left : '';
                    let nextSynchroElementXPos = nextSynchroElement.length > 0 ? nextSynchroElement.position().left : '';

                    $(window).on('mousemove', e => {

                        currentElementOnDrag.addClass('ondrag');
                        let direction = myXPos > currentElementOnDrag.position().left ? 'left' : 'right';
                        let newLeft = 0 + (e.clientX - dragStartXPos);
                        myXPos = currentElementOnDrag.position().left;
                        currentElementOnDrag.css({
                            left: newLeft + 'px'
                        });

                        /*if (newLeft < dragMinXPos) {
                            currentElementOnDrag.css({
                                left: dragMinXPos + 'px'
                            });
                        }

                        if (newLeft > dragMaxXPos) {
                            currentElementOnDrag.css({
                                left: dragMaxXPos + 'px'
                            });
                        }*/

                        console.log("my : " + myXPos);
                        console.log("next : " + nextSynchroElementXPos);

                        let tempOrder = (direction === 'right') ? nextSynchroElement.data('order') : prevSynchroElement.data('order');
                        let tempPosition = (direction === 'right') ? nextSynchroElement.find('.synchro_element_position_container .synchro_element_position').val() : prevSynchroElement.find('.synchro_element_position_container .synchro_element_position').val();

                        if(direction === 'right' && nextSynchroElementXPos !== '')
                        {
                            if(myXPos >= nextSynchroElementXPos)
                            {
                                nextSynchroElement.css({ left: parseInt(nextSynchroElement.css('left') - nextSynchroElement.outerWidth() + 'px') });

                                nextSynchroElement.attr('data-order', currentElementOnDrag.attr('data-order'))
                                                  .find('.synchro_element_position_container .synchro_element_position').attr('value', currentElementOnDrag.find('.synchro_element_position_container .synchro_element_position').val());



                                currentElementOnDrag.attr('data-order', tempOrder)
                                                    .find('.synchro_element_position_container .synchro_element_position').attr('value', tempPosition);


                                prevSynchroElement = nextSynchroElement;
                                nextSynchroElement = nextSynchroElement.nextAll('.synchor_element:not(.ondrag)');
                            }
                        }
                        else if(direction === 'left' && prevSynchroElementXPos !== '')
                        {
                            if(myXPos <= prevSynchroElementXPos)
                            {
                                prevSynchroElement.css({ left: parseInt(prevSynchroElement.css('left') - prevSynchroElement.outerWidth() + 'px') });
                                prevSynchroElement.attr('data-order', currentElementOnDrag.attr('data-order'));

                                prevSynchroElement.attr('data-order', currentElementOnDrag.attr('data-order'))
                                    .find('.synchro_element_position_container .synchro_element_position').attr('value', currentElementOnDrag.find('.synchro_element_position_container .synchro_element_position').val());

                                currentElementOnDrag.attr('data-order', tempOrder)
                                    .find('.synchro_element_position_container .synchro_element_position').attr('value', tempPosition);

                                nextSynchroElement = prevSynchroElement;
                                prevSynchroElement = prevSynchroElement.nextAll('.synchor_element:not(.ondrag)');
                            }
                        }

                        //debugger

                        prevSynchroElementXPos = prevSynchroElement.length > 0 ? prevSynchroElement.position().left : '';
                        nextSynchroElementXPos = nextSynchroElement.length > 0 ? nextSynchroElement.position().left : '';

                    })

                    $(window).on('mouseup', e => {
                        if(e.which===1) {
                            $('.synchro_container .synchro_element').removeClass('ondrag');
                            $(window).off('mouseup mousemove');
                            //Reorder and reposition all

                            let sorted = $('.synchro_container .synchro_element').sort( (a, b) => {

                                let prevOrder = parseInt( $(a).attr('data-order'));
                                let nextOrder = parseInt( $(b).attr('data-order'));
                                return (prevOrder < nextOrder) ? -1 : (prevOrder > nextOrder) ? 1 : 0;

                            })

                            this.__$synchroContainer.find('.synchro_elements_container ').html(sorted);

                            $('.synchro_container .synchro_element').css('left','0')
                                                                    .removeAttr('data-order'); //reset
                        }
                    })

                }

            })

        }
        else
        {

            this.__$synchroContainer.off('mousedown.onMouseDownOnSynchroElement', '.synchro_element')

        }

        return this;
    }

    synchroEditFormIsValid()
    {

        let form = this.__$location.find('#synchro_edit_form');

        form.find(`.error`).text('').addClass('hidden');
        form.find(`.form_input`).removeClass('invalid');

        if(form.find('.form_input:empty').length > 0)
        {
            form.find('.form_input:empty').each( (index, element) => {

                $(element).parent().find('.error').text(this.__errors.empty_field).removeClass( 'hidden' );
                $(element).addClass('invalid');

            } )


            return false;
        }

        return true;

    }

    onTypingInInputCheckDuplicateValue(active)
    {
        if(active)
        {
            this.__$synchroContainer.on('input.onTypingInInputCheckDuplicateValue', '.form_input', e => {

                this.__$synchroContainer.find(`.error`).text('').addClass('hidden');
                this.__$synchroContainer.find(`.form_input`).removeClass('invalid');

                let input = $(e.currentTarget);
                let inputValue = input.val();
                input.attr('value', inputValue);

                let duplicateFields = this.__$synchroContainer.find(`.form_input[value='${ inputValue }']`);

                if(duplicateFields.length > 1)
                {
                    duplicateFields.each( (index, element) => {

                        if($(element).hasClass('synchro_element_position'))
                            $(element).parent().find('.error').text(this.__errors.duplicate_position).removeClass( 'hidden' );

                        else
                            $(element).parent().find('.error').text(this.__errors.duplicate_name).removeClass( 'hidden' );

                        $(element).addClass('invalid');

                    } )
                }

                if(this.__$synchroContainer.find(`.form_input.invalid`).length === 0)
                    this.__$location.find('.save_synchro_edits_button').removeAttr('disabled');

                else
                    this.__$location.find('.save_synchro_edits_button').attr('disabled', true);

            })
        }
        else
        {
            this.__$synchroContainer.off('input.onTypingInInputCheckDuplicateValue', '.form_input');
        }

        return this;
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

                            console.log(response); debugger

                            let error = response.responseJSON;

                            let subject = error.subject;

                            switch (error.text)
                            {

                                case "520 Position already used":
                                    this.__$synchroContainer.find(`#synchro_element_${ subject } .synchro_element_position_container .error`).text(this.__errors.duplicate_position).removeClass( 'hidden' );
                                    this.__$synchroContainer.find(`#synchro_element_${ subject } .synchro_element_position`).addClass('invalid');
                                    break;

                                case "521 Duplicate Synchro Name":
                                    this.__$synchroContainer.find(`.synchro_name_container .error`).text(this.__errors.duplicate_name).removeClass( 'hidden' );
                                    this.__$synchroContainer.find(`.synchro_name`).addClass('invalid');
                                    break;

                                case "522 Duplicate Synchro Element Name":
                                    this.__$synchroContainer.find(`#synchro_element_${ subject } .synchro_element_name_container .error`).text(this.__errors.duplicate_name).removeClass( 'hidden' );
                                    this.__$synchroContainer.find(`#synchro_element_${ subject } .synchro_element_name`).addClass('invalid');
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
            .onTypingInInputCheckDuplicateValue(true)
            .onClickOnDraggableButton(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnSynchroActionButton(false)
            .onTypingInInputCheckDuplicateValue(false)
            .onClickOnDraggableButton(false)
        ;
    }

}

export default UploadVideoSynchroSubTool;