import Tool from "../Tool"


class UploadHandlerTool extends Tool
{

    constructor()
    {
        super();
        this.__name = "UploadHandlerTool";

        // W3C authors recommend to specify both MIME-types and corresponding extensions in input type file "accept" attribute
        // @see : https://html.spec.whatwg.org/multipage/input.html#attr-input-accept
        this.__authorizedFiles = {

            image: [
                'image/jpg', 'image/jpeg', 'image/png', 'image/bmp', 'image/gif', 'image/x-windows-bmp', 'image/pjpeg', 'image/svg+xml',
                '.jpg', '.jpeg', '.png', '.bmp', '.gif', '.svg',
            ],

            video: [
                'video/*', 'video/mp4', 'video/avi', 'video/x-matroska', 'video/3gpp', 'video/quicktime', '.mp4', '.avi', '.3gp'
            ],

            audio: [
                'audio/3gpp', '.3gp', 'audio/mpeg', '.mp3'
            ],

            powerpoint: [
                'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', '.ppt', '.pptx'
            ],

            pdf: [
               '.pdf', 'application/pdf'
            ],

            word: [
                'application/msword', '.doc', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '.docx'
            ],

        };

        this.__errors = {

            bad_extension: "Erreur : ce type de fichier n'est pas accepté",

            bad_resolution: "Erreur : cette résolution n'est pas accepté",

            corrupt_file: "Erreur : fichier corrompu",

            duplicate_file: "Ce nom est déjà utilisé !",

            invalid_error: "Ce champ contient des caractères non autorisés !",

            empty_error: "Ce champ ne peut pas être vide",

            too_short_error: "Ce champ doit contenir au moins 5 caractères !",

            uploaded_file_not_found_error: "Erreur interne : Le fichier n'existe plus sur le serveur !",

        };

        this.__dataCheckingErrors = "";

        this.__uploadMediaType = $('.main-media').data('media_displayed');

        this.__total_files_allowed = 50;
        this.__max_file_size = 524288000;
        this.__filesToUpload = [];
        this.__authorized_char_regex = /^[a-zA-Z0-9_.-\s*]*$/;
        this.__uploadAuthorized = false;
    }


    onPageLoadAddFilterOnFileInput(active)
    {

        if(active)
        {
            $( () => {

                $(".uploadbtn").attr("accept", this.__authorizedFiles[this.__uploadMediaType]);

            } )
        }

        return this;

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

                $(".tbody").empty();

                $(".model-upload-file .start_upload_button").fadeOut();

                $(".upload-title").text("Préparation de l'Upload");
                $(".files_selection").fadeIn();
                $(".modal-upload-download").fadeOut();
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

    isFileMimeTypeIsAccepted(mime_type)
    {
        // search mime_type in authorized extension using upload current tab (image, video, video synchro, ...)
        return this.__authorizedFiles[this.__uploadMediaType].indexOf(mime_type) !== -1;
    }

    isFileIsAlreadyUploaded(file)
    {

        return new Promise( (resolve, reject) => {

            $.ajax({
                type: 'post',
                url: '/file/is/uploaded',
                data: { file: file }
            })

            .done( (response) => {
                resolve(parseInt(response) === 0);
            } )

            .fail( (errorType, errorStatus, errorThrown) => {
                reject(new Error(errorType));
            } );

        } );

    }

    fileNameContainMultipleDot(fileName)
    {
        // si le nom de fichier comporte plusieurs extensions
        let dotCount = 0;
        let pos = fileName.indexOf(".");
        while (pos !== -1) {
            dotCount++;
            pos = fileName.indexOf(".", pos+1);
        }

        return dotCount > 1;
    }
    
    async addItemInUploadList(item)
    {

        let fileName = item.name;

        console.log(item); //debugger

        let index = $(`.upload-info table tbody tr`).length + 1;

        // don't duplicate file in upload list
        if( $(`.upload-info table tbody tr[data-file='${fileName}']`).length < 1 )
        {

            const fileNameContainMultipleDot = this.fileNameContainMultipleDot(fileName);
            const fileExtension = (!fileNameContainMultipleDot) ? '.' + fileName.split('.').pop() : '';
            const fileIsAlreadyUploaded = await this.isFileIsAlreadyUploaded(fileName.replace(fileExtension, ''));
            const fileMimeType = item.type;

            console.log(fileNameContainMultipleDot, fileMimeType, fileExtension); //debugger

            // sometime file mimetype was empty
            // in this case, use extension for check if file can be upload
            const fileExtensionIsAccepted = this.isFileMimeTypeIsAccepted( (fileMimeType !== "") ? fileMimeType : fileExtension );
            console.log(fileExtensionIsAccepted); debugger
            const fileSize = item.size;
            let fileIsAccepted = true;

            let newUploadFileItem = $("<tr>", {
                class: (!fileExtensionIsAccepted || fileIsAlreadyUploaded || fileNameContainMultipleDot) ? 'invalid_upload_item' : 'valid_upload_item'
            }).attr("data-file", fileName);

            let html = `<td>${fileName}</td>`;

            if(!fileExtensionIsAccepted)
            {
                html += `<td><span class="error">Ce type de fichier n'est pas accepté ! Il ne sera pas uploadé</span></td>`;
                fileIsAccepted = false;
            }

            else if(fileIsAlreadyUploaded)
            {
                html += `<td><span class="error">Un fichier portant le même nom a déjà été uploadé ! Il ne sera pas uploadé</span></td>`;
                fileIsAccepted = false;
            }

            else if(fileSize > this.__max_file_size)
            {
                html += `<td><span class="error">Vous avez selectionné un fichier volumineux. Le temps de chargement pour un tel volume de données peut prendre un temps conséquent en fonction de votre connexion</span></td>`;
                fileIsAccepted = true;
            }

            else if(!this.__authorized_char_regex.test(fileName))
            {
                html += `<td><span class="error">Le nom de ce fichier comporte un ou plusieurs caractère(s) non autorisé !</span></td>`;
                fileIsAccepted = false;
            }

            else if(fileNameContainMultipleDot)
            {
                html += `<td><span class="error">Le nom de ce fichier comporte plusieurs extensions !</span></td>`;
                fileIsAccepted = false;
            }

            else
                html += `<td></td>`;

            html += `<td><i class="${ (!fileIsAccepted) ? 'fas fa-times' : 'fas fa-check' }"></i></td>
                                 <td><i data-target="${fileName}" class="fas fa-trash-alt remove_file_from_list"></i></td>`;

            if(fileIsAccepted && !fileIsAlreadyUploaded)
            {

                this.__filesToUpload.push( {index: index, name: fileName, file: item, media_type: this.__uploadMediaType} );

                $("<tr>", {
                    id: `upload_${index}`
                }).html(`<td>${fileName}</td>
                                     <td><progress class="progress_bar" id="progress_${fileName}" max="100" value="0"></progress></td>   
                                     <td class="upload_state">En attente ...</td>
                                     <td><form data-target="${index}" action="#" class="upload_form"></form></td>`)
                    .appendTo( $(".modal-upload-download table tbody") );

            }

            newUploadFileItem.html(html);

            newUploadFileItem.appendTo( $(".upload-info table tbody") );

            if($(".upload-info table tbody tr.valid_upload_item").length > this.__total_files_allowed)
            {
                this.__uploadAuthorized = false;
                $('.start-upload-container .error_over-max-file').text(`Vous avez sélectionné ${$(".upload-info table tbody tr.valid_upload_item").length} fichiers, le maximum autorisé est de ${this.__total_files_allowed} !"`)
                    .fadeIn();
                $(".model-upload-file .start_upload_button").fadeOut();
            }

            else if($(".upload-info table tbody tr.valid_upload_item").length < 1)
            {
                this.__uploadAuthorized = false;
                $(".model-upload-file .start_upload_button").fadeOut();
            }

            else
            {
                this.__uploadAuthorized = true;
                $('.start-upload-container .error_over-max-file').empty().fadeOut();
                $(".model-upload-file .start_upload_button").fadeIn();
            }

        }
        
    }

    onDragNDropFileAddFileList(active)
    {
        if(active)
        {

            $("#uploadForm .dragndrop_download_input_container").on("dragover", e => {

                e.preventDefault();

            });

            $("#uploadForm .dragndrop_download_input_container").on("dragenter", e => {

                $(e.currentTarget).addClass("on_dragenter");

            });

            $("#uploadForm .dragndrop_download_input_container").on("dragleave", e => {

                $(e.currentTarget).removeClass("on_dragenter");

            });


            $("#uploadForm .dragndrop_download_input_container").on("drop", e => {

                e.preventDefault();
                $(e.currentTarget).removeClass("on_dragenter");
                console.log('File(s) dropped !');
                let droppedFiles = e.originalEvent.dataTransfer.files;

                console.log(droppedFiles); //debugger

                droppedFiles.forEach( (droppedFile) => {

                    this.addItemInUploadList(droppedFile);

                } )

            });

        }
        else
        {
            $("#uploadForm .dragndrop_download_input_container").off("dragover");
            $("#uploadForm .dragndrop_download_input_container").off("dragenter");
            $("#uploadForm .dragndrop_download_input_container").off("dragleave");
            $("#uploadForm .dragndrop_download_input_container").off("drop");
        }

        return this;
    }

    onFileSelectAddFileInList(active)
    {

        if(active)
        {
            $("#uploadmedia").on("change.onFileSelectAddFileInList", e => {

                const uploadButton = $(e.currentTarget);
                let filesSelected = uploadButton[0].files;

                filesSelected.forEach( (file) => {

                    this.addItemInUploadList(file);

                    //console.log(filesSelected); debugger
                    /*let fileName = file.name;

                    console.log(file); //debugger

                    // don't duplicate file in upload list
                    if( $(`.upload-info table tbody tr[data-file='${fileName}']`).length < 1 )
                    {

                        const fileNameContainMultipleDot = this.fileNameContainMultipleDot(fileName);
                        const fileExtension = (!fileNameContainMultipleDot) ? '.' + fileName.split('.').pop() : '';
                        const fileIsAlreadyUploaded = await this.isFileIsAlreadyUploaded(fileName.replace(fileExtension, ''));
                        const fileMimeType = file.type;

                        console.log(fileNameContainMultipleDot, fileMimeType, fileExtension); //debugger

                        // sometime file mimetype was empt
                        // in this case, use extension for check if file can be upload
                        const fileExtensionIsAccepted = this.isFileMimeTypeIsAccepted( (fileMimeType !== "") ? fileMimeType : fileExtension );
                        const fileSize = file.size;
                        let fileIsAccepted = true;

                        let newUploadFileItem = $("<tr>", {
                            class: (!fileExtensionIsAccepted || fileIsAlreadyUploaded || fileNameContainMultipleDot) ? 'invalid_upload_item' : 'valid_upload_item'
                        }).attr("data-file", fileName);

                        let html = `<td>${fileName}</td>`;

                        if(fileIsAlreadyUploaded)
                        {
                            html += `<td><span class="error">Un fichier portant le même nom a déjà été uploadé ! Il ne sera pas uploadé</span></td>`;
                            fileIsAccepted = false;
                        }

                        else if(!fileExtensionIsAccepted)
                        {
                            html += `<td><span class="error">Ce type de fichier n'est pas accepté ! Il ne sera pas uploadé</span></td>`;
                            fileIsAccepted = false;
                        }

                        else if(fileSize > this.__max_file_size)
                        {
                            html += `<td><span class="error">Vous avez selectionné un fichier volumineux. Le temps de chargement pour un tel volume de données peut prendre un temps conséquent en fonction de votre connexion</span></td>`;
                            fileIsAccepted = true;
                        }

                        else if(!this.__authorized_char_regex.test(fileName))
                        {
                            html += `<td><span class="error">Le nom de ce fichier comporte un ou plusieurs caractère(s) non autorisé !</span></td>`;
                            fileIsAccepted = false;
                        }

                        else if(fileNameContainMultipleDot)
                        {
                            html += `<td><span class="error">Le nom de ce fichier comporte plusieurs extensions !</span></td>`;
                            fileIsAccepted = false;
                        }

                        else
                            html += `<td></td>`;

                        html += `<td><i class="${ (!fileIsAccepted) ? 'fas fa-times' : 'fas fa-check' }"></i></td>
                                 <td><i data-target="${fileName}" class="fas fa-trash-alt remove_file_from_list"></i></td>`;

                        if(fileIsAccepted && !fileIsAlreadyUploaded)
                        {

                            this.__filesToUpload.push( {index: index, name: fileName, file: file, media_type: this.__uploadMediaType} );

                            $("<tr>", {
                                id: `upload_${index}`
                            }).html(`<td>${fileName}</td>
                                     <td><progress class="progress_bar" id="progress_${fileName}" max="100" value="0"></progress></td>   
                                     <td class="upload_state">En attente ...</td>
                                     <td><form data-target="${index}" action="#" class="upload_form"></form></td>`)
                              .appendTo( $(".modal-upload-download table tbody") );

                        }

                        newUploadFileItem.html(html);

                        newUploadFileItem.appendTo( $(".upload-info table tbody") );

                        if($(".upload-info table tbody tr.valid_upload_item").length > this.__total_files_allowed)
                        {
                            this.__uploadAuthorized = false;
                            $('.start-upload-container .error_over-max-file').text(`Vous avez sélectionné ${$(".upload-info table tbody tr.valid_upload_item").length} fichiers, le maximum autorisé est de ${this.__total_files_allowed} !"`)
                                                                             .fadeIn();
                            $(".model-upload-file .start_upload_button").fadeOut();
                        }

                        else if($(".upload-info table tbody tr.valid_upload_item").length < 1)
                        {
                            this.__uploadAuthorized = false;
                            $(".model-upload-file .start_upload_button").fadeOut();
                        }

                        else
                        {
                            this.__uploadAuthorized = true;
                            $('.start-upload-container .error_over-max-file').empty().fadeOut();
                            $(".model-upload-file .start_upload_button").fadeIn();
                        }

                    }*/

                } );

                uploadButton.val("");

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

                if(this.__uploadAuthorized)
                {

                    let uploadFinished = 0;

                    $(".files_selection").fadeOut();
                    $(".files_selection table tbody").empty();
                    $(".upload-title").text("Traitement de la file en cours, veuillez patienter merci");
                    $(".modal-upload-download").fadeIn();

                    // for each file in upload list
                    $.each( this.__filesToUpload, (index, fileToUpload) => {

                        $(`.modal-upload-download #upload_${fileToUpload.index} .upload_state`).html("Téléchargement en cours ...");

                        const form = $(`form.upload_form[data-target=${fileToUpload.index}]`);
                        const uploadState = $(`.modal-upload-download #upload_${fileToUpload.index} .upload_state`);

                        form.on("submit", e => {

                            e.preventDefault();

                            const formData = new FormData();

                            formData.append('file', fileToUpload.file);
                            formData.append('media_type', fileToUpload.media_type); // will be saved in Request->request->parameters['media_type']

                            setTimeout( () => {
                                jQuery.ajax({
                                    url: "/upload/media",
                                    type: "POST",
                                    data: formData,
                                    contentType: false,
                                    cache: false,
                                    processData: false,
                                    xhr: function () {
                                        //upload Progress
                                        let xhr = jQuery.ajaxSettings.xhr();
                                        if (xhr.upload) {
                                            xhr.upload.addEventListener('progress', function (event) {
                                                var percent = 0;
                                                var position = event.loaded || event.position;
                                                var total = event.total;
                                                if (event.lengthComputable) {
                                                    percent = Math.ceil(position / total * 100);
                                                }
                                                //update progressbar
                                                $(`.modal-upload-download #upload_${fileToUpload.index} progress`).addClass("on_upload").attr("value", percent);
                                                uploadState.html(`Téléchargement en cours ... (${percent}%)`);
                                                //jQuery('#progress' + (index + 1) + ' .progress-bar').css("left", +percent + "%");

                                                if(percent === 100)
                                                    uploadState.html("Finalisation ...");

                                                //jQuery('#progress' + (index + 1) + ' .status').text(percent + "%");
                                            }, true);
                                        }

                                        return xhr;
                                    },
                                    mimeType: "multipart/form-data",
                                    success: (response) => {

                                        uploadState.html("Téléchargement terminé ! <i class='fas fa-check'></i>");
                                        $(`.modal-upload-download #upload_${fileToUpload.index} progress`).removeClass("on_upload");
                                        uploadFinished++;

                                        if(uploadFinished === this.__filesToUpload.length)
                                        {
                                            if($(`.modal-upload-download .on_progress`).length === 0)
                                            {
                                                $('button.upload_abort').fadeOut();
                                                $(".modal-upload-download .show_media_edit_container").fadeIn();
                                            }

                                            $(".upload-title").text("Traitement de la file terminé");

                                        }
                                    },
                                    error: (response, status, error) => {

                                        $(".modal-upload-download .show_media_edit_container").fadeOut();
                                        this.__filesToUpload.splice(index , 1);
                                        //$(`.modal-upload-download #upload_${fileToUpload.index} progress`).css({ 'color': 'red' });

                                        if(response.responseText === "513 Bad Resolution")
                                            uploadState.html(`${this.__errors.bad_resolution} <i class='fas fa-times'></i>`);

                                        else if(response.responseText === "515 Duplicate File")
                                            uploadState.html(`${this.__errors.duplicate_file} <i class='fas fa-times'></i>`);

                                        else if(response.responseText === "516 Invalid Filename")
                                            uploadState.html(`${this.__errors.invalid_error} <i class='fas fa-times'></i>`);

                                        else if(response.responseText === "517 Empty Filename")
                                            uploadState.html(`${this.__errors.empty_error} <i class='fas fa-times'></i>`);

                                        else
                                            $(`.modal-upload-download #upload_${fileToUpload.index} .upload_state`).html("Téléchargement annulé suite à une erreur interne ! <i class='fas fa-times'></i>");

                                        console.log(response.responseText); debugger
                                    }
                                });
                            }, 500);

                        });

                        form.submit();

                    } )

                }

            })
        }
        else
        {
            $(".model-upload-file .start_upload_button").off("click.onClickOnStartUploadButtonStartUpload");
        }

        return this;
    }

    checkMediaNameValidity(mediaName)
    {
        const input = $('<input>', {
            type: 'text',
            value: mediaName
        });

        return this.checkFormInputValidity(input);

    }

    checkFormInputValidity(input)
    {

        let inputIsValid = true;
        this.__dataCheckingErrors = "";

        if(input.val() === "")
        {
            inputIsValid = false;
            this.__dataCheckingErrors += this.__errors.empty_error + '<br>';
        }

        if( input.val().match(/(\w)*\.(\w)*/) && input.attr('type') === "text" )
        {
            inputIsValid = false;
            this.__dataCheckingErrors += this.__errors.invalid_error + '<br>';
        }

        if( input.val().length < 5 && input.attr('type') === "text" )
        {
            inputIsValid = false;
            this.__dataCheckingErrors += this.__errors.too_short_error + '<br>';
        }

        return inputIsValid;

    }

    onClickOnNextButtonShowMediaInfosEditContainer(active)
    {

        if(active)
        {
            $(".show_media_edit_container").on("click.onClickOnNextButtonShowMediaInfosEditContainer", e => {

                if(this.__filesToUpload.length > 0)
                {

                    //$(".files_selection").fadeOut();
                    $(".modal-upload-download").fadeOut();
                    $(".modal-upload-download table tbody").empty();

                    $.each( this.__filesToUpload, (index, fileToUpload) => {

                        const file = fileToUpload.file;
                        const fileExtension = file.name.split('.').pop();
                        const fileName = file.name.split('.')[0];

                        const img = new Image();
                        img.src = window.URL.createObjectURL(file);

                        const fileNameIsValid = this.checkMediaNameValidity(fileName);
                        //console.log(fileNameIsValid); debugger
                        let html = `<form class="tr" method="post"> 

                                    <div class="td"><img class="preview" src="${img.src}" alt="${file.name}" /></div>
                                    
                                    <div class="td">
                                        <span>${fileExtension}</span> <br>
                                        <span>${img.width}*${img.height}px</span> <br>
                                        <span>__RESOLUTION__</span>
                                    </div>
                                    
                                    <div class="td">
                                        <div class="hidden">
                                            <input type="hidden" name="files[${index}][extension]" value="${fileExtension}">
                                            <input type="hidden" name="files[${index}][old-name]" value="${fileName}">
                                        </div>
                                        
                                        <div>
                                            <span class="error ${ !fileNameIsValid ? '' : 'hidden'}"> ${ !fileNameIsValid ? this.__dataCheckingErrors : '' } </span> <br>
                                            <input type="text" name="files[${index}][name]" class="form_input fileName ${ !fileNameIsValid ? 'invalid' : ''}" placeholder="Nom du fichier" value="${fileName}" required="required">
                                        </div>
                                      
                                    </div>
                                    
                                    <div class="td media-diff-date-container">
                                        <button type="button" id="files[${index}]" data-media="files[${index}]" class="add-diff-date">Définir la période de diffusion</button>
                                    </div>
                                    
                                    <div class="td">
                                        <button class="associate-tag">Associer TAGS</button>
                                    </div>
                                    
                                    <div class="td">
                                        <button class="associate-product">Associer produit</button>
                                    </div>
                                    
                                    <div class="td">
                                        <div> 
                                            <label><input type="radio" name="files[${index}][add-price-incruste]" value="yes" required="required">Oui</label>
                                            <label><input type="radio" name="files[${index}][add-price-incruste]" value="no" required="required" checked>Non</label> </div>
                                        </div>
                                    
                                </form>`;

                        $(html).appendTo( $(".edit_media_info .tbody") )

                    } )

                    $(".upload-title").text("Médias à caractériser");
                    $(".edit_media_info").fadeIn();

                }

            })
        }

        else
        {
            $(".show_media_edit_container").off("click.onClickOnNextButtonShowMediaInfosEditContainer");
        }

        return this;
    }

    onClickOnAddDiffusionDateShowInput(active)
    {

        if(active)
        {

            $(".edit_media_info .tbody").on("click.onClickOnAddDiffusionDateShowInput", ".add-diff-date", e => {

                $(e.currentTarget).fadeOut();

                let date = new Date();
                date.setMonth( date.getMonth() + 1 );
                const minDate = `${date.getFullYear()}-${(date.getMonth() < 10) ? '0' + date.getMonth() : date.getMonth()}-${date.getDate()}`;

                let html = `<button type="button" class="remove-diffusion-date">Supprimer la periode de diffusion</button> <br>
                            <label for="${e.currentTarget.id}[diffusion-start-date]">Du</label><input type="date" class="form_input" id="${ e.currentTarget.id }[diffusion-start-date]" name="${ $(e.currentTarget).data('media') }[diffusion-start-date]" required="required"> <br>
                            <label for="${e.currentTarget.id}[diffusion-end-date]">Au</label><input type="date" min="${minDate}" class="form_input" id="${ e.currentTarget.id }[diffusion-end-date]" name="${ $(e.currentTarget).data('media') }[diffusion-end-date]" required="required">`;

                $(e.currentTarget).parent().html( html );

            })
        }
        else
        {
            $('.add-diff-date').off("click.onClickOnAddDiffusionDateShowInput");
        }

        return this;
    }

    onClickOnRemoveDiffusionDateButton(active)
    {

        if(active)
        {
            $(".edit_media_info .tbody").on("click.onClickOnRemoveDiffusionDateButton", ".remove-diffusion-date", e => {

                $(e.currentTarget).parent().html( `<button class="add-diff-date">Définir la période de diffusion</button>` );

            })
        }
        else
        {
            $('.media-diff-date-container').off("click.onClickOnRemoveDiffusionDateButton", ".remove-diffusion-date");
        }

        return this;
    }

    onClickOnSaveButtonSendMediaInfo(active)
    {

        if(active)
        {
            $('.save-media-modif').on('click.onClickOnSaveButtonSendMediaInfo', e => {

                $('.edit_media_info .tbody form').each( (index, form) => {

                    $(form).on("submit", e => {

                        e.preventDefault();

                        jQuery.ajax({
                            type: 'post',
                            url: '/edit/media',
                            data: $(e.currentTarget).serialize(),
                            success: (response) => {
                                console.log(response); debugger
                            },
                            error: (response, status, error) => {

                                if(response.responseText === "404 File Not Found")
                                {
                                    $(form).find('.form_input').parent().find("span.error").text( this.__errors.uploaded_file_not_found_error );
                                    $(form).find('.form_input').addClass('invalid');
                                }

                                if(response.responseText === "515 Duplicate File")
                                {
                                    $(form).find('.form_input.fileName').parent().find("span.error").text( this.__errors.duplicate_file );
                                    $(form).find('.form_input.fileName').addClass('invalid');
                                }

                                else if(response.responseText === "516 Invalid Filename")
                                {
                                    $(form).find('.form_input.fileName').parent().find("span.error").text( this.__errors.invalid_error );
                                    $(form).find('.form_input.fileName').addClass('invalid');
                                }

                                else if(response.responseText === "517 Empty Filename")
                                {
                                    $(form).find('.form_input.fileName').parent().find("span.error").text( this.__errors.empty_error );
                                    $(form).find('.form_input.fileName').addClass('invalid');
                                }

                                else if(response.responseText === "518 Too short Filename")
                                {
                                    $(form).find('.form_input.fileName').parent().find("span.error").text( this.__errors.too_short_error );
                                    $(form).find('.form_input.fileName').addClass('invalid');
                                }

                                else
                                    console.log(response.responseText); debugger
                            },
                        });

                    });

                    let formIsValid = false;
                    
                    $(form).find('.form_input[required="required"]').each( (index, input) => {

                        const inputIsValid = this.checkFormInputValidity( $(input) );

                        if(!inputIsValid)
                        {
                            $(input).parent().find("span.error").html( this.__dataCheckingErrors ).removeClass("hidden");
                            $(input).addClass('invalid');
                        }

                        else
                        {
                            formIsValid = true;
                            $(input).parent().find("span.error").html("").addClass("hidden");
                            $(input).removeClass('invalid');
                        }

                    } );

                    if( formIsValid )
                        $(form).submit();

                } )

            })
        }
        else
        {
            $('.save-media-modif').off('click.onClickOnSaveButtonSendMediaInfo');
        }

        return this;
    }

    onTypingFileNewNameCheckValidity(active)
    {

        if(active)
        {
            $(".edit_media_info .tbody").on("input.onTypingFileNewNameCheckValidity", ".form_input.fileName", e => {

                const input = $(e.currentTarget);
                const nameIsValid = this.checkMediaNameValidity(input.val());

                if(!nameIsValid)
                {
                    //console.log(this.__dataCheckingErrors); debugger
                    input.parent().find("span.error").html( this.__dataCheckingErrors ).removeClass("hidden");
                    input.addClass('invalid');
                }

                else
                {
                    input.parent().find("span.error").html("").addClass("hidden");
                    input.removeClass('invalid');
                }

            })
        }

        else
        {
            $(".edit_media_info .tbody").off("input.onTypingFileNewNameCheckValidity", ".form_input.fileName");
        }

        return this;
    }

    enable()
    {
        super.enable();
        this.onPageLoadAddFilterOnFileInput(true)
            .onClickOnUploadButtonShowModal(true)
            .onClickOnModalCloseButtonsCloseModal(true)
            .onDragNDropFileAddFileList(true)
            .onFileSelectAddFileInList(true)
            .onClickOnStartUploadButtonStartUpload(true)
            .onClickOnNextButtonShowMediaInfosEditContainer(true)
            .onClickOnRemoveFileButtonRemoveFileFromList(true)
            .onClickOnSaveButtonSendMediaInfo(true)
            .onClickOnAddDiffusionDateShowInput(true)
            .onClickOnRemoveDiffusionDateButton(true)
            .onTypingFileNewNameCheckValidity(true)
        ;
    }

    disable()
    {
        super.disable();
        // call function with 'false' for remove events (if event was applied on DOM element by function)
        this.onPageLoadAddFilterOnFileInput(false)
            .onClickOnUploadButtonShowModal(false)
            .onClickOnModalCloseButtonsCloseModal(false)
            .onDragNDropFileAddFileList(false)
            .onFileSelectAddFileInList(false)
            .onClickOnStartUploadButtonStartUpload(false)
            .onClickOnNextButtonShowMediaInfosEditContainer(false)
            .onClickOnRemoveFileButtonRemoveFileFromList(false)
            .onClickOnSaveButtonSendMediaInfo(false)
            .onClickOnAddDiffusionDateShowInput(false)
            .onClickOnRemoveDiffusionDateButton(false)
            .onTypingFileNewNameCheckValidity(false)
        ;
    }

}

export default UploadHandlerTool;