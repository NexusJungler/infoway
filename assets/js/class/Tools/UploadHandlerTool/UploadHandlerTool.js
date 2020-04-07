import Tool from "../Tool"


class UploadHandlerTool extends Tool
{

    constructor()
    {
        super();
        this.__name = "UploadHandlerTool";
        this.__authorizedExtensions = [];
    }


    onClickOnUploadButtonShowModal(active)
    {

        if(active)
        {
            $(".btn-upload").on("click.onClickOnUploadButtonShowModal", e => {

                $('.add-popup').addClass('is-open');

            })
        }
        else
        {
            $(".btn-upload").off("click.onClickOnUploadButtonShowModal");
        }

        return this;
    }

    onClickOnModalCloseButtonsCloseModal(active)
    {

        if(active)
        {
            $('.btn-popupclose').on("click.onClickOnModalCloseButtonsCloseModal",e => {

                $('.add-popup').removeClass('is-open');

            })

        }
        else
        {
            $('.btn-popupclose').off("click.onClickOnModalCloseButtonsCloseModal");
        }

        return this;
    }

    onPageLoadGetFilesAuthorizedExtensions(active)
    {

        // if tool is actived
        if(active)
        {
            $.ajax({
               type: 'get',
               url: "/get/files/authorized/extensions",
            })

                .done( (extensions) => {

                    console.table(extensions);

                } )

                .fail( (errorType, errorStatus, errorThrown) => {

                    throw new Error(errorType);

                } );
        }

        return this;

    }

    onFileUploadCheckFileExtension(active)
    {

        if(active)
        {

        }

        return this;

    }


    onFileSelectAddFileInList(active)
    {

        if(active)
        {
            $("#uploadmedia").on("change.onFileSelectAddFileInList", e => {

                console.log( $(e.currentTarget)[0].files ); debugger

                let newUploadFileItem = $("<tr>");

                $("<td>", {

                    text: 'fileName'

                }).appendTo(newUploadFileItem);

                $("<td>", {

                    text: ''

                }).appendTo(newUploadFileItem);

                $("<td>", {

                }).appendTo(newUploadFileItem);

                newUploadFileItem.appendTo( $(".upload-info table tbody") );


            })
        }
        else
        {
            $("#uploadmedia").off("change.onFileSelectAddFileInList");
        }

        return this;
    }


    enable()
    {
        super.enable();
        this.onClickOnUploadButtonShowModal(true)
            .onClickOnModalCloseButtonsCloseModal(true)
            .onPageLoadGetFilesAuthorizedExtensions(true)
            .onFileUploadCheckFileExtension(true)
            .onFileSelectAddFileInList(true)
        ;
    }

    disable()
    {
        super.disable();
        // call function with 'false' for remove events (if event was applied on DOM element by function)
        this.onClickOnUploadButtonShowModal(false)
            .onClickOnModalCloseButtonsCloseModal(false)
            .onPageLoadGetFilesAuthorizedExtensions(false)
            .onFileUploadCheckFileExtension(false)
            .onFileSelectAddFileInList(false)
        ;
    }

}

export default UploadHandlerTool;