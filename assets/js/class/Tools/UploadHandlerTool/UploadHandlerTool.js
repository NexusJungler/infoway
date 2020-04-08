import Tool from "../Tool"


class UploadHandlerTool extends Tool
{

    constructor()
    {
        super();
        this.__name = "UploadHandlerTool";
        this.__authorizedExtensions = null;
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
                    this.__authorizedExtensions = extensions;

                } )

                .fail( (errorType, errorStatus, errorThrown) => {

                    throw new Error(errorType);

                } );
        }

        return this;

    }

    isFileExtensionIsAccepted(extension)
    {
        let output = false;

        Object.keys(this.__authorizedExtensions).forEach( (key) => {

            if(this.__authorizedExtensions[key].indexOf(extension) !== -1)
                output = true;

        } );

        return output;

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

                let filesSelected = $(e.currentTarget)[0].files;

                filesSelected.forEach( (file) => {

                    let fileName = file.name;
                    let fileExtension = fileName.split('.').pop();
                    let fileIsAccepted = this.isFileExtensionIsAccepted(fileExtension);

                    console.log(file, fileName, fileExtension);

                    if( $(`.upload-info table tbody tr td[data-file='${fileName}']`).length < 1 )
                    {

                        let newUploadFileItem = $("<tr>", {

                        });

                        let fileNameContainer = $("<td>", {

                            text: fileName

                        }).attr("data-file", fileName)
                          .appendTo(newUploadFileItem);


                        let fileIsNotAcceptedErrorMessageContainer = $("<td>", {

                        });


                        let fileIsAcceptedResultContainer =  $("<td>", {

                        });

                        if(!fileIsAccepted)
                        {
                            let fileIsNotAcceptedErrorMessage = $("<span>", {

                                text: "Ce type de fichier n'est pas accepté ! Il ne sera pas être uploader",

                            }).appendTo(fileIsNotAcceptedErrorMessageContainer);
                        }

                        let fileIsAcceptedResultIcon =  $("<i>", {

                            class: (fileIsAccepted === true) ? 'fas fa-check' : 'fas fa-times'

                        }).appendTo(fileIsAcceptedResultContainer);

                        fileIsNotAcceptedErrorMessageContainer.appendTo(newUploadFileItem);

                        fileIsAcceptedResultContainer.appendTo(newUploadFileItem);

                        newUploadFileItem.appendTo( $(".upload-info table tbody") );

                    }

                } )




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