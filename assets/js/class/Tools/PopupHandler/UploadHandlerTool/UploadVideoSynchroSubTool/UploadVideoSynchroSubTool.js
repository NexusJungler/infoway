import ParentTool from "../../../ParentTool";
import SubTool from "../../../SubTool";
import Synchro from "../../../../objects/Media/Video/Synchro/Synchro";
import SynchroElement from "../../../../objects/Media/Video/Synchro/SynchroElement";

class UploadVideoSynchroSubTool extends SubTool {

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__synchro = new Synchro();
        this.__synchroIsSend = false;
        this.__$location = $('.popup_upload');
        this.__synchroHtml = "";
        this.__encodedMediaInfos = [];
        this.__$synchroContainer = null;
        this.__errors = {
            empty_field: "Ce champ est obligatoire",
            duplicate_name: "Ce nom est déjà utilisé",
            duplicate_position : "Cette position est déjà utilisée",
            exceed_position: "Cette position dépasse le nombre d'écran disponible",
            invalid_element_id: "Une erreur est survenue, veuillez réessayer",
        }

        this.__existedSynchroNames = $('.step_3').data('existed_synchro');
        $('.step_3').removeAttr('data-existed_synchro');

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

        console.log(this.__synchro); debugger

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
     *
     * @param {object} element
     */
    getSynchroElementIndex(element)
    {
        return this.__synchro.getSynchroElements().findIndex( synchroElement => synchroElement.getId() === element.id );
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

    showSynchros()
    {

        let html =
            `    
            <div class="top">
            
                <form name="synchro_edit_form" id="synchro_edit_form" action="/save/synchro/infos" method="post">
            
                    <div class="synchro_name_container container">
    
                        <div class="label_container"><label for="synchro_name">Nom</label></div>
                        <div class="input_container">
                            <span class="error hidden"></span>
                            <input type="hidden" name="synchro_edit_form[synchro][synchro_id]" class="synchro_id" value="">
                            <input type="text" id="synchro_name" name="synchro_edit_form[synchro][name]" placeholder="Nom de la synchro" class="synchro_name form_input" value="${ this.__synchro.getName() }">
                        </div>
                        <div class="synchro_action_button_container">
                            <button type="button" class="synchro_action_button"><i class="fas fa-play synchro_action_button_icon"></i></button>
                            <button type="button" class="synchro_action_button"><i class="fas fa-pause synchro_action_button_icon"></i></button>
                            <button type="button" class="synchro_action_button restart_video_btn"><i class="fas fa-step-backward synchro_action_button_icon"></i></button>
                            <button type="button" class="synchro_action_button drag_video_btn"><i class="fas fa-arrows-alt"></i></button>
                        </div>
            
                    </div>
           
                    <div class="synchro_elements_container container">
        
        `;

        this.__synchro.getSynchroElements().forEach( (mediaInfos, index) => {

            html += `
            
                <div class="synchro_element" id="synchro_element_${ index }" data-order="${ index+1 }">

                    <div class="synchro_element_preview_container">
                        <video>
                            <source src="/miniatures/kfc/video/sync/low/${ mediaInfos.getId() }.mp4" type="video/mp4" />
                        </video>
                    </div>
    
                    <div class="synchro_element_name_container">
                        <span class="error hidden"></span>
                        <input type="hidden" class="form_input synchro_element_id" name="synchro_edit_form[synchro][synchros_elements][${index}][synchro_element_id]" value="${ mediaInfos.getId() }">
                        <input type="text" class="synchro_element_name form_input" title="nom du média" name="synchro_edit_form[synchro][synchros_elements][${index}][name]" value="${ mediaInfos.getName() }">
                    </div>
    
                    <div class="synchro_element_position_container">
                        <span class="error hidden"></span>
                        <input type="text" class="synchro_element_position form_input" title="position" name="synchro_edit_form[synchro][synchros_elements][${index}][position]" value="${ index +1 }">
                    </div>
    
                </div>
            
            `;

        } );

        html += "</div></form></div>";

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

        this.__$location.find('.step_3').html(html);

        this.__$synchroContainer = $('.synchro_container');

        if($('#synchro_edit_form .form_input:empty').length > 0)
            $('.save_synchro_edits_button').attr('disabled', true);

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
            this.__$location.on('click.onClickOnDraggableButton', '.drag_video_btn', e => {

                if($(e.currentTarget).hasClass('active'))
                {
                    //this.__$synchroContainer.find('.synchro_element').removeClass('draggable');
                    $(e.currentTarget).removeClass('active');
                    $('.step_3 .form_input').removeAttr('readonly');
                    $('.synchro_elements_container .synchro_element').removeClass('draggable');
                    this.onMouseDownOnSynchroElement(false);
                }
                else
                {
                    //this.__$synchroContainer.find('.synchro_element').addClass('draggable');
                    $(e.currentTarget).addClass('active');
                    $('.step_3 .form_input').attr('readonly', true);
                    $('.synchro_elements_container .synchro_element').addClass('draggable');
                    this.onMouseDownOnSynchroElement(true);
                }

            })
        }
        else
        {
            this.__$location.off('click.onClickOnDraggableButton', '.drag_video_btn');
        }

        return this;
    }

    onMouseDownOnSynchroElement(active)
    {

        if(active)
        {

            $('.synchro_elements_container').on('mousedown.onMouseDownOnSynchroElement', '.synchro_element', e => {

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

                    $('.synchro_elements_container .synchro_element').each( (index, element) => {
                        $(element).attr('data-order', (index + 1));
                    });

                    let prevSynchroElement = currentElementOnDrag.prev('.synchro_element');
                    let nextSynchroElement = currentElementOnDrag.next('.synchro_element');
                    //console.log(nextSynchroElement);// debugger
                    let prevSynchroElementXPos = prevSynchroElement.length > 0 ? prevSynchroElement.position().left : '';
                    let nextSynchroElementXPos = nextSynchroElement.length > 0 ? nextSynchroElement.position().left : '';

                    $(window).on('mousemove', e => {

                        currentElementOnDrag.addClass('ondrag');
                        let direction = (myXPos > currentElementOnDrag.position().left) ? 'left' : 'right';
                        let newLeft = left + (e.clientX - dragStartXPos);
                        myXPos = currentElementOnDrag.position().left;
                        currentElementOnDrag.css({
                            left: newLeft + 'px'
                        });

                        if (newLeft < dragMinXPos) {
                            currentElementOnDrag.css({
                                left: dragMinXPos + 'px'
                            });
                        }

                        if (newLeft > dragMaxXPos) {
                            currentElementOnDrag.css({
                                left: dragMaxXPos + 'px'
                            });
                        }

                        let tempOrder = (direction === 'right') ? nextSynchroElement.attr('data-order') : prevSynchroElement.attr('data-order');
                        let tempPosition = (direction === 'right') ? nextSynchroElement.find('.synchro_element_position_container .synchro_element_position').val() : prevSynchroElement.find('.synchro_element_position_container .synchro_element_position').val();

                        if(direction === 'right' && nextSynchroElementXPos !== '')
                        {
                            if(myXPos >= nextSynchroElementXPos)
                            {

                                nextSynchroElement.css({ left: ( parseInt(nextSynchroElement.css('left')) - nextSynchroElement.outerWidth(true) ) + 'px' });

                                nextSynchroElement.attr('data-order', currentElementOnDrag.attr('data-order'))
                                    .find('.synchro_element_position_container .synchro_element_position').val(currentElementOnDrag.find('.synchro_element_position_container .synchro_element_position').val());

                                currentElementOnDrag.attr('data-order', tempOrder)
                                    .find('.synchro_element_position_container .synchro_element_position').val(tempPosition);


                                prevSynchroElement = nextSynchroElement;
                                nextSynchroElement = nextSynchroElement.nextAll('.synchro_element:not(.ondrag)').first();
                            }
                        }

                        else if(direction === 'left' && prevSynchroElementXPos !== '')
                        {
                            if(myXPos <= prevSynchroElementXPos)
                            {

                                prevSynchroElement.css({ left:  ( parseInt(prevSynchroElement.css('left')) + prevSynchroElement.outerWidth(true) ) + 'px' });

                                prevSynchroElement.attr('data-order', currentElementOnDrag.attr('data-order'));

                                prevSynchroElement.attr('data-order', currentElementOnDrag.attr('data-order'))
                                    .find('.synchro_element_position_container .synchro_element_position').val(currentElementOnDrag.find('.synchro_element_position_container .synchro_element_position').val());

                                currentElementOnDrag.attr('data-order', tempOrder)
                                    .find('.synchro_element_position_container .synchro_element_position').val(tempPosition);

                                nextSynchroElement = prevSynchroElement;
                                prevSynchroElement = prevSynchroElement.prevAll('.synchro_element:not(.ondrag)').first();
                            }
                        }

                        prevSynchroElementXPos = prevSynchroElement.length > 0 ? prevSynchroElement.position().left : '';
                        nextSynchroElementXPos = nextSynchroElement.length > 0 ? nextSynchroElement.position().left : '';

                    })

                    $(window).on('mouseup', e => {
                        if(e.which===1) {
                            $('.synchro_elements_container .synchro_element').removeClass('ondrag');
                            $(window).off('mouseup mousemove');

                            this.sortSynchroElementsByAttribute('data-order');

                            $('.synchro_elements_container .synchro_element').removeAttr('style')
                                .removeAttr('data-order');

                        }
                    })

                }

            })

        }
        else
        {

            $('.synchro_elements_container').off('mousedown.onMouseDownOnSynchroElement', '.synchro_element');

        }

        return this;
    }

    sortSynchroElementsByAttribute(attribute, fieldSelector)
    {

        let sorted = $('.step_3 .synchro_element').sort( (a, b) => {

            let prevOrder, nextOrder = null;

            if(attribute.indexOf('data-') >= 0)
            {

                if(!(a.hasAttribute(attribute)) || !(b.hasAttribute(attribute)))
                    throw new Error(`all elements to sort don't have '${ attribute }' attribute !`);

                prevOrder = parseInt( $(a).attr(`${attribute}`));
                nextOrder = parseInt( $(b).attr(`${attribute}`));


            }
            else
            {
                prevOrder = parseInt( $(a).find(fieldSelector).attr(`${attribute}`));
                nextOrder = parseInt( $(b).find(fieldSelector).attr(`${attribute}`));
            }

            return (prevOrder < nextOrder) ? -1 : (prevOrder > nextOrder) ? 1 : 0;

        })

        $('.step_3 .synchro_elements_container ').html(sorted);

        return this;
    }

    onSynchroElementPositionChangeUpdateAllElementsPosition(active)
    {
        if(active)
        {
            $('.step_3').on('input.onSynchroElementPositionChangeUpdateAllElementsPosition', '.synchro_elements_container .synchro_element_position', e => {

                let input = $(e.currentTarget);
                let inputValue = input.val();
                input.parents('.synchro_element').attr('data-order', inputValue);

                input.on('blur', e => {

                    if(inputValue > $('.synchro_elements_container .synchro_element').length)
                    {
                        input.parent().find('.error').text(this.__errors.exceed_position).removeClass( 'hidden' );
                        input.addClass('invalid');
                    }

                    if(!input.hasClass('invalid'))
                    {
                        this.sortSynchroElementsByAttribute('data-order');

                        let synchroElementId = parseInt( input.parents('.synchro_element').find('.synchro_element_id').attr('value') );
                        let position = parseInt( input.parents('.synchro_element').find('.synchro_element_position').attr('value') );

                        let synchroElementIndex = this.getSynchroElementIndex( { id: synchroElementId } );

                        if(synchroElementIndex > -1)
                        {
                            let synchroElement = this.__synchro.getSynchroElements()[synchroElementIndex];

                            synchroElement.setPosition(position);

                            this.updateSynchroElements([synchroElement]);
                        }
                    }

                    input.off('blur');

                })

            })
        }
        else
        {
            $('.step_3').off('input.onSynchroElementPositionChangeUpdateAllElementsPosition', '.synchro_elements_container .synchro_element_position');
        }

        return this;
    }

    synchroEditFormIsValid()
    {

        let form = $('.step_3 #synchro_edit_form');

        form.find(`.error`).empty().addClass('hidden');
        form.find(`.form_input`).removeClass('invalid');

        let output = true;

        form.find('.form_input').each( (index, element) => {

            if( $(element).val() === '' )
            {
                output = false;
                $(element).parent().find('.error').text(this.__errors.empty_field).removeClass( 'hidden' );
                $(element).addClass('invalid');
            }

        } )

        return output;

    }

    onTypingInInputCheckDuplicateValue(active)
    {
        if(active)
        {
            $('.step_3').on('input.onTypingInInputCheckDuplicateValue', '.form_input', e => {

                $(`.step_3 .error`).text('').addClass('hidden');
                $(`.step_3 .form_input`).removeClass('invalid');

                let input = $(e.currentTarget);
                let inputValue = input.val();
                input.attr('value', inputValue);

                if(input.val() === '')
                {
                    input.parent().find('.error').text(this.__errors.empty_field).removeClass( 'hidden' );

                    input.addClass('invalid');
                }
                else
                {
                    let duplicateFields = $(`.step_3 .form_input[value='${ inputValue }']`);

                    if(duplicateFields.length > 1)
                    {
                        duplicateFields.each( (index, element) => {

                            if(input.hasClass('synchro_element_position'))
                                $(element).parent().find('.error').text(this.__errors.duplicate_position).removeClass( 'hidden' );

                            else
                                $(element).parent().find('.error').text(this.__errors.duplicate_name).removeClass( 'hidden' );

                            $(element).addClass('invalid');

                        } )
                    }
                    else
                    {

                        if(input.hasClass('synchro_name'))
                        {

                            if( typeof this.__existedSynchroNames !== "undefined" )
                            {

                                if(!this.__existedSynchroNames.includes(inputValue + ", "))
                                {
                                    this.__synchro.setName(inputValue);
                                }
                                else
                                {
                                    input.parent().find('.error').text(this.__errors.duplicate_name).removeClass( 'hidden' );

                                    input.addClass('invalid');
                                }

                            }

                        }
                        else
                        {
                            let synchroElementId = parseInt( input.parents('.synchro_element').find('.synchro_element_id').attr('value') );
                            let synchroElementName = input.parents('.synchro_element').find('.synchro_element_name').attr('value');
                            let position = parseInt( input.parents('.synchro_element').find('.synchro_element_position').attr('value') );

                            let synchroElementIndex = this.getSynchroElementIndex( { id: synchroElementId } );

                            if(synchroElementIndex > -1)
                            {
                                let synchroElement = this.__synchro.getSynchroElements()[synchroElementIndex];

                                synchroElement.setName(synchroElementName)
                                    .setPosition(position);

                                this.updateSynchroElements([synchroElement]);
                            }
                        }

                    }
                }

                if($(`.step_3 .form_input.invalid`).length === 0)
                    this.__$location.find('.save_synchro_edits_button').removeAttr('disabled');

                else
                    this.__$location.find('.save_synchro_edits_button').attr('disabled', true);

            })
        }
        else
        {
            $('.step_3').off('input.onTypingInInputCheckDuplicateValue', '.form_input');
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

                    //console.log(JSON.stringify($('.step_3 #synchro_edit_form').serializeArray())); de
                    super.showLoadingPopup();

                    //console.table(this.__synchro.getSynchroElements()); debugger

                    //let elements = this.__synchro.getSynchroElements();

                    $.ajax({
                        url: `/save/synchro/infos`,
                        type: 'POST',
                        data: $('.step_3 #synchro_edit_form').serialize(),
                        //data: JSON.stringify( this.__synchro.formatObjectToExport() ),
                        success: (response) => {

                            if(response.errors.length > 0)
                            {

                                response.errors.forEach( (error) => {

                                    if(error.text === "Invalid synchro id")
                                    {
                                        $(`#synchro_edit_form .form_input.synchro_name`).addClass('invalid');
                                        $(`#synchro_edit_form .synchro_name_container .error`).text(this.__errors.invalid_element_id).removeClass('hidden');
                                    }

                                    else if(error.text === "Duplicate synchro name")
                                    {
                                        $(`#synchro_edit_form .form_input.synchro_name`).addClass('invalid');
                                        $(`#synchro_edit_form .synchro_name_container .error`).text(this.__errors.duplicate_name).removeClass('hidden');
                                    }

                                    else if(error.text === "Invalid synchro element id")
                                    {
                                        $(`#synchro_edit_form #${ error.subject } .form_input`).addClass('invalid');
                                        $(`#synchro_edit_form #${ error.subject } .synchro_element_name_container .error`).text(this.__errors.invalid_element_id).removeClass('hidden');
                                    }

                                    else if(error.text === "Duplicate synchro element name")
                                    {
                                        $(`#synchro_edit_form #${ error.subject } .form_input.synchro_element_name`).addClass('invalid');
                                        $(`#synchro_edit_form #${ error.subject } .synchro_element_name_container .error`).text(this.__errors.duplicate_name).removeClass('hidden');
                                    }

                                    else if(error.text === "Position already used")
                                    {
                                        $(`#synchro_edit_form #${ error.subject } .form_input.synchro_element_position`).addClass('invalid');
                                        $(`#synchro_edit_form #${ error.subject } .synchro_element_position_container .error`).text(this.__errors.duplicate_position).removeClass('hidden');
                                    }

                                    else
                                    {
                                        console.log(error); debugger
                                    }

                                } )

                                this.__$location.find('.save_synchro_edits_button').attr('disabled', true);

                            }
                            else
                            {
                                $('#synchro_edit_form .synchro_id').val(response.synchro_infos.synchro_id);
                                alert("OK !");
                            }

                            //this.__$fileToCharacterisationList.find('.unregistered').removeClass('unregistered');

                        },
                        error: (response) => {

                            alert("Erreur interne !");

                            console.log(response); debugger

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
            .onClickOnSynchroSaveButton(true)
            .onSynchroElementPositionChangeUpdateAllElementsPosition(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnSynchroActionButton(false)
            .onTypingInInputCheckDuplicateValue(false)
            .onClickOnDraggableButton(false)
            .onClickOnSynchroSaveButton(false)
            .onSynchroElementPositionChangeUpdateAllElementsPosition(false)
        ;
    }

}

export default UploadVideoSynchroSubTool;