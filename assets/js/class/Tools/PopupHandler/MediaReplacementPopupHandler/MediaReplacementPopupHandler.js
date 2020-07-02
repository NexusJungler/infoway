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

        this.__currentMediaInfos= {
            id: null,
            customer: null,
            type: null,
            extension: null,
            mediaLowMiniatureExist: null,
        };

        this.__replacementInfos = {
            mediaId: null,
            replaceByMediaId: null,
            replacementDate: {
                start: null,
                end: null,
            }
        };
    }

    async initializePopupContent()
    {

        const url = `/miniatures/${ this.__currentMediaInfos.customer }/${ this.__currentMediaInfos.type }/low/${ this.__currentMediaInfos.id }.${ (this.__currentMediaInfos.type === 'image') ? 'png' : 'mp4' }`;

        if(this.__currentMediaInfos.mediaLowMiniatureExist === null)
        {
            this.__currentMediaInfos.mediaLowMiniatureExist = await this.mediaLowMiniatureExist(url);
            let miniature = '';

            if(this.__currentMediaInfos.mediaLowMiniatureExist === false)
                miniature = `<img class="media_miniature miniature_${ this.__currentMediaInfos.type }" src="/build/images/no-available-image.png" alt="/build/images/no-available-image.png">`;

            else
            {

                if(this.__currentMediaInfos.type === 'image')
                    miniature = `<img class="media_miniature miniature_img" src="/miniatures/${ this.__currentMediaInfos.customer }/images/low/${ this.__replacementInfos.mediaId }.png"
                             alt="/miniatures/${ this.__currentMediaInfos.customer }/images/low/${ this.__replacementInfos.mediaId }.png" />`;

                else
                    miniature = `<video class="media_miniature miniature_video" controls>
                                <source src="/miniatures/${ this.__currentMediaInfos.customer }/videos/low/${ this.__currentMediaInfos.id }.mp4" type="video/mp4">          
                             </video>`;

            }

            $(miniature).appendTo( this.__$location.find('.media_miniature_container') );
        }

        $('.media_criterions_container').clone(true).appendTo( this.__$location.find('.media_associated_datas_container') );
        $('.media_tags_list').clone(true).appendTo( this.__$location.find('.media_associated_datas_container') );

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

                $('.popup_loading_container').css({ 'z-index': 100000 }).addClass('is_open');

                this.__replacementInfos.mediaId = $('.col.top .media_miniature_container').data('media_id');

                this.__currentMediaInfos = {
                    id: this.__replacementInfos.mediaId,
                    customer: $('.media_miniature').parents('.media_miniature_container').data('customer'),
                    type: ($('.media_miniature').parents('.media_miniature_container').data('media_type') === 'image') ? 'images' : 'videos',
                    extension: ( $('.media_miniature').parents('.media_miniature_container').data('media_type') === 'image' ) ? 'png' : 'mp4',
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

    onClickOnPopupCloseButton(active)
    {
        if(active)
        {
            this.__$location.find('.close_modal_button').on('click.onClickOnPopupCloseButton', e => {

                this.__$container.removeClass('is_open');
                this.__$location.find('.media_substitute_name').text('');
                this.__$mediaSubstitutesContainer.find('.card.selected').removeClass('selected');
                this.__$location.find('.validate_remplacement_btn').attr('disabled', true);
                this.__$location.find('.remplacement_infos_setting_date_container input.remplacement_date_choice_input').removeClass('empty');

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
                    this.__replacementInfos.replaceByMediaId = null;
                }
                else
                {

                    this.__$mediaSubstitutesContainer.find('.card.selected .choice_substitute').prop('checked', false);
                    this.__$mediaSubstitutesContainer.find('.card.selected').removeClass('selected');
                    cardSelected.addClass('selected');

                    this.__replacementInfos.replaceByMediaId = cardSelected.data('media_id');

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
                    this.__$location.find('.remplacement_date_start').removeClass('empty');

                if(this.__$location.find('.remplacement_date_start').val() !== "" && this.__$location.find('.remplacement_date_end').val() !== "")
                {

                    this.__replacementInfos.replacementDate.start = this.__$location.find('.remplacement_date_start').val();
                    this.__replacementInfos.replacementDate.end = this.__$location.find('.remplacement_date_end').val();
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
            url: '/file/miniature/exists',
            type: "POST",
            data: {datas: this.__replacementInfos},
            success: (response) => {

            },
            error: (response) => {
                console.error(response); debugger
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