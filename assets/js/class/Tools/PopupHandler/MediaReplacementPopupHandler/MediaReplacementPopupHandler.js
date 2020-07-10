import SubTool from "../../SubTool";

class MediaReplacementPopupHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_media_remplacement_basic_container');
        this.__$location = $('.popup_media_remplacement_basic');
        this.__$mediaSubstitutesContainer = $('.substitutes_container');

        this.__subTools = [

        ];

        this.__currentMediaInfos = {
            id: null,
            name: null,
            customer: null,
            type: null,
            extension: null,
            mediaLowMiniatureExist: null,
        };

        this.__replacementInfos = {
            mediaToReplaceId: null,
            mediaToReplaceType: null,
            fileType: null,
            substituteId: null,
            remplacementDate: {
                start: null,
                end: null,
            }
        };
    }

    async initializePopupContent()
    {

        if(this.__currentMediaInfos.mediaLowMiniatureExist === null)
        {
            const url = `/miniatures/${ this.__currentMediaInfos.customer }/${ this.__currentMediaInfos.type }/low/${ this.__currentMediaInfos.id }.${ (this.__currentMediaInfos.type === 'image') ? 'png' : 'mp4' }`;
            this.__currentMediaInfos.mediaLowMiniatureExist = await this.mediaLowMiniatureExist(url);
            let miniature = '';

            if(this.__currentMediaInfos.mediaLowMiniatureExist === false)
                miniature = `<img class="media_miniature miniature_${ this.__currentMediaInfos.type }" src="/build/images/no-available-image.png" alt="/build/images/no-available-image.png">`;

            else
            {

                if(this.__currentMediaInfos.type === 'image')
                    miniature = `<img class="media_miniature miniature_img" src="/miniatures/${ this.__currentMediaInfos.customer }/image/low/${ this.__replacementInfos.mediaId }.png"
                             alt="/miniatures/${ this.__currentMediaInfos.customer }/image/low/${ this.__replacementInfos.mediaId }.png" />`;

                else
                    miniature = `<video class="media_miniature miniature_video" controls>
                                <source src="/miniatures/${ this.__currentMediaInfos.customer }/video/low/${ this.__currentMediaInfos.id }.mp4" type="video/mp4">          
                             </video>`;

            }

            $(miniature).appendTo( this.__$location.find('.media_miniature_container') );
        }

        this.__$location.find('.media_associated_datas_container').append( $('.media_criterions_container').clone(true) );
        this.__$location.find('.media_associated_datas_container').append( $('.media_tags_list').clone(true) );

    }

    getMediaProgrammingInfos(mediaId)
    {

        return new Promise( (resolve, reject) => {

            $.ajax({
                url: `/get/media/${mediaId}/programming/infos`,
                type: "POST",
                data: {},
                success: (response) => {

                    $('.popup_loading_container').css({ 'z-index': '' }).removeClass('is_open');

                    resolve(response);

                },
                error: (response, status, error) => {

                    $('.popup_loading_container').css({ 'z-index': '' }).removeClass('is_open');

                    console.error(response); debugger

                    reject(response);

                },
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

                // filter_by_media_category_container

                $('.popup_loading_container').css({ 'z-index': 100000 }).addClass('is_open');

                this.__replacementInfos.mediaToReplaceId = $('.col.top .media_miniature_container').data('media_id');
                this.__replacementInfos.fileType = $('.col.top .media_miniature_container').data('file_type');

                const mediaNameText = $('.col.middle .media_name_container .media_name').val();

                this.__$location.find('.media_name_container').html( `<p class="media_name">${mediaNameText}</p>` );

                const remplacementDateMin = $('.col.top .media_diff_start_container .media_diff_start').val();

                this.__$location.find('.remplacement_date_choice_input.remplacement_date_start').prop('min', remplacementDateMin);

                this.__currentMediaInfos = {
                    id: this.__replacementInfos.mediaToReplaceId,
                    name: mediaNameText,
                    customer: $('.media_miniature').parents('.media_miniature_container').data('customer'),
                    type: ($('.media_miniature').parents('.media_miniature_container').data('file_type') === 'image') ? 'image' : 'video',
                    extension: ( $('.media_miniature').parents('.media_miniature_container').data('file_type') === 'image' ) ? 'png' : 'mp4',
                };

                await this.initializePopupContent();

                $('.popup_loading_container').css({ 'z-index': '' }).removeClass('is_open');

                this.__$container.addClass('is_open');

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
                }
                else
                {

                    this.__$mediaSubstitutesContainer.find('.card.selected .choice_substitute').prop('checked', false);
                    this.__$mediaSubstitutesContainer.find('.card.selected').removeClass('selected');
                    cardSelected.addClass('selected');

                    this.__replacementInfos.substituteId = cardSelected.data('media_id');
                    this.__replacementInfos.substituteType = cardSelected.data('media_id');

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

                if(this.__$location.find('.remplacement_date_start').val() === "")
                    this.__$location.find('.remplacement_date_start').addClass('empty');

                else
                    this.__$location.find('.remplacement_date_start').removeClass('empty');

                if(this.__$location.find('.remplacement_date_end').val() === "")
                    this.__$location.find('.remplacement_date_end').addClass('empty');

                else
                    this.__$location.find('.remplacement_date_end').removeClass('empty');

                if(this.__replacementInfos.substituteId !== null && this.__$location.find('.remplacement_date_start').val() !== "" && this.__$location.find('.remplacement_date_end').val() !== "")
                {

                    this.__replacementInfos.remplacementDate.start = this.__$location.find('.remplacement_date_start').val();
                    this.__replacementInfos.remplacementDate.end = this.__$location.find('.remplacement_date_end').val();

                    if(confirm(`Le média '${ this.__currentMediaInfos.name }' sera remplacé puis archivé ! Êtres-vous sûr de vouloir continuer ?`))
                        this.sendRemplacementDatas();
                }

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

        $('.popup_loading_container').css({ 'z-index': 100000 }).addClass('is_open');

        $.ajax({
            url: `/replace/media/in/mediatheque`,
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
                $('.popup_loading_container').css({ 'z-index': '' }).removeClass('is_open');
            }

        })

    }

    enable()
    {
        super.enable();
        this.onClickOnReplacementButton(true)
            .onClickOnPopupCloseButton(true)
            .onClickOnSubstitute(true)
            .onClickOnRemplacementValidationButton(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnReplacementButton(false)
            .onClickOnPopupCloseButton(false)
            .onClickOnSubstitute(false)
            .onClickOnRemplacementValidationButton(false)
        ;
    }

}

export default MediaReplacementPopupHandler;