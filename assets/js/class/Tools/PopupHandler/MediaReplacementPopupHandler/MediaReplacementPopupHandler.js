import SubTool from "../../SubTool";

class MediaReplacementPopupHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_media_remplacement_basic_container');
        this.__$location = $('.popup_media_remplacement_basic');
        this.__$mediaSubstitutesContainer = $('.substitutes_container .card_container');
        this.__replacementInfos = {
            mediaId: null,
            replaceByMediaId: null,
            replacementSettings: {
                replacementLocation: null,
                replacementDate: {
                    start: null,
                    end: null,
                }
            }
        };
    }

    initializePopupContent(mediaInfos)
    {

        console.table( mediaInfos ); debugger
        let mediaType = $('.media_miniature').parents('.media_miniature_container').data('media_type');
        console.log(mediaType); debugger
// Substitution de media | Menuboard / 5 ECR NAT Gouter / Ecran 3
        if(mediaInfos.mediaMiniatureExist === false)
        {
            $('.col.top .media_miniature_container .media_miniature').clone(true).appendTo( this.__$location.find('.media_miniature_container') );
        }
        else
        {
            let miniature = '';

            if(mediaType === 'image')
            {
                miniature = `<img class="media_miniature miniature_img" src="/miniatures/${ mediaInfos.customer }/images/low/${ this.__replacementInfos.mediaId }.png"
                             alt="/miniatures/${ mediaInfos.customer }/images/low/${ this.__replacementInfos.mediaId }.png" />`;
            }
            else
            {
                miniature = `<video class="media_miniature miniature_video" controls>
                                <source src="/miniatures/${ mediaInfos.customer }/videos/low/${ this.__replacementInfos.mediaId }.mp4" type="video/mp4">          
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

    onClickOnReplacementButton(active)
    {
        if(active)
        {
            $('.media_replacement_btn').on("click.onClickOnMediaReplacementButton", async(e) => {

                $('.popup_loading_container').css({ 'z-index': 100000 }).addClass('is_open');

                this.__replacementInfos.mediaId = $('.col.top .media_miniature_container').data('media_id');

                const mediaName = $('.col.middle .media_name_container .media_name').text() || $('.col.middle .media_name_container .media_name').val();

                $(`<p class="media_name"> ${ mediaName } </p>`).appendTo( this.__$location.find('.media_name_container') );

                this.initializePopupContent( await this.getMediaProgrammingInfos( this.__replacementInfos.mediaId ) );

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

            })
        }
        else
        {
            this.__$location.find('.close_modal_button').off('click.onClickOnPopupCloseButton');
        }

        return this;
    }


    onReplacementLocationChange(active)
    {
        if(active)
        {
            this.__$location.find('.media_replacement_location').on('click.onReplacementLocationChange', e => {

                console.log( $(e.currentTarget).val() ); debugger

            })
        }
        else
        {
            this.__$location.find('.media_replacement_location').off('click.onReplacementLocationChange');
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
                    cardSelected.removeClass('selected');
                    this.__$location.find('.media_substitute_name').text('');
                }
                else
                {

                    this.__$mediaSubstitutesContainer.find('.card.selected').removeClass('selected');
                    cardSelected.addClass('selected');

                    this.__$location.find('.media_substitute_name').text( cardSelected.find('.substitute_name').text() );

                }

            })
        }
        else
        {
            this.__$mediaSubstitutesContainer.find('.card').off('click.onClickOnSubstitute');
        }

        return this;
    }

    enable()
    {
        super.enable();
        this.onClickOnReplacementButton(true)
            .onClickOnPopupCloseButton(true)
            .onReplacementLocationChange(true)
            .onClickOnSubstitute(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnReplacementButton(false)
            .onClickOnPopupCloseButton(false)
            .onReplacementLocationChange(false)
            .onClickOnSubstitute(false)
        ;
    }

}

export default MediaReplacementPopupHandler;