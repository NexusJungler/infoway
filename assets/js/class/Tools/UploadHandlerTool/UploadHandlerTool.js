import Tool from "../Tool"


class UploadHandlerTool extends Tool
{

    constructor()
    {
        super();
        this.__name = "UploadHandlerTool";
        this.__authorizedExtensions = null;
        this.__filesToUpload = [];
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
                $("#uploadmedia").val("");
                $(".upload-info table .tbody-content").empty();
                $(".model-upload-file .start_upload_button").hide();

                $('.main-media .upload-file').show();
                $('.main-media .product-btn-add').show();
                $('.main-media .upload-title').show();
                $('.main-media .download-file').hide();

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

    onFileSelectAddFileInList(active)
    {

        if(active)
        {
            $("#uploadmedia").on("change.onFileSelectAddFileInList", e => {

                const uploadButton = $(e.currentTarget);
                let filesSelected = uploadButton[0].files;

                filesSelected.forEach( (file, index) => {
                    //console.log(filesSelected); debugger
                    let fileName = file.name;
                    let fileExtension = fileName.split('.').pop();
                    let fileIsAccepted = this.isFileExtensionIsAccepted(fileExtension);

                    //console.log(file, fileName, fileExtension);

                    // don't duplicate file in upload list
                    if( $(`.upload-info table tbody tr[data-file='${fileName}']`).length < 1 )
                    {

                        this.__filesToUpload.push( {index: index, name: fileName, file: file} );

                        let newUploadFileItem = $("<tr>", {

                        }).attr("data-file", fileName);

                        let html = `<td>${fileName}</td>
                                    <td>${ (!fileIsAccepted) ? "<span style='color: red;'>Ce type de fichier n'est pas accepté ! Il ne sera pas être uploader</span>" : ''  }</td>
                                    <td><i class="${ (!fileIsAccepted) ? 'fas fa-times' : 'fas fa-check' }"></i></td>
                                    <td><i data-target="${fileName}" class="fas fa-trash-alt remove_file_from_list"></i></td>`;

                        if(fileIsAccepted)
                        {

                            $("<tr>", {
                                id: `upload_${index}`
                            }).html(`<td>${fileName}</td>
                                     <td><progress class="progress_bar" id="progress_${fileName}" max="100" value="0"></progress></td>   
                                     <td class="upload_state">Téléchargement en cours</td>`)
                              .appendTo( $(".on_progress table tbody") );

                        }

                        newUploadFileItem.html(html);

                        newUploadFileItem.appendTo( $(".upload-info table tbody") );

                        if($(".upload-info table tbody tr").length > 0)
                            $(".model-upload-file .start_upload_button").show();

                    }

                } );

                uploadButton.val("");

                //console.log(this.__filesToUpload); debugger


            })
        }
        else
        {
            $("#uploadmedia").off("change.onFileSelectAddFileInList");
        }

        return this;
    }


    onClickOnRemoveFileButtonRemoveFileFromList(active)
    {

        if(active)
        {
            $(".upload-info table tbody").on("click.onClickOnRemoveFileButtonRemoveFileFromList", ".remove_file_from_list", e => {

                // remove element from list
                $(`.upload-info table tbody tr[data-file="${ $(e.currentTarget).data('target') }"]`).remove();

                // remove element from array
                const index = this.__filesToUpload.findIndex( fileToUpload => fileToUpload.name === $(e.currentTarget).data('target') );
                this.__filesToUpload.splice( index, 1 );

            })
        }
        else
        {
            $(".upload-info table tbody").off("click.onClickOnRemoveFileButtonRemoveFileFromList", ".remove_file_from_list");
        }

        return this;
    }



    onClickOnStartUploadButtonStartUpload(active)
    {

        if (active)
        {
            $(".model-upload-file .start_upload_button").on("click.onClickOnStartUploadButtonStartUpload", e => {

                /*$('.main-media .upload-file').hide();
                $('.main-media .product-btn-add').hide();
                $('.main-media .upload-title').hide();
                $('.main-media .download-file').show();*/

                $(".files_selection").fadeOut();
                $(".upload-title").text("Traitement de la file en cours, veuillez patienter merci")
                $(".on_progress").fadeIn();

                this.__filesToUpload.forEach( (fileToUpload) => {

                    let form = $("<form>", {
                        method: "post",
                    });

                    form.on("submit", e => {

                        e.preventDefault();

                        const formData = new FormData();

                        formData.append('file', fileToUpload.file);

                        const xhr = new XMLHttpRequest();

                        xhr.upload.addEventListener("progress", e => {

                            const percent =  e.lengthComputable ? (e.loaded / e.total) * 100 : 0;
                            //let progressBarWith = percent.toFixed(2) + '%';
                            let progressBarWith = Math.round( percent.toFixed(2) );

                            $(`.on_progress #upload_${fileToUpload.index} progress`).attr("value", progressBarWith);
                            if(progressBarWith === 100)
                                $(`.on_progress #upload_${fileToUpload.index} .upload_state`).text("Téléchargement réussi");

                            console.log(progressBarWith);

                        });

                        xhr.addEventListener("load", e => {

                            console.log(e);
                            $(`.on_progress #upload_${fileToUpload.index} .upload_state`).text("Téléchargement réussi");

                        })

                        $(".upload_abort").on("click", e => {

                            xhr.abort();
                            console.log("abort"); debugger

                        })

                        xhr.open("POST", "/upload/media");
                        xhr.send(formData);

                    });

                    form.submit();

                } );

            })
        }
        else
        {
            $(".model-upload-file .start_upload_button").off("click.onClickOnStartUploadButtonStartUpload");
        }

        return this;
    }


    enable()
    {
        super.enable();
        this.onClickOnUploadButtonShowModal(true)
            .onClickOnModalCloseButtonsCloseModal(false)
            .onPageLoadGetFilesAuthorizedExtensions(true)
            .onFileSelectAddFileInList(true)
            .onClickOnRemoveFileButtonRemoveFileFromList(true)
            .onClickOnStartUploadButtonStartUpload(true)
        ;
    }

    disable()
    {
        super.disable();
        // call function with 'false' for remove events (if event was applied on DOM element by function)
        this.onClickOnUploadButtonShowModal(false)
            .onClickOnModalCloseButtonsCloseModal(false)
            .onPageLoadGetFilesAuthorizedExtensions(false)
            .onFileSelectAddFileInList(false)
            .onClickOnRemoveFileButtonRemoveFileFromList(false)
            .onClickOnStartUploadButtonStartUpload(false)
        ;
    }

}

export default UploadHandlerTool;