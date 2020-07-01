import SubTool from "../../SubTool";

class MediaReplacementPopupHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_media_remplacement_basic_container');
        this.__$location = $('.popup_media_remplacement_basic');
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

    initializePopupContent(mediaProgrammingInfos)
    {

        console.table( mediaProgrammingInfos ); debugger

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


    enable()
    {
        super.enable();
        this.onClickOnReplacementButton(true)
            .onClickOnPopupCloseButton(true)
            .onReplacementLocationChange(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnReplacementButton(false)
            .onClickOnPopupCloseButton(false)
            .onReplacementLocationChange(false)
        ;
    }

}

export default MediaReplacementPopupHandler;