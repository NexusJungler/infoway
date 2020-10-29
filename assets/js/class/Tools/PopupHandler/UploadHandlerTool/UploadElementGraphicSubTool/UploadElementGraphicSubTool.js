import ParentTool from "../../../ParentTool";
import SubTool from "../../../SubTool";
import UploadHandlerSubTool from "../UploadHandlerSubTool";

class UploadElementGraphicSubTool extends UploadHandlerSubTool {

    constructor()
    {
        super();
        this.__name = this.constructor.name;
    }

    initMediaCardPrototype()
    {

        this.__mediaCardPrototype = `
        
            <div class="card __CLASS__" id="__ID__"  data-created_date="__CREATED_DATE__" data-file_type="__FILE_TYPE__" data-orientation="__ORIENTATION__"

             data-media_diff_start="__DIFF_START__" data-media_diff_end="__DIFF_END__" data-customer="__CUSTOMER__"
        
             data-products="__PRODUCTS_ID__" data-categories="__CATEGORIES_ID__" data-criterions="__CRITERIONS_ID__" >
        
            <div class="card_header">
                <div class="select_media_input_container">
                    <label class="container-input">
                        <input type="checkbox" class="select_media_input">
                        <span class="container-rdo-tags"></span>
                    </label>
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
        
                    <div class="media_criterions_container associated_item">
        
                    </div>
        
                </div>
        
            </div>
        
        </div>
        
        `;

        //console.log(this.__mediaCardPrototype); debugger

        return this;

    }

    showMediaInfoForEdit(elementGraphicInfos)
    {

        const criterionsContainers = this.__parent.buildAvailableProductsCriterionsContainer();

        return `<td> 
                    <p title="${ elementGraphicInfos.fileName }"><i class="fas fa-trash-alt cancel-upload" aria-hidden="true"></i> ${ elementGraphicInfos.fileName }</p> 
                </td>
                <td>
                    <progress class="progress_bar" id="progress_${ elementGraphicInfos.index }" max="100" value="100"></progress>
                    <i class="fas fa-check" aria-hidden="true"></i>
                </td>
                <td> 
                    ${ super.getMediaPreview(elementGraphicInfos) } 
                    <i class="fas fa-expand-alt show_expanded_miniature" data-media_id="${ elementGraphicInfos.id }" aria-hidden="true"></i>
                </td>
                <td> 
                    <input type="hidden" class="media_id" name="medias_list[medias][${elementGraphicInfos.index}][id]" value="${ elementGraphicInfos.id }">
                    <input type="hidden" name="medias_list[medias][${elementGraphicInfos.index}][id]" value="${ elementGraphicInfos.id }"> 
                    <span class="error hidden"></span> <br>
                    <input type="text" name="medias_list[medias][${elementGraphicInfos.index}][name]" class="form_input media_name" title="${ elementGraphicInfos.fileNameWithoutExtension }" placeholder="Nom du media" value="${ elementGraphicInfos.fileNameWithoutExtension }" required>
                 </td>
                 <td class="associated_criterions_container">
                    ${ criterionsContainers }
                </td>
                <td class="products_affectation_container"> 
                    <button type="button" class="btn product_association_btn association_btn">Associer produits</button>
                    <div class="associated_products_container">
                        ${ this.__parent.buildAssociationInputsHtml('products', elementGraphicInfos.index) }
                    </div> 
                </td>`;
    }

    showMediaEditingResume()
    {

        let html = "";

        this.__$fileToCharacterisationList.find('tr').each( (index, element) => {

            const preview = $(element).find('.preview')[0].outerHTML;

            let associatedProducts = "", associatedCriterions = "";

            $(element).find(".associated_products_container input[type='checkbox']:checked").each( (index, input) => {

                let productName = $(`label[for='${ $(input).attr('id') }']`).text();
                if(productName !== "" && (typeof productName !== "undefined"))
                {
                    associatedProducts += `<span> ${ productName } </span>`;

                    associatedCriterions += $(element).find(`.associated_criterions_container .criterions_container[data-product='${ $(input).val() }']`).html();

                }

            } )

            //console.log(associatedCriterions); debugger

            html += `
                
                    <tr>
                        
                        <td> 
                            <input type="checkbox" class="choice_media"> 
                            ${ preview }
                        </td>
                        
                        <td>
                            <div class="media_name_container"> <p>${ $(element).find('.media_name').val() }</p> </div>
                        </td>
                        
                        <td>
                            <div> ${ (associatedCriterions) ? associatedCriterions : "Aucun critère(s)" } </div>
                        </td>
                
                        <td> ${ (associatedProducts) ? associatedProducts : "Aucun produit(s)" } </td>
                
                `;

/*            const mediaContaintIncrustation = parseInt($(element).find('.form_input.media_contain_incruste:checked').val());

            html += `<td> <div class="redirect_to_module_incruste_btn_container"> <button type="button" class="btn redirect_to_module_incruste_btn" ${ (mediaContaintIncrustation === 0) ? 'disabled' : '' }>Incruster PRIX</button> </div> </td>`;*/

            html += "</tr>";

        } )

        this.__$location.find('.step_3 .media_characterisation_resume_list').html( html );

    }

    buildMediaCard(elementGraphicInfos)
    {

        elementGraphicInfos.cardsInfos.forEach( cardInfos => {

            let mediaCard = this.__mediaCardPrototype;
            mediaCard = mediaCard.replace(/__ID__/gi, "card_" + cardInfos.id);
            mediaCard = mediaCard.replace(/__CLASS__/gi, ("card_" + cardInfos.fileType));
            mediaCard = mediaCard.replace(/__FILE_TYPE__/gi, cardInfos.fileType);
            mediaCard = mediaCard.replace(/__CREATED_DATE__/gi, cardInfos.createdAt);
            mediaCard = mediaCard.replace(/__DIFF_START__/gi, cardInfos.diffStart);
            mediaCard = mediaCard.replace(/__DIFF_END__/gi, cardInfos.diffEnd);
            mediaCard = mediaCard.replace(/__CUSTOMER__/gi, cardInfos.customer);
            mediaCard = mediaCard.replace(/__ORIENTATION__/gi, "media_" + cardInfos.orientation);
            mediaCard = mediaCard.replace(/__PRODUCTS_ID__/gi, (cardInfos.productsIds.length > 0) ? cardInfos.productsIds.join(', ') : "none");
            mediaCard = mediaCard.replace(/__CRITERIONS_ID__/gi, (cardInfos.criterionsIds.length > 0) ? cardInfos.criterionsIds.join(', ') : "none");
            mediaCard = mediaCard.replace(/__CATEGORIES_ID__/gi, (cardInfos.categoriesIds.length > 0) ? cardInfos.categoriesIds.join(', ') : "none");
            //mediaCard = mediaCard.replace(/__TAGS_ID__/gi, (cardInfos.tagsIds.length > 0) ? cardInfos.tagsIds.join(', ') : "none");
            mediaCard = mediaCard.replace(/__NAME__/gi, cardInfos.name);
            mediaCard = mediaCard.replace(/__SIZE__/gi, (cardInfos.width + "*" + cardInfos.height));
            mediaCard = mediaCard.replace(/__EXTENSION__/gi, cardInfos.extension);
            mediaCard = mediaCard.replace(/__LOW_EXIST__/gi, cardInfos.miniatureLowExist);
            mediaCard = mediaCard.replace(/__MEDIUM_EXIST__/gi, cardInfos.miniatureMediumExist);

            mediaCard = $(mediaCard);

            if(super.getDaysDiffBetweenDates(new Date(cardInfos.diffEnd), new Date()) <= 14)
                mediaCard.find('.shortcut_diff_date_modification').addClass('alert_date')

            cardInfos.criterions.forEach( criterionInfos => {

                let criterion = `<p class="criterion"><span></span>${ criterionInfos.name }</p>`;

                $(criterion).appendTo( mediaCard.find('.media_criterions_container ') );

            } );

            if(cardInfos.criterions.length <= 0)
                mediaCard.find('.media_criterions_container  ').html("<p>0 critères </p>");

            /*cardInfos.tags.forEach( tagInfos => {

                let tag = `<p class="tag container-tags">
                                <span class="mini-cercle" style="background: ${ tagInfos.color };"></span>
                                <span class="current-tags-name">${ tagInfos.name }</span>
                           </p>`;

                $(tag).appendTo( mediaCard.find('.media_tags_container  ') );

            } );

            if(cardInfos.tags.length <= 0)
                mediaCard.find('.media_tags_container  ').html("<p>0 tags </p>");*/

            let mediaMiniature= "";

            if(cardInfos.miniatureLowExist)
            {

                if(cardInfos.fileType === 'image')
                {

                    mediaMiniature = `<div class="media_container_img">
                                            <div class="media_container_arrows show_expanded_miniature">
                                                <i class="fas fa-expand-arrows-alt"></i>
                                            </div>
                                            <img class="media_miniature miniature_image" src="/miniatures/${ cardInfos.customer }/image/${ cardInfos.mediaType }/low/${ cardInfos.id }.png"
                                                 alt="/miniatures/${ cardInfos.customer }/image/${ cardInfos.mediaType }/low/${ cardInfos.id }.png">
                                      </div>`;

                }
                else
                {
                    mediaMiniature = `<div class="media_container_video">
                                            <div class="media_container_arrows show_expanded_miniature">
                                                <i class="fas fa-expand-arrows-alt"></i>
                                            </div>
                                            <video class="media_miniature miniature_video">
                                                <source src="/miniatures/${ cardInfos.customer }/video/${ cardInfos.mediaType }/low/${ cardInfos.id }.mp4" type="video/mp4">
                                            </video>
                                      </div>`;
                }

            }
            else
                mediaMiniature= `<img class="media_miniature miniature_${ cardInfos.fileType }" src="/build/images/no-available-image.png" alt="/build/images/no-available-image.png">`;

            $(mediaMiniature).appendTo( mediaCard.find('.media_miniature_container') );

            //console.log(mediaCard); debugger;

            if( $(".medias_list_container").find(`#card_${ cardInfos.id }`).length === 0 )
                mediaCard.appendTo( $(".medias_list_container") );

            else
                $(".medias_list_container").find(`#card_${ cardInfos.id }`).replaceWith( mediaCard )

            //console.log($(".medias_list_container").find(`#media_${ cardInfos.id }`)); debugger;


        } )

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

export default UploadElementGraphicSubTool;