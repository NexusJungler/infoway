import SubTool from "../../../SubTool";
import MediaReplacementSubTool from "../MediaReplacementSubTool";

class RemplacementInProgHandler extends MediaReplacementSubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
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

    enable()
    {
        super.enable();
    }

    disable()
    {
        super.disable();
    }

}

export default RemplacementInProgHandler;