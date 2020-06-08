import SubTool from "../../SubTool";

class MediaInfoSheetHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_media_info_sheet_container');
        this.__$location = $('.popup_media_info_sheet');
    }

    onClickOnMediaMiniatureShowMediaInfoSheet(active)
    {
        if(active)
        {
            $('.media-miniature').on('click.onClickOnMediaMiniatureShowMediaInfoSheet', async(e) => {

                let mediaInfos = await this.retrieveMediaAssociatedInfos($(e.currentTarget).parents('.card').attr('id'));

                const mediaId = $(e.currentTarget).parents('.card').attr('id').replace('media_', '');

                const isImage = $(e.currentTarget).hasClass('miniature-image');

                if( isImage )
                {

                    this.__$location.find('.media_type').text('image');
                }

                else
                {
                    this.__$location.find('.media_type').text('video');
                }

                this.__$location.find('.media_title').text( $(e.currentTarget).parents('.card-body').find('.media-name').text() );
                this.__$location.find('.media_validity_container .media_diff_start').text( $(e.currentTarget).parents('.card').data('media_diff_start') );
                this.__$location.find('.media_validity_container .media_diff_end').text( $(e.currentTarget).parents('.card').data('media_diff_end') );

                if(this.getDaysDiffBetweenDates($(e.currentTarget).parents('.card').data('media_diff_end'), new Date()) <= 14)
                    this.__$location.find('.media_validity_container .media_diff_end').addClass('date-coming-soon');

                this.__$location.find('.media_infos_bottom .media_name_container .media_name').text( $(e.currentTarget).parents('.card-body').find('.media-name').text() );

                this.showMediaIncrustes($(e.currentTarget).parents('.card').attr('id'));
                this.showMediaAssociatedProdcuts($(e.currentTarget).parents('.card').attr('id'));
                this.showMediaCharacteristics($(e.currentTarget).parents('.card').attr('id'), isImage);


                this.__$container.addClass('is_open');

            })
        }
        else
        {
            $('.media-miniature').off('click.onClickOnMediaMiniatureShowMediaInfoSheet');
        }

        return this;
    }

    onClickOnPopupCloseButton(active)
    {
        if(active)
        {
            this.__$location.find('.close_modal_button').on('click.onClickOnPopupCloseButton', e => {

                this.__$container.removeClass('is_open');

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

    retrieveMediaAssociatedInfos(mediaId)
    {
        return 0;
        mediaId = mediaId.replace('media_', '');

        return new Promise( (resolve, reject) => {

            $.ajax({
                url: "/retrieve/media/associated/infos",
                type: "POST",
                data: {media: mediaId},
                success: (response) => {
                    console.log(response); //debugger
                    resolve(response);

                },
                error: (response, status, error) => {

                    console.error(response); //debugger
                    resolve(false);

                },
            })

        } );

    }

    showMediaCharacteristics(mediaChooseId, mediaIsImage)
    {

        let characteristics = '<span>' + $(`#${mediaChooseId}`).find('.media-miniature-container').data('size') + ' px</span>, <span>' + $(`#${mediaChooseId}`).find('.media-miniature-container').data('extension') + '</span>, <span></span>';

        if(mediaIsImage)
            characteristics += $(`#${mediaChooseId}`).find('.media-miniature-container').data('dpi') + ' dpi';

        else
            characteristics += $(`#${mediaChooseId}`).find('.media-miniature-container').data('codec');

        characteristics += '</span>';

        this.__$location.find('.media_characteristics_container .media_characteristics').html( characteristics );

    }

    showMediaIncrustes(mediaChooseId)
    {

    }

    showMediaAssociatedProdcuts(mediaChooseId)
    {

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