
import UploadHandlerSubTool from "../UploadHandlerSubTool";

class UploadVideoThematicSubTool extends UploadHandlerSubTool {

    constructor()
    {

        super();
        this.__name = this.constructor.name;
        this.__videoThematicThemePrototype =  $('.step_2 .file_to_characterisation_list').data('video_thematic_themes_prototype');

        $('.step_2 .file_to_characterisation_list').removeAttr('data-video_thematic_themes_prototype');

    }

    initMediaCardPrototype()
    {
        this.__mediaCardPrototype = `
        
        <div class="card __CLASS__" id="__ID__"  data-created_date="__CREATED_DATE__" data-file_type="__FILE_TYPE__" data-orientation="__ORIENTATION__"

             data-media_diff_start="__DIFF_START__" data-media_diff_end="__DIFF_END__" data-customer="__CUSTOMER__" data-tags="__TAGS_ID__" >
        
            <div class="card_header">
                <div class="select_media_input_container">
                    <label class="container-input">
                        <input type="checkbox" class="select_media_input">
                        <span class="container-rdo-tags"></span>
                    </label>
                </div>
        
                <div class="video_theme_container">
                    <span class="video_theme">__VIDEO_THEMATIC__</span>
                </div>
        
                <div class="media_actions_shortcuts_container">
                    <div class="shortcut shortcut_diff_date_modification">
                        <i class="far fa-clock"></i>
                    </div>
        
                    <div class="shortcut">
                        <i class="fas fa-euro-sign"></i>
                    </div>
        
                    <div class="shortcut">
                        <i class="fas fa-link shortcut_product_association"></i>
                    </div>
        
                    <div class="shortcut">
                        <i class="fas fa-spinner"></i>
                    </div>
        
                </div>
            </div>
        
            <div class="card_body">
        
                <div class="media_miniature_container __ORIENTATION__" data-miniature_medium_exist="__MEDIUM_EXIST__" data-size="__SIZE__" data-extension="__EXTENSION__">
        
                </div>
        
                <div class="media_name_container">
                    <p class="media_name" title="__NAME__">__NAME__</p>
                </div>
        
                <div class="media_associated_items_container">
        
                    <div class="media_tags_container associated_item">
        
                    </div>
        
                </div>
        
            </div>
        
        </div>
        
        `;

        return this;

    }
    
    getThemePrototype()
    {
        return this.__videoThematicThemePrototype;
    }

    showMediaInfoForEdit(videoThematicInfos)
    {

        let now = new Date();
        let month = (now.getMonth() + 1);
        month = (month < 10) ? '0' + month : month;
        let day = (now.getDate() < 10 ) ? '0' + now.getDate() : now.getDate();
        let year = now.getFullYear();

        return `<td> 
                    <p title="${ videoThematicInfos.fileName }"><i class="fas fa-trash-alt cancel-upload" aria-hidden="true"></i> ${ videoThematicInfos.fileName }</p> 
                </td>
                <td>
                    <progress class="progress_bar" id="progress_${ videoThematicInfos.index }" max="100" value="100"></progress>
                    <i class="fas fa-check" aria-hidden="true"></i>
                </td>
                <td> 
                    ${ super.getMediaPreview(videoThematicInfos) } 
                    <i class="fas fa-expand-alt show_expanded_miniature" data-media_id="${ videoThematicInfos.id }" aria-hidden="true"></i>
                </td>
                <td>
                    <span>${videoThematicInfos.extension}</span> <br> <span>${videoThematicInfos.width} * ${videoThematicInfos.height} px</span> <br> <span>${ videoThematicInfos.codec }</span>
                </td>
                <td> 
                    <input type="hidden" class="media_id" name="medias_list[medias][${videoThematicInfos.index}][id]" value="${ videoThematicInfos.id }">
                    <span class="error hidden"></span> <br>
                    <input type="text" name="medias_list[medias][${videoThematicInfos.index}][name]" class="form_input media_name" title="${ videoThematicInfos.fileNameWithoutExtension }" placeholder="Nom du media" value="${ videoThematicInfos.fileNameWithoutExtension }" required>
                 </td>
                 <td class="media_diff_date_container"> 
                    <div class="diff_start_container">
                        <span class="error hidden"></span> <br> 
                        <label for="media_${videoThematicInfos.index}_diff_start">Du</label>
                        <input type="date" name="${ $('.step_2 form').attr('name') }[medias][${videoThematicInfos.index}][diffusionStart]" id="media_${videoThematicInfos.index}_diff_start" class="diffusion_dates start form_input" value="${year}-${month}-${day}">
                   </div>

                   <div class="diff_end_container">
                        <span class="error hidden"></span> <br> 
                        <label for="media_${videoThematicInfos.index}_diff_end">Au</label>
                        <input type="date" name="${ $('.step_2 form').attr('name') }[medias][${videoThematicInfos.index}][diffusionEnd]" id="media_${videoThematicInfos.index}_diff_end" class="diffusion_dates end form_input" min="${year}-${month}-${day}" value="${year + 10}-${month}-${day}">
                   </div>
                </td>
                <td class="tags_affectation_container"> 
                    <button type="button" class="btn tag_association_btn association_btn">Associer tags</button>
                    <div class="associated_tags_container">
                        ${ this.__parent.buildAssociationInputsHtml('tags', videoThematicInfos.index) }
                    </div> 
                </td>    
                <td class="thematic_affectation_container"> 
                    <span class="error hidden"></span> <br> 
                    <select class="media_thematic form_input" name="${ $('.step_2 form').attr('name') }[medias][${ videoThematicInfos.index }][thematic]">
                        ${ this.__videoThematicThemePrototype }
                    </select>
                </td>            

            `;

    }

    showMediaEditingResume()
    {

        let html = "";

        this.__$fileToCharacterisationList.find('tr').each( (index, element) => {

            const preview = $(element).find('.preview')[0].outerHTML;

            let associatedTags = "";

            $(element).find(".associated_tags_container input[type='checkbox']:checked").each( (index, input) => {

                let tagName = $(`label[for='${ $(input).attr('id') }']`).text();
                if(tagName !== "" && (typeof tagName !== "undefined"))
                {
                    associatedTags += `<p class="tag container-tags">
                                            <span class="mini-cercle" style="${ $(input).data('style') }"></span>
                                            <span class="current-tags-name">${ tagName }</span>
                                       </p>`;
                }

            } );

            html += `
                
                    <tr>
                        
                        <td> 
                            <input type="checkbox" class="choice_media"> 
                            ${ preview }
                        </td>
                        
                        <td>
                            <div class="media_name_container"> <p>${ $(element).find('.media_name').val() }</p> </div>
                        </td>
                        
                        <td> ${ (associatedTags) ? associatedTags : "Aucun tag(s)" } </td>
                        
                        <td> <img src="/miniatures/themes/${ $(element).find('.thematic_affectation_container select').val() }.png" alt="/miniatures/themes/${ $(element).find('.thematic_affectation_container select').val() }.png"> </td>
                        
                    </tr>
                `;

        } )

        this.__$location.find('.step_3 .media_characterisation_resume_list').html( html );

    }

    onMediaDiffDateChange(active)
    {

        if(active)
        {
            this.__$location.on('change.onMediaDiffDateChange', '.diffusion_dates ', e => {

                $(e.currentTarget).parents('tr').addClass('unregistered');

            })
        }
        else
        {
            this.__$location.off('change.onMediaDiffDateChange', '.diffusion_dates ');
        }

        return this;

    }

    buildMediaCard(videoThematicInfos)
    {

        videoThematicInfos.cardsInfos.forEach( cardInfos => {

            let mediaCard = this.__mediaCardPrototype;

            mediaCard = mediaCard.replace(/__ID__/gi, "card_" + cardInfos.id);
            mediaCard = mediaCard.replace(/__CLASS__/gi, ("card_" + cardInfos.fileType));
            mediaCard = mediaCard.replace(/__VIDEO_THEMATIC__/gi, cardInfos.thematicName);
            mediaCard = mediaCard.replace(/__FILE_TYPE__/gi, cardInfos.fileType);
            mediaCard = mediaCard.replace(/__CREATED_DATE__/gi, cardInfos.createdAt);
            mediaCard = mediaCard.replace(/__DIFF_START__/gi, cardInfos.diffStart);
            mediaCard = mediaCard.replace(/__DIFF_END__/gi, cardInfos.diffEnd);
            mediaCard = mediaCard.replace(/__CUSTOMER__/gi, cardInfos.customer);
            mediaCard = mediaCard.replace(/__ORIENTATION__/gi, "media_" + cardInfos.orientation);
            //mediaCard = mediaCard.replace(/__PRODUCTS_ID__/gi, (cardInfos.productsIds.length > 0) ? cardInfos.productsIds.join(', ') : "none");
            //mediaCard = mediaCard.replace(/__CRITERIONS_ID__/gi, (cardInfos.criterionsIds.length > 0) ? cardInfos.criterionsIds.join(', ') : "none");
            //mediaCard = mediaCard.replace(/__CATEGORIES_ID__/gi, (cardInfos.categoriesIds.length > 0) ? cardInfos.categoriesIds.join(', ') : "none");
            mediaCard = mediaCard.replace(/__TAGS_ID__/gi, (cardInfos.tagsIds.length > 0) ? cardInfos.tagsIds.join(', ') : "none");
            mediaCard = mediaCard.replace(/__NAME__/gi, cardInfos.name);
            mediaCard = mediaCard.replace(/__SIZE__/gi, (cardInfos.width + "*" + cardInfos.height));
            mediaCard = mediaCard.replace(/__EXTENSION__/gi, cardInfos.extension);
            mediaCard = mediaCard.replace(/__LOW_EXIST__/gi, cardInfos.miniatureLowExist);
            mediaCard = mediaCard.replace(/__MEDIUM_EXIST__/gi, cardInfos.miniatureMediumExist);

            mediaCard = $(mediaCard);

            if(super.getDaysDiffBetweenDates(new Date(cardInfos.diffEnd), new Date()) <= 14)
                mediaCard.find('.shortcut_diff_date_modification').addClass('alert_date')

            /*  cardInfos.criterions.forEach( criterionInfos => {

                let criterion = `<p class="criterion"><span></span>${ criterionInfos.name }</p>`;

                $(criterion).appendTo( mediaCard.find('.media_criterions_container ') );

            } );*/

            cardInfos.tags.forEach( tagInfos => {

                let tag = `<p class="tag container-tags">
                                <span class="mini-cercle" style="background: ${ tagInfos.color };"></span>
                                <span class="current-tags-name">${ tagInfos.name }</span>
                           </p>`;

                $(tag).appendTo( mediaCard.find('.media_tags_container  ') );

            } );

            if(cardInfos.tags.length <= 0)
                mediaCard.find('.media_tags_container  ').html("<p>0 tags </p>");

            let mediaMiniature= "";

            if(cardInfos.miniatureLowExist)
            {

                mediaMiniature = `<div class="media_container_video">
                                            <div class="media_container_arrows show_expanded_miniature">
                                                <i class="fas fa-expand-arrows-alt"></i>
                                            </div>
                                            <video class="media_miniature miniature_video">
                                                <source src="/miniatures/${ cardInfos.customer }/video/them/low/${ cardInfos.id }.mp4" type="video/mp4">
                                            </video>
                                      </div>`;

            }
            else
                mediaMiniature= `<img class="media_miniature miniature_${ cardInfos.fileType }" src="/build/images/no-available-image.png" alt="/build/images/no-available-image.png">`;

            $(mediaMiniature).appendTo( mediaCard.find('.media_miniature_container') );

            //console.log(mediaCard); debugger

            if( $(".medias_list_container").find(`#card_${ cardInfos.id }`).length === 0 )
                mediaCard.appendTo( $(".medias_list_container") );

            else
                $(".medias_list_container").find(`#card_${ cardInfos.id }`).replaceWith( mediaCard )

            //console.log($(".medias_list_container").find(`#media_${ cardInfos.id }`)); debugger

        } )

    }

    enable()
    {
        super.enable();
        this.onMediaDiffDateChange(true);
    }

    disable()
    {
        super.disable();
        this.onMediaDiffDateChange(false);
    }

}

export default UploadVideoThematicSubTool;