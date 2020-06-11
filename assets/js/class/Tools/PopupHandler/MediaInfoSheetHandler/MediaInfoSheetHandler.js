import SubTool from "../../SubTool";

class MediaInfoSheetHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_media_info_sheet_container');
        this.__$location = $('.popup_media_info_sheet');
        this.__mediasInfos = [];
        this.__toolIsActive = false;
    }

    onClickOnMediaMiniatureShowMediaInfoSheet(active)
    {
        if(active)
        {
            this.__parent.getMediasContainer().find('.media_miniature').on('click.onClickOnMediaMiniatureShowMediaInfoSheet', async(e) => {

                if(!this.__toolIsActive)
                {

                    this.__toolIsActive = true;

                    const mediaId = $(e.currentTarget).parents('.card').attr('id').replace('media_', '');
                    const customer = $('.medias_list_container').data('customer');

                    let mediaInfosExist = false;

                    if(!this.mediaInfosIsAlreadyRegistered(mediaId))
                        mediaInfosExist = await this.retrieveMediaAssociatedInfos(mediaId);

                    else
                        mediaInfosExist = true;


                    const isImage = $(e.currentTarget).hasClass('miniature_image');

                    let miniature = null;

                    let path = `/miniatures/${customer}/${ (isImage === true) ? 'images' : 'videos'  }/medium/${mediaId}.${ (isImage === true) ? 'png' : 'mp4' }`;

                    if(this.getMediaRegisteredInfos(mediaId).miniatureExist === null)
                    {

                        if(await this.mediaFileExist(path))
                        {
                            miniature = (isImage) ? `<img class="media_miniature" src="${path}">` : `<video class="media_miniature" controls> <source src="${path}" type="video/mp4"> </video>`;
                            this.getMediaRegisteredInfos(mediaId).miniatureExist = true;
                        }
                        else
                        {
                            miniature = `<img class="media_miniature not_found" src="/build/images/no-available-image.png">`;
                            this.getMediaRegisteredInfos(mediaId).miniatureExist = false;
                        }

                    }

                    else if(this.getMediaRegisteredInfos(mediaId).miniatureExist === false)
                        miniature = `<img class="media_miniature not_found" src="/build/images/no-available-image.png">`;

                    else
                        miniature = (isImage) ? `<img class="media_miniature" src="${path}">` : `<video class="media_miniature" controls> <source src="${path}" type="video/mp4"> </video>`;



                    if( isImage )
                        this.__$location.find('.media_type').text('image');

                    else
                        this.__$location.find('.media_type').text('video');

                    this.__$location.find('.media_miniature_container').html( miniature );
                    this.__$location.find('.media_title').text( $(e.currentTarget).parents('.card_body').find('.media_name').text() );
                    this.__$location.find('.media_validity_container .media_diff_start').text( $(e.currentTarget).parents('.card').data('media_diff_start') );
                    this.__$location.find('.media_validity_container .media_diff_end').text( $(e.currentTarget).parents('.card').data('media_diff_end') );

                    if(this.getDaysDiffBetweenDates($(e.currentTarget).parents('.card').data('media_diff_end'), new Date()) <= 14)
                        this.__$location.find('.media_validity_container .media_diff_end').addClass('date_coming_soon');

                    this.__$location.find('.media_infos_bottom .media_name_container .media_name').text( $(e.currentTarget).parents('.card_body').find('.media_name').text() );

                    this.showMediaCharacteristics(mediaId, isImage);

                    if(mediaInfosExist)
                    {
                        const mediaInfos = this.getMediaRegisteredInfos(mediaId).infos;

                        this.showMediaIncrustes(mediaInfos.incrustations);
                        this.showMediaCriterions(mediaInfos.criterions);
                        this.showMediaTags(mediaInfos.tags);
                        this.showMediaAllergens(mediaInfos.allergens);
                        this.showMediaAssociatedProducts(mediaInfos.products);
                    }

                    $('.popup_loading_container').removeClass('is_open');

                    this.__$container.addClass('is_open');

                }

            })
        }
        else
        {
            this.__parent.getMediasContainer().find('.media_miniature').off('click.onClickOnMediaMiniatureShowMediaInfoSheet');
        }

        return this;
    }

    onClickOnPopupCloseButton(active)
    {
        if(active)
        {
            this.__$location.find('.close_modal_button').on('click.onClickOnPopupCloseButton', e => {

                this.__toolIsActive = false;

                this.__$container.removeClass('is_open');
                this.__$location.find('.media_criterions_container').empty();
                this.__$location.find('.media_tags_container').empty();
                this.__$location.find('.media_allergens_container').empty();
                this.__$location.find('.media_diffusion_spaces_container .container_body').empty();
                this.__$location.find('.media_products_list').empty();
                this.__$location.find('.media_incrustations_list').empty();

            })
        }
        else
        {
            this.__$location.find('.close_modal_button').off('click.onClickOnPopupCloseButton');
        }

        return this;
    }

    getDaysDiffBetweenDates(date1, date2)
    {
        date1 = date1.replace(/\//g, '-').split("-").reverse().join("-");
        date1 = ( date1 instanceof Date) ? date1 : new Date(date1);
        date2 = ( date2 instanceof Date) ? date2 : new Date(date2);
        const diffTime = Math.abs(date1 - date2);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }

    mediaInfosIsAlreadyRegistered(mediaId)
    {
        return this.__mediasInfos.findIndex( mediaInfos => mediaInfos.id === mediaId ) !== -1;
    }

    getMediaRegisteredInfos(mediaId)
    {
        if(this.mediaInfosIsAlreadyRegistered(mediaId))
        {
            let index = this.__mediasInfos.findIndex( mediaInfos => mediaInfos.id === mediaId );
            return this.__mediasInfos[index];
        }

        else
            return [];
    }

    retrieveMediaAssociatedInfos(mediaId)
    {

        $('.popup_loading_container').addClass('is_open');

        return new Promise( (resolve, reject) => {

            $.ajax({
                url: "/retrieve/media/associated/infos",
                type: "POST",
                data: {mediaId: mediaId},
                success: (response) => {

                    console.log(response); //debugger

                    this.__mediasInfos.push({ id: mediaId, infos: response, miniatureExist: null });

                    resolve(true);

                },
                error: (response, status, error) => {

                    resolve(false);

                    console.error(response); //debugger

                },
            })

        } );

    }

    showMediaCharacteristics(mediaChooseId, mediaIsImage)
    {

        let characteristics = '<span>' + $(`#media_${mediaChooseId}`).find('.media_miniature_container').data('size') + ' px</span>, <span>' + $(`#media_${mediaChooseId}`).find('.media_miniature_container').data('extension') + '</span>, <span></span>';

        if(mediaIsImage)
            characteristics += $(`#media_${mediaChooseId}`).find('.media_miniature_container').data('dpi') + ' dpi';

        else
            characteristics += $(`#media_${mediaChooseId}`).find('.media_miniature_container').data('codec');

        characteristics += '</span>';

        this.__$location.find('.media_characteristics_container .media_characteristics').html( characteristics );

    }

    showMediaIncrustes(mediaIncrustations)
    {

        mediaIncrustations.forEach( (mediaIncrustation) => {

            const productName = Object.keys(mediaIncrustation);
            const incrustesTypes = Object.values(mediaIncrustation)[0];

            let newElement= `<tr>
                                 <td>${productName}</td> 
                                 <td><input type="checkbox" ${ incrustesTypes.includes('Prix') ? 'checked' : '' }></td>  
                                 <td><input type="checkbox" ${ incrustesTypes.includes('Rupture') ? 'checked' : '' }></td>  
                                 <td><input type="checkbox" ${ incrustesTypes.includes('Texte') ? 'checked' : '' }></td>  
                             </tr>`;

            $(newElement).appendTo( this.__$location.find('.media_incrustations_list') )

        } )

    }

    showMediaCriterions(mediaCriterionsNames)
    {

        mediaCriterionsNames.forEach( (mediaCriterionName) => {

            const newElement = `<span>${mediaCriterionName}</span>`;

            $(newElement).appendTo( this.__$location.find('.media_criterions_container') );

        } )

    }

    showMediaTags(mediaTagsNames)
    {

        mediaTagsNames.forEach( (mediaTagName) => {

            const newElement = `<span>${mediaTagName}</span>`;

            $(newElement).appendTo( this.__$location.find('.media_tags_container') );

        } )

    }

    showMediaAllergens(mediaAllergensNames)
    {

        mediaAllergensNames.forEach( (mediaAllergenName) => {

            const newElement = `<span>${mediaAllergenName}</span>`;

            $(newElement).appendTo( this.__$location.find('.media_allergens_container') );

        } )

    }

    showMediaAssociatedProducts(mediaProductsNames)
    {

        mediaProductsNames.forEach( (mediaProductName) => {

            const newElement = `<tr><td>${mediaProductName}</td></tr>`;

            $(newElement).appendTo( this.__$location.find('.media_products_associated_container .container_body .media_products_list') );

        } )

    }

    mediaFileExist(url)
    {

        return new Promise( (resolve, reject) => {

            $.ajax({
                url: '/file/miniature/exists',
                type: "POST",
                data: {path: url},
                success: () => {
                    resolve(true);
                },
                error: () => {
                    resolve(false);
                }

            })

        } )

    }

    enable()
    {
        super.enable();
        this.onClickOnMediaMiniatureShowMediaInfoSheet(true)
            .onClickOnPopupCloseButton(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnMediaMiniatureShowMediaInfoSheet(false)
            .onClickOnPopupCloseButton(false)
        ;
    }

}

export default MediaInfoSheetHandler;