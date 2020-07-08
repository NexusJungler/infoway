import SubTool from "../../SubTool";

class MediaExpandedMiniatureDisplayHandler extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $('.popup_media_expanded_miniature_container');
        this.__$location = $('.popup_media_expanded_miniature');
        this.__currentMediaInfos = {
            id: null,
            name: null,
            customer: null,
            mediumFileExist: null,
            fileType: null
        };
    }

    initializePopupContent()
    {

        let miniature = '';

        if(this.__currentMediaInfos.mediumFileExist)
        {

            if(this.__currentMediaInfos.fileType === 'image')
                miniature = `<img class="media_expanded_miniature miniature_img" src="/miniatures/${ this.__currentMediaInfos.customer }/images/medium/${ this.__currentMediaInfos.id }.png"
                             alt="/miniatures/${ this.__currentMediaInfos.customer }/images/medium/${ this.__currentMediaInfos.id }.png" />`;

            else
                miniature = `<video class="media_expanded_miniature miniature_video" controls>
                                <source src="/miniatures/${ this.__currentMediaInfos.customer }/videos/medium/${ this.__currentMediaInfos.id }.mp4" type="video/mp4">          
                             </video>`;

        }
        else
            miniature = `<img class="media_expanded_miniature miniature_${ this.__currentMediaInfos.fileType }" src="/build/images/no-available-image.png" alt="/build/images/no-available-image.png">`;

        this.__$location.find('.media_name').text( this.__currentMediaInfos.name );

        this.__$location.find('.expanded_miniature_container').html(miniature);

        this.__$container.addClass('is_open');
    }

    onClickOnMiniatureExpandedIcon(active)
    {
        if(active)
        {

            $('.show_expanded_miniature').on('click.onClickOnMiniatureExpandedIcon', e => {

                const mediaMediumMiniatureExist = $(e.currentTarget).parents('.media_miniature_container').data('miniature_medium_exist');


                if(typeof mediaMediumMiniatureExist === 'undefined')
                    console.error("Missing data-miniature_medium_exist on media miniature container !");

                else
                {
                    const id = $(e.currentTarget).parents('.card').attr('id').replace('media_', '');
                    const fileType = $(e.currentTarget).parents('.card').data('file_type');
                    const customer = $(e.currentTarget).parents('.card').data('customer');
                    const name = $(e.currentTarget).parents('.card').find('.media_name_container .media_name').text();

                    this.__currentMediaInfos = {
                        id: id,
                        name: name,
                        customer: customer,
                        mediumFileExist: mediaMediumMiniatureExist,
                        fileType: fileType
                    };

                    this.initializePopupContent();
                }

            })

        }
        else
        {
            $('.show_expanded_miniature').off('click.onClickOnMiniatureExpandedIcon');
        }

        return this;
    }

    onClickOnPopupCloseButton(active)
    {
        if(active)
        {
            this.__$location.find('.close_modal_button').on('click.onClickOnPopupCloseButton', e => {

                this.__$container.removeClass('is_open');
                this.__$location.find('.expanded_miniature_container').empty();
                this.__$location.find('.media_name').empty();

            })
        }
        else
        {
            this.__$location.find('.close_modal_button').off('click.onClickOnPopupCloseButton');
        }

        return this;
    }

    enable()
    {
        super.enable();
        this.onClickOnMiniatureExpandedIcon(true)
            .onClickOnPopupCloseButton(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onClickOnMiniatureExpandedIcon(false)
            .onClickOnPopupCloseButton(false)
        ;
    }

}

export default MediaExpandedMiniatureDisplayHandler;