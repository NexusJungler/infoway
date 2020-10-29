import ParentTool from "../../../ParentTool";
import SubTool from "../../../SubTool";
import Synchro from "../../../../objects/Media/Video/Synchro/Synchro";
import SynchroElement from "../../../../objects/Media/Video/Synchro/SynchroElement";
import UploadHandlerSubTool from "../UploadHandlerSubTool";

class UploadVideoSynchroSubTool extends UploadHandlerSubTool {

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

    initMediaCardPrototype()
    {

        this.__mediaCardPrototype = `
            <div class="synchro_container" id="__ID__" data-created_date="__SYNCHRO_CREATED_AT__" data-media_diff_start="__SYNCHRO_DIFF_START__"
                 data-media_diff_end="__SYNCHRO_DIFF_END__" data-customer="__SYNCHRO_CUSTOMER__" data-products="__SYNCHRO_PRODUCTS_ID__" 
                 data-criterions="__SYNCHRO_CRITERIONS_ID__" data-categories="__SYNCHRO_CATEGORIES_ID__" data-tags="__SYNCHRO_TAGS_ID__" >
            
                <div class="container top">
            
                    <div class='left'>
                    
                        <div class='select_synchro_input_container'>
                            <input type='checkbox' class='select_synchro_input'>
                        </div>
                        
                        <div class='synchro_name_container'>
                            <p class='synchro_name' title="__SYNCHRO_NAME__"> __SYNCHRO_NAME__ </p>
                        </div>
                        
                        <div class='synchro_action_button_container'>
                            <button type='button' class='synchro_action_button'><i class='fas fa-play synchro_action_button_icon' aria-hidden='true'></i></button>
                            <button type='button' class='synchro_action_button'><i class='fas fa-pause synchro_action_button_icon' aria-hidden='true'></i></button>
                            <button type='button' class='synchro_action_button restart_video_btn'><i class='fas fa-step-backward synchro_action_button_icon' aria-hidden='true'></i></button>
                            <button type='button' class='synchro_action_button drag_video_btn'><i class='fas fa-arrows-alt' aria-hidden='true'></i></button>
                        </div>
                        
                    </div>
            
                    <div class='right'>
                    
                        <div class='media_actions_shortcuts_container'>
            
                            <div class='shortcut shortcut_diff_date_modification '>
                                <i class='far fa-clock' aria-hidden='true'></i>
                            </div>
            
                            <div class='shortcut'>
                                <i class='fas fa-euro-sign' aria-hidden='true'></i>
                            </div>
            
                            <div class='shortcut'>
                                <i class='fas fa-link shortcut_product_association' aria-hidden='true'></i>
                            </div>
            
                            <div class='shortcut'>
                                <i class='fas fa-spinner' aria-hidden='true'></i>
                            </div>
            
                        </div>
                        
                    </div>
            
                </div>
            
                <div class="container middle">
            
                    <div class="synchro_elemens_container">
                        
                    </div>
                    
                </div>
            
                <div class="container bottom">
            
                    <div class="synchro_criterions_container">

                    </div>
            
                    <div class="synchro_tags_container">

                    </div>
            
                </div>
            
            </div>`;

        return this;

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

            // par défaut la position est à 1
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
                        .setOrientation(element.orientation)
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

    showMediaEditingResume()
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

        let associatedTags = "", associatedProducts = "", associatedCriterions = "";

        this.__$location.find('.step_2 tr').each( (index, element) => {

            $(element).find(".associated_tags_container input[type='checkbox']:checked").each( (index, input) => {

                let tagName = $(`label[for='${ $(input).attr('id') }']`).text();
                if(tagName !== "" && (typeof tagName !== "undefined") && associatedTags.indexOf(tagName) === -1 )
                {
                    associatedTags += `<p class="tag container-tags">
                                            <span class="mini-cercle" style="${ $(input).data('style') }"></span>
                                            <span class="current-tags-name">${ tagName }</span>
                                       </p>`;
                }

            } )

            $(element).find(".associated_products_container input[type='checkbox']:checked").each( (index, input) => {

                let productName = $(`label[for='${ $(input).attr('id') }']`).text();
                if(productName !== "" && (typeof productName !== "undefined") && associatedProducts.indexOf(productName) === -1)
                {
                    associatedProducts += `<span> ${ productName } </span>`;
                    associatedCriterions += $(input).data('criterions');
                }

            } )

        } )

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
                                <td class="synchro_criterions_container"> ${ (associatedCriterions) ? associatedCriterions : "Aucun critère(s)" } </td>
                                <td class="synchro_tags_container"> ${ (associatedTags) ? associatedTags : "Aucun tag(s)" } </td>
                                <td class="synchro_products_container"> ${ (associatedProducts) ? associatedProducts : "Aucun produit(s)" } </td>
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

    showMediaInfoForEdit(videoSynchroInfos)
    {

        let now = new Date();
        let month = (now.getMonth() + 1);
        month = (month < 10) ? '0' + month : month;
        let day = (now.getDate() < 10 ) ? '0' + now.getDate() : now.getDate();
        let year = now.getFullYear();

        videoSynchroInfos.forEach( (element) => {

            let html = `<td> <p title="${ element.fileName }"><i class="fas fa-trash-alt cancel-upload" aria-hidden="true"></i> ${ element.fileName }</p> </td>
                <td>
                    <progress class="progress_bar" id="progress_${ element.index }" max="100" value="100"></progress>
                    <i class="fas fa-check" aria-hidden="true"></i>
                </td>
                <td> 
                    ${ super.getMediaPreview(element) } 
                    <i class="fas fa-expand-alt show_expanded_miniature" data-media_id="${ element.id }" aria-hidden="true"></i>
                </td>
                <td>
                    <span>${element.extension}</span> <br> <span>${element.width} * ${element.height} px</span> <br> <span>${ (element.fileType === 'image') ? element.dpi + ' dpi' :  element.codec}</span>
                </td>
                <td class="media_name_container"> 
                    <input type="hidden" class="media_id" name="${ $('.step_2 form').attr('name') }[medias][${element.index}][id]" value="${ element.id }"> 
                    <span class="error hidden"></span> <br>
                    <input type="text" name="${ $('.step_2 form').attr('name') }[medias][${element.index}][name]" class="form_input media_name" title="${ element.fileNameWithoutExtension }" placeholder="Nom du media" value="${element.fileNameWithoutExtension}" required> </td>
                <td class="media_diff_date_container"> 
                    <div class="diff_start_container">
                        <span class="error hidden"></span> <br> 
                        <label for="media_${element.index}_diff_start">Du</label>
                        <input type="date" name="${ $('.step_2 form').attr('name') }[medias][${element.index}][diffusionStart]" id="media_${element.index}_diff_start" class="diffusion_dates start form_input" value="${year}-${month}-${day}">
                   </div>

                   <div class="diff_end_container">
                        <span class="error hidden"></span> <br> 
                        <label for="media_${element.index}_diff_end">Au</label>
                        <input type="date" name="${ $('.step_2 form').attr('name') }[medias][${element.index}][diffusionEnd]" id="media_${element.index}_diff_end" class="diffusion_dates end form_input" min="${year}-${month}-${day}" value="${year + 10}-${month}-${day}">
                   </div>
                </td>
                <td class="associated_criterions_container">
                
                </td>
                <td class="tags_affectation_container"> 
                    <button type="button" class="btn tag_association_btn association_btn">Associer tags</button>
                    <div class="associated_tags_container">
                        ${ this.__parent.buildAssociationInputsHtml('tags', element.index) }
                    </div> 
                </td>
                <td class="products_affectation_container"> 
                    <button type="button" class="btn product_association_btn association_btn">Associer produits</button>
                    <div class="associated_products_container">
                        ${ this.__parent.buildAssociationInputsHtml('products', element.index) }
                    </div> 
                </td>`;

            this.__$location.find(`#upload_${element.index}`).html(html)

        } )

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
                                $('#synchro_edit_form .synchro_id').val(response.synchroInfos.id);

                                this.buildMediaCard(response);

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

    buildMediaCard(newSynchroInfos)
    {

        const synchroInfos = newSynchroInfos.synchroInfos;
        const synchroElementsPreviews = newSynchroInfos.synchroElementsPreviews;
        let synchroCard = this.__mediaCardPrototype;

        synchroCard = synchroCard.replace(/__ID__/gi, "card_" + synchroInfos.id);
        synchroCard = synchroCard.replace(/__SYNCHRO_CREATED_AT__/gi, synchroInfos.createdAt);
        synchroCard = synchroCard.replace(/__SYNCHRO_DIFF_START__/gi, synchroInfos.diffStart);
        synchroCard = synchroCard.replace(/__SYNCHRO_DIFF_END__/gi, synchroInfos.diffEnd);
        synchroCard = synchroCard.replace(/__SYNCHRO_CUSTOMER__/gi, synchroInfos.customer);
        synchroCard = synchroCard.replace(/__SYNCHRO_PRODUCTS_ID__/gi, (synchroInfos.products.length > 0) ? synchroInfos.products.join(', ') : "none");
        synchroCard = synchroCard.replace(/__SYNCHRO_CRITERIONS_ID__/gi, (synchroInfos.criterions.length > 0) ? synchroInfos.criterions.join(', ') : "none");
        synchroCard = synchroCard.replace(/__SYNCHRO_CATEGORIES_ID__/gi, (synchroInfos.categories.length > 0) ? synchroInfos.categories.join(', ') : "none");
        synchroCard = synchroCard.replace(/__SYNCHRO_TAGS_ID__/gi, (synchroInfos.tags.length > 0) ? synchroInfos.tags.join(', ') : "none");
        synchroCard = synchroCard.replace(/__SYNCHRO_NAME__/gi, synchroInfos.name);

        synchroCard = $(synchroCard);

        synchroElementsPreviews.forEach( (synchroElementPreview) => {

            $(synchroElementPreview).appendTo( synchroCard.find('.synchro_elemens_container') );

        } )

        if( $(".medias_list_container").find(`#card_${ synchroInfos.id }`).length === 0 )
            synchroCard.appendTo( $(".medias_list_container") );

        else
            $(".medias_list_container").find(`#card_${ synchroInfos.id }`).replaceWith( synchroCard );

    }

    notifyServerToDeleteSynchroElements()
    {

        let ids = this.__synchro.getSynchroElements().map( (synchroElement) => synchroElement.getId() );

        ids = ids.filter( (id) => id !== null )

        //console.log(ids); debugger

        if(ids.length > 0)
        {

            super.changeLoadingPopupText( "Suppression des medias..." );
            super.showLoadingPopup();

            $.ajax({
                url: `/remove/multiple/medias`,
                type: "POST",
                data: { mediasToDelete:  ids },
                success: (response) => {

                    if(response.error.length > 0)
                    {
                        console.error(`Error during deleting media with ids : ${ response.error.join(', ') }`);
                        debugger;
                    }

                },
                error: (response, status, error) => {
                    debugger
                },
                complete: () => {

                    super.hideLoadingPopup();

                }
            });

        }

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