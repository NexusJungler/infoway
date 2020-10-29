import SubTool from "../../SubTool";

class MediaReplacementPopupHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_media_remplacement_container');
        this.__$location = $('.popup_media_remplacement');
        this.__$mediaSubstitutesContainer = $('.substitutes_container');

        this.__currentMediaInfos = {
            id: null,
            name: null,
            customer: null,
            fileType: null,
            mediaType: null,
            extension: null,
            mediaLowMiniatureExist: null,
            progrommingInfos: null,
        };

        this.__replacementInfos = {
            mediaToReplaceId: null,
            mediaToReplaceMediaType: null,
            mediaToReplaceFileType: null,
            substituteId: null,
            substituteFileType: null,
            substituteMediaType: null,
            remplacementDate: {
                start: null,
                end: null,
            }
        };

        this.__errors= {
            empty_field: "Ce champ ne peut pas être vide",
            remplacement_date_is_past: "Merci de choisir une date récente",
            remplacement_date_end_is_past: "Cette date doit être supérieur à la date de début",
            invalid_date : "Date invalide",
        };

    }

    async initializePopupContent()
    {

        let miniature = '';

        if(this.__currentMediaInfos.mediaLowMiniatureExist === false)
            miniature = `<img class="media_miniature miniature_${ this.__currentMediaInfos.type }" src="/build/images/no-available-image.png" alt="/build/images/no-available-image.png">`;

        else
        {

            if(this.__currentMediaInfos.fileType === 'image')
                miniature = `<img class="media_miniature miniature_img" src="/miniatures/${ this.__currentMediaInfos.customer }/image/${ this.__currentMediaInfos.mediaType }/low/${ this.__replacementInfos.mediaToReplaceId }.png"
                             alt="/miniatures/${ this.__currentMediaInfos.customer }/image/${ this.__currentMediaInfos.mediaType }/low/${ this.__replacementInfos.mediaToReplaceId }.png" />`;

            else
                miniature = `<video class="media_miniature miniature_video" controls>
                                <source src="/miniatures/${ this.__currentMediaInfos.customer }/video/${ this.__currentMediaInfos.mediaType }/low/${ this.__currentMediaInfos.mediaToReplaceId }.mp4" type="video/mp4">          
                             </video>`;

        }

        this.__$location.find('.media_to_replace_miniature_container').html( miniature );

        this.__$location.find('.media_associated_datas_container').empty();

        $('.container_child.media_tags_container .tag:not(.hidden)').each( ( index, tag ) => {

            $(tag).clone(true).appendTo( this.__$location.find('.media_associated_datas_container ') )

        } )

        $('.container_child.media_criterions_container .criterion:not(.hidden)').each( ( index, criterion ) => {

            $(criterion).clone(true).appendTo( this.__$location.find('.media_associated_datas_container') )

        } )

    }

    async getMediaProgrammingInfos(mediaId)
    {

        return new Promise( (resolve, reject) => {

            $.ajax({
                url: `/get/media/${mediaId}/programming/infos`,
                type: "POST",
                data: {},
                success: (response) => {

                    resolve(response);

                },
                error: (response, status, error) => {

                    console.error(response); debugger

                    reject(response);

                },
                complete: () => {

                }
            });

        } )

    }

    mediaLowMiniatureExist(url)
    {

        console.log(url); debugger
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

    onClickOnReplacementButton(active)
    {
        if(active)
        {
            $('.media_replacement_btn').on("click.onClickOnMediaReplacementButton", async(e) => {

                super.showLoadingPopup();

                this.__currentMediaInfos.progrommingInfos = await this.getMediaProgrammingInfos( parseInt($('.media_miniature_container').data('media_id')) );

                this.__replacementInfos.mediaToReplaceId = $('.media_miniature_container').data('media_id');
                this.__replacementInfos.mediaToReplaceFileType = $('.media_miniature').parents('.card').data('file_type');
                this.__replacementInfos.mediaToReplaceMediaType = $('.media_miniature').parents('.card').data('media_type');

                const mediaNameText = $('#edit_media_name').val();

                this.__$location.find('.media_name_container').html( `<p class="media_name">${mediaNameText}</p>` );

                /*let date = new Date(),
                    day = ( (date.getDate() < 10) ? ('0' + date.getDate()) : date.getDate() ),
                    month = ( ((date.getMonth()+1) < 10) ? ('0' + (date.getMonth()+1)) : (date.getMonth()+1) ),
                    year = date.getFullYear();*/

                let remplacementDateMin = $('#edit_media_diffusionStart').val();

                this.__$location.find('.remplacement_date_start').attr('min', remplacementDateMin);
                //this.__$location.find('.remplacement_date_end').attr('min', year + '-' + month + '-' + day);
                this.__$location.find('.remplacement_date_end').attr('min', remplacementDateMin);

                this.__currentMediaInfos.id = this.__replacementInfos.mediaToReplaceId;
                this.__currentMediaInfos.name = mediaNameText
                this.__currentMediaInfos.customer = $('.media_miniature').parents('.card').data('customer');
                this.__currentMediaInfos.fileType = ($('.media_miniature').parents('.card').data('file_type') === 'image') ? 'image' : 'video';
                this.__currentMediaInfos.mediaType = $('.media_miniature').parents('.card').data('media_type');
                this.__currentMediaInfos.extension = ( $('.media_miniature').parents('.card').data('file_type') === 'image' ) ? 'png' : 'mp4';
                this.__currentMediaInfos.mediaLowMiniatureExist = $('.media_miniature').parents('.card').data('miniature_medium_exist');

                //console.log(this.__currentMediaInfos); debugger

                await this.initializePopupContent();

                super.hideLoadingPopup();

                this.__$container.addClass('is_open');
                this.__parent.__popupIsOpen = true;

            })
        }
        else
        {
            $('.media_replacement_btn').off("click.onClickOnMediaReplacementButton");
        }

        return this;
    }

    closePopup()
    {
        this.__$container.removeClass('is_open');
        this.__parent.__popupIsOpen = false;
        this.__$location.find('.media_substitute_name').text('');
        this.__$mediaSubstitutesContainer.find('.card.selected .choice_substitute').prop('checked', false);
        this.__$mediaSubstitutesContainer.find('.card.selected').removeClass('selected');
        this.__$location.find('.validate_remplacement_btn').attr('disabled', true);
        this.__$location.find('.remplacement_infos_setting_date_container input.remplacement_date_choice_input').removeClass('empty');
        this.__$location.find('.media_associated_datas_container').empty();

    }

    onClickOnPopupCloseButton(active)
    {
        if(active)
        {
            this.__$location.find('.close_modal_button').on('click.onClickOnPopupCloseButton', e => {

                this.closePopup();

            })
        }
        else
        {
            this.__$location.find('.close_modal_button').off('click.onClickOnPopupCloseButton');
        }

        return this;
    }

    onClickOnSubstitute(active)
    {
        if(active)
        {
            this.__$mediaSubstitutesContainer.find('.card').on('click.onClickOnSubstitute', e => {

                const cardSelected = $(e.currentTarget);

                if(cardSelected.hasClass('selected'))
                {
                    cardSelected.find('.choice_substitute').prop('checked', false);
                    cardSelected.removeClass('selected');
                    this.__$location.find('.media_substitute_name').text('');
                    this.__replacementInfos.substituteId = null;
                    this.__replacementInfos.substituteFileType = null;
                    this.__replacementInfos.substituteMediaType = null;
                }
                else
                {

                    this.__$mediaSubstitutesContainer.find('.card.selected .choice_substitute').prop('checked', false);
                    this.__$mediaSubstitutesContainer.find('.card.selected').removeClass('selected');
                    cardSelected.addClass('selected');

                    this.__replacementInfos.substituteId = cardSelected.data('media_id');
                    this.__replacementInfos.substituteFileType = cardSelected.data('file_type');
                    this.__replacementInfos.substituteMediaType = cardSelected.data('media_type');

                    cardSelected.find('.choice_substitute').prop('checked', true);

                    this.__$location.find('.media_substitute_name').text( cardSelected.find('.substitute_name').text() );

                }

                this.onSubstituteSelectionActiveButton();

            })
        }
        else
        {
            this.__$mediaSubstitutesContainer.find('.card').off('click.onClickOnSubstitute');
        }

        return this;
    }

    onSubstituteSelectionActiveButton()
    {
        this.__$location.find('.validate_remplacement_btn').attr('disabled', (this.__$mediaSubstitutesContainer.find('.card.selected').length === 0));
    }

    onClickOnRemplacementValidationButton(active)
    {
        if(active)
        {
            this.__$location.find('.validate_remplacement_btn').on('click.onClickOnRemplacementValidationButton', e => {

                this.__$location.find('.error').text('');
                this.__$location.find('.form_input').removeClass('invalid');
                this.__$location.find('.media_substitute_name').removeClass('invalid');
                this.__replacementInfos.remplacementDate.start = this.__$location.find('.remplacement_date_start').val();
                this.__replacementInfos.remplacementDate.end = this.__$location.find('.remplacement_date_end').val();
                let isValid = true;

                if(this.__$location.find('.media_substitute_name').text() === "")
                {
                    isValid = false;
                    this.__$location.find('.media_substitute_name').addClass('invalid');
                    this.__$location.find('.media_substitute_name').parent().prev('.error').text( this.__errors.empty_field );
                }

                if(this.__$location.find('.remplacement_date_start').val() === "")
                {
                    isValid = false;
                    this.__$location.find('.remplacement_date_start').addClass('invalid');
                    this.__$location.find('.remplacement_date_start').parent().prev('.error').text( this.__errors.empty_field );
                }

                if(this.__$location.find('.remplacement_date_end').val() === "")
                {
                    isValid = false;
                    this.__$location.find('.remplacement_date_end').addClass('invalid');
                    this.__$location.find('.remplacement_date_end').parent().prev('.error').text( this.__errors.empty_field );
                }

                if(this.__replacementInfos.substituteId === null || this.__replacementInfos.substituteId === "")
                {
                    isValid = false;
                    console.error("Erreur !");
                }

                /*if((new Date(this.__replacementInfos.remplacementDate.end)) < (new Date()))
                {
                    isValid = false;
                    this.__$location.find('.remplacement_date_end').addClass('invalid');
                    this.__$location.find('.remplacement_date_end').parent().prev('.error').text( this.__errors.remplacement_date_is_past );
                }*/

                if( (new Date(this.__replacementInfos.remplacementDate.end)) < (new Date(this.__replacementInfos.remplacementDate.start)) )
                {
                    isValid = false;
                    this.__$location.find('.remplacement_date_end').addClass('invalid');
                    this.__$location.find('.remplacement_date_end').parent().prev('.error').text( this.__errors.remplacement_date_end_is_past );
                }

                if( (isValid) && (confirm(`Le média '${ this.__currentMediaInfos.name }' sera remplacé puis archivé ! Êtres-vous sûr de vouloir continuer ?`)) )
                    this.sendRemplacementDatas();

            })
        }
        else
        {
            this.__$location.find('.validate_remplacement_btn').off('click.onClickOnRemplacementValidationButton');
        }

        return this;
    }

    sendRemplacementDatas()
    {
        /*console.log(this.__replacementInfos); debugger*/
        super.changeLoadingPopupText("Remplacement en cours...");
        super.showLoadingPopup();

        $.ajax({
            url: `/replace/media`,
            type: "POST",
            data: {remplacementDatas: this.__replacementInfos},
            success: (response) => {
                alert("Success !");
                this.closePopup();
            },
            error: (response) => {
                alert("Error !");
            },
            complete: () => {
                super.hideLoadingPopup();
            }

        })

    }

    onClickOnFileTypeOrMediaTypeFilter(active)
    {

        if(active)
        {
            this.__$location.find('.filter_by_file_type_media_type_container .filter').on('click.onClickOnFileTypeOrMediaTypeFilter', e => {

                let filter = $(e.currentTarget);
                this.__$location.find('.media_substitute_name').text('');
                this.__$mediaSubstitutesContainer.find('.card.selected .choice_substitute').prop('checked', false);

                if(filter.hasClass('filter_by_file_type'))
                    this.filterSubstituteByFileType( filter.data('target') );

                else if(filter.hasClass('filter_by_media_type'))
                    this.filterSubstituteByMediaType( filter.data('target') );

            })
        }
        else
        {
            this.__$location.find('.filter_by_file_type_media_type_container .filter').off('click.onClickOnFileTypeOrMediaTypeFilter');
        }

        return this;
    }

    /**
     *
     * @param {string} fileType
     */
    filterSubstituteByFileType(fileType)
    {
        this.__$mediaSubstitutesContainer.find(`.substitute`).addClass('hidden');
        this.__$mediaSubstitutesContainer.find(`.substitute[data-file_type*='${ fileType }']`).removeClass('hidden');
    }

    /**
     *
     * @param {string} mediaType
     */
    filterSubstituteByMediaType(mediaType)
    {
        this.__$mediaSubstitutesContainer.find(`.substitute`).addClass('hidden');
        this.__$mediaSubstitutesContainer.find(`.substitute[data-media_type*='${ mediaType }']`).removeClass('hidden');
    }



    enable()
    {
        super.enable();
        this.onClickOnReplacementButton(true)
            .onClickOnPopupCloseButton(true)
            .onClickOnSubstitute(true)
            .onClickOnRemplacementValidationButton(true)
            .onClickOnFileTypeOrMediaTypeFilter(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnReplacementButton(false)
            .onClickOnPopupCloseButton(false)
            .onClickOnSubstitute(false)
            .onClickOnRemplacementValidationButton(false)
            .onClickOnFileTypeOrMediaTypeFilter(false)
        ;
    }

}

export default MediaReplacementPopupHandler;