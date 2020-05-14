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

                $("table tbody").empty();

                $(".model-upload-file .start_upload_button").fadeOut();

                $(".upload-title").text("Préparation de l'Upload");
                $(".files_selection").fadeIn();
                $(".on_progress").fadeOut();
                $(".edit_media_info").fadeOut();

                this.__filesToUpload = [];

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
        //console.log(this.__authorizedExtensions); debugger
        Object.keys(this.__authorizedExtensions).forEach( (key) => {

            if(this.__authorizedExtensions[key].indexOf(extension) !== -1)
                output = true;

        } );

        return output;

    }

    isFileIsAlreadyUploaded(file)
    {

        return new Promise( (resolve, reject) => {

            $.ajax({
                type: 'post',
                url: '/file/is/uploaded',
                data: { file: file },
                async: false
            })

                .done( (response) => {

                    resolve(parseInt(response) === 0) ;

                } )

                .fail( (errorType, errorStatus, errorThrown) => {

                    reject(new Error(errorType)) ;

                } );

        } );

    }

    onFileSelectAddFileInList(active)
    {

        if(active)
        {
            $("#uploadmedia").on("change.onFileSelectAddFileInList", e => {

                const uploadButton = $(e.currentTarget);
                let filesSelected = uploadButton[0].files;

                filesSelected.forEach( async (file, index) => {
                    //console.log(filesSelected); debugger
                    let fileName = file.name;
                    let fileExtension = fileName.split('.').pop();
                    let fileIsAccepted = this.isFileExtensionIsAccepted(fileExtension);

                    // don't duplicate file in upload list
                    if( $(`.upload-info table tbody tr[data-file='${fileName}']`).length < 1 )
                    {
                        //console.log("azaz2222222222222"); debugger

                        const fileIsAlreadyUploaded = await this.isFileIsAlreadyUploaded(fileName);

                        let newUploadFileItem = $("<tr>", {
                            class: (!fileIsAccepted || fileIsAlreadyUploaded) ? 'invalid_upload_item' : 'upload_item'
                        }).attr("data-file", fileName);

                        let html = `<td>${fileName}</td>`;

                        if(fileIsAlreadyUploaded)
                            html += `<td><span style='color: red;'>Un fichier portant le même nom a déjà été uploadé ! Il ne sera pas uploadé</span></td>`;

                        else if(!fileIsAccepted)
                            html += `<td><span style='color: red;'>Ce type de fichier n'est pas accepté ! Il ne sera pas uploadé</span></td>`;

                        else
                            html += `<td></td>`;

                        html += `<td><i class="${ (!fileIsAccepted) ? 'fas fa-times' : 'fas fa-check' }"></i></td>
                                 <td><i data-target="${fileName}" class="fas fa-trash-alt remove_file_from_list"></i></td>`;

                        if(fileIsAccepted && !fileIsAlreadyUploaded)
                        {

                            this.__filesToUpload.push( {index: index, name: fileName, file: file} );

                            $("<tr>", {
                                id: `upload_${index}`
                            }).html(`<td>${fileName}</td>
                                     <td><progress class="progress_bar" id="progress_${fileName}" max="100" value="0"></progress></td>   
                                     <td class="upload_state">En attente ...</td>
                                     <td><form data-target="${index}" action="#" class="upload_form"></form></td>`)
                              .appendTo( $(".on_progress table tbody") );

                        }

                        newUploadFileItem.html(html);

                        newUploadFileItem.appendTo( $(".upload-info table tbody") );

                        if($(".upload-info table tbody tr.upload_item").length > 0)
                            $(".model-upload-file .start_upload_button").fadeIn();

                        else
                            $(".model-upload-file .start_upload_button").fadeOut();

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

                if( $(".upload-info table tbody tr").length < 1 )
                    $(".model-upload-file .start_upload_button").fadeOut();

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

                //$(".files_selection").fadeOut();

                $(".files_selection").fadeOut();
                $(".files_selection table tbody").empty();
                $(".upload-title").text("Traitement de la file en cours, veuillez patienter merci");
                $(".on_progress").fadeIn();

                // pause de 10s, sans ça le telechargement est lancé et fini avant que le contenu de la popup ne soit affiché
                // vraiment nécessaire ???
                setTimeout( () => {

                    // for each file in upload list
                    for (let i = 0; i < this.__filesToUpload.length; i++)
                    {

                        const fileToUpload = this.__filesToUpload[i];

                        $(`form.upload_form[data-target=${fileToUpload.index}]`).on("submit", e => {

                            e.preventDefault();

                            const formData = new FormData();

                            formData.append('file', fileToUpload.file);

                            const xhr = new XMLHttpRequest();

                            // on upload progress
                            xhr.upload.addEventListener("progress", e => {

                                const percent =  e.lengthComputable ? (e.loaded / e.total) * 100 : 0;

                                let progressBarWith = Math.round( percent.toFixed(2) );

                                $(`.on_progress #upload_${fileToUpload.index} progress`).addClass("on_upload")
                                                                                        .attr("value", progressBarWith);

                                if(progressBarWith === 100)
                                    $(`.on_progress #upload_${fileToUpload.index} .upload_state`).html("Traitement en cours ...</i>");

                                console.log(progressBarWith);

                            });

                            // on upload end
                            xhr.addEventListener("load", e => {

                                console.log(e);

                                $(`.on_progress #upload_${fileToUpload.index} progress`).removeClass("on_upload")
                                                                                        .addClass("upload_finish");

                                $(`.on_progress #upload_${fileToUpload.index} .upload_state`).html("Téléchargement réussi <i class='fas fa-check'></i>");

                                if(typeof this.__filesToUpload[i + 1] === "undefined")
                                    if( $(".on_progress .on_upload").length < 1 )
                                        $(".on_progress .show_media_edit_container").fadeIn();

                                    else
                                        $(".on_progress .show_media_edit_container").fadeOut();

                            })


                            // send data
                            xhr.open("POST", "/upload/media");
                            $(`.on_progress #upload_${fileToUpload.index} .upload_state`).html("Téléchargement en cours ...");
                            xhr.send(formData);

                        });

                        $(`form.upload_form[data-target=${fileToUpload.index}]`).submit();

                    }

                }, 1000);

            })
        }
        else
        {
            $(".model-upload-file .start_upload_button").off("click.onClickOnStartUploadButtonStartUpload");
        }

        return this;
    }


    onClickOnNextButtonShowMediaInfosEditContainer(active)
    {

        if(active)
        {
            $(".show_media_edit_container").on("click.onClickOnNextButtonShowMediaInfosEditContainer", e => {

                //$(".files_selection").fadeOut();
                $(".on_progress").fadeOut();
                $(".on_progress table tbody").empty();
                $(".upload-title").text("Médias à caractériser");
                $(".edit_media_info").fadeIn();

            })
        }

        else
        {
            $(".show_media_edit_container").off("click.onClickOnNextButtonShowMediaInfosEditContainer");
        }

        return this;
    }


    enable()
    {
        super.enable();
        this.onClickOnUploadButtonShowModal(true)
            .onClickOnModalCloseButtonsCloseModal(true)
            .onPageLoadGetFilesAuthorizedExtensions(true)
            .onFileSelectAddFileInList(true)
            .onClickOnStartUploadButtonStartUpload(true)
            .onClickOnNextButtonShowMediaInfosEditContainer(true)
            .onClickOnRemoveFileButtonRemoveFileFromList(true)
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
            .onClickOnStartUploadButtonStartUpload(false)
            .onClickOnNextButtonShowMediaInfosEditContainer(false)
            .onClickOnRemoveFileButtonRemoveFileFromList(false)
        ;
    }

}

export default UploadHandlerTool;