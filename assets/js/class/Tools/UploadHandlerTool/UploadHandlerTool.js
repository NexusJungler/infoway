import Tool from "../Tool"


class UploadHandlerTool extends Tool
{

    constructor()
    {
        super();
        //this.__name = "UploadHandlerTool";
        this.__name = this.constructor.name;

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

            invalid_diffusion_date: "La date de fin de diffusion doit être supérieur à la date de début !",

            invalid_diffusion_start_date: "La date de début de diffusion n'est pas valide !",

            invalid_diffusion_end_date: "La date de fin de diffusion n'est pas valide !",

        };

        this.__dataCheckingErrors = "";

        this.__uploadMediaType = $('.main-media').data('media_displayed');
        if(this.__uploadMediaType === null)
        {
            console.log("Error : UploadHandler::this.__uploadMediaType is not found or is null !"); debugger
        }

        $(".uploadbtn").attr("accept", this.__authorizedFiles[this.__uploadMediaType]);

        this.__total_files_allowed = 50;
        this.__max_file_size = 524288000;
        this.__filesToUpload = [];
        this.__authorized_char_regex = /^[a-zA-Z0-9_.-\s*]*$/;
        this.__uploadAuthorized = false;
        this.__$location = $('.upload_popup');
        this.__$mediasCollection = $('.medias_collection');

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
                this.__$mediasCollection.empty();

            })

        }
        else
        {
            $('.btn-popupclose').off("click.onClickOnModalCloseButtonsCloseModal");
        }

        return this;
    }

    fileMimeTypeIsAccepted(mime_type)
    {
        // search mime_type in authorized extension using upload current tab (image, video, video synchro, ...)
        return this.__authorizedFiles[this.__uploadMediaType].indexOf(mime_type) !== -1;
    }

    fileIsAlreadyUploaded(file)
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
                console.error(errorType.responseText); debugger
                resolve('Error : ' + errorType.responseText);
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
        let fileNameWithoutExtension = fileName.replace( '.' + fileName.split('.').pop(), '' )

        console.log(item); //debugger
        console.log(fileNameWithoutExtension); //debugger

        let index = $(`.upload-info table tbody tr`).length + 1;

        // don't duplicate file in upload list
        if( $(`.upload-info table tbody tr[data-file='${fileName}']`).length < 1 )
        {

            const fileNameContainMultipleDot = this.fileNameContainMultipleDot(fileName);
            const fileExtension = (!fileNameContainMultipleDot) ? '.' + fileName.split('.').pop() : '';
            const fileIsAlreadyUploaded = await this.fileIsAlreadyUploaded(fileName);
            const fileMimeType = item.type;

            console.log(fileNameContainMultipleDot, fileMimeType, fileExtension); //debugger

            // sometime file mimetype was empty
            // in this case, use extension for check if file can be upload
            const fileExtensionIsAccepted = this.fileMimeTypeIsAccepted( (fileMimeType !== "") ? fileMimeType : fileExtension );
            console.log(fileExtensionIsAccepted); //debugger
            const fileSize = item.size;
            let fileIsAccepted = true;

            let newUploadFileItem = $("<tr>", {
                class: (!fileExtensionIsAccepted || fileIsAlreadyUploaded || fileNameContainMultipleDot || (fileNameWithoutExtension.length < 5) ) ? 'invalid_upload_item' : 'valid_upload_item'
            }).attr("data-file", fileName);

            let html = `<td>${fileName}</td>`;

            if(!fileExtensionIsAccepted)
            {
                html += `<td><span class="error">Ce type de fichier n'est pas accepté !</span></td>`;
                fileIsAccepted = false;
            }

            else if(fileIsAlreadyUploaded)
            {
                html += `<td><span class="error">Un fichier portant le même nom a déjà été uploadé !</span></td>`;
                fileIsAccepted = false;
            }

            else if(fileNameWithoutExtension.length < 5)
            {
                html += `<td><span class="error">Le nom de votre fichier doit contenir au moins 5 caractères !</span></td>`;
                fileIsAccepted = false;
            }

            else if(fileSize > this.__max_file_size)
            {
                html += `<td><span class="error">Vous avez selectionné un fichier volumineux. Le temps de chargement pour un tel volume de données peut prendre un temps conséquent en fonction de votre connexion</span></td>`;
                fileIsAccepted = true; // on permet le telechargement des fichiers volimineux
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

    addNewItemInMediaCollection(item)
    {

        let list = this.__$mediasCollection;

        // Try to find the counter of the list or use the length of the list
        let counter = list.data('widget-counter') || list.children().length;

        // grab the prototype template
        let newWidget = list.attr('data-prototype');
        //console.log(newWidget); debugger
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);
        newWidget = newWidget.replace(/__MEDIA_NAME__/g, item.fileName);
        newWidget = newWidget.replace(/__MEDIA_OLD_NAME__/g, item.fileName);
        newWidget = newWidget.replace(/__MEDIA_TYPE__/g, this.__uploadMediaType);
        newWidget = newWidget.replace(/__MEDIA_EXTENSION__/g, item.fileExtension);

        // create a new list element and add it to the list
        let newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
        newElem.attr( 'data-index', counter );

        // put data in input
        //newElem.find('.media_name').val(fileName);

        let now = new Date();
        let month = (now.getMonth() + 1);
        let day = now.getDate();
        let year = now.getFullYear();

        newElem.find(`.media_diffusion_date_start #medias_list_medias_${counter}_diffusionStart_day option[value='${day}']`).attr('selected', true);
        newElem.find(`.media_diffusion_date_start #medias_list_medias_${counter}_diffusionStart_month option[value='${month}']`).attr('selected', true);

        // rebuild year field
        // par defaut symfony construit le select avec un interval : année - 5 < année < année + 5
        newElem.find(`.media_diffusion_date_start #medias_list_medias_${counter}_diffusionStart_year`).html(this.rebuildYearFieldContent(counter, {type: 'start', choice: year}));

        newElem.find(`.media_diffusion_date_end #medias_list_medias_${counter}_diffusionEnd_day option[value='${day}']`).attr('selected', true);
        newElem.find(`.media_diffusion_date_end #medias_list_medias_${counter}_diffusionEnd_month option[value='${month}']`).attr('selected', true);

        // on modifie l'interval pour avoir : année < année < année +30
        newElem.find(`.media_diffusion_date_end #medias_list_medias_${counter}_diffusionEnd_year`).html(this.rebuildYearFieldContent(counter, {type: 'end', choice: year + 30}));

        //console.log(newElem); debugger
        newElem.appendTo(list);
        //console.log(list); debugger

        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);

    }

    onClickOnStartUploadButtonStartUpload(active)
    {

        if (active)
        {
            $(".model-upload-file .start_upload_button").on("click.onClickOnStartUploadButtonStartUpload", e => {

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

                        let formData = new FormData();
                        formData.append('file', fileToUpload.file);

                        const fileExtension = fileToUpload.file.name.split('.').pop();
                        const fileName = fileToUpload.file.name.replace( '.' + fileExtension , '');

                        const uploadState = $(`.modal-upload-download #upload_${fileToUpload.index} .upload_state`);

                        $.ajax({
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
                                        let percent = 0;
                                        let position = event.loaded || event.position;
                                        let total = event.total;
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

                                if( $('.medias-list-to-upload').find(`.media_name[value='${ fileName }']`).length === 0 )
                                    this.addNewItemInMediaCollection( {fileName: fileName, fileExtension: fileExtension} );

                                if(uploadFinished === this.__filesToUpload.length)
                                {
                                    if($(`.modal-upload-download .on_progress`).length === 0)
                                    {
                                        $('.download-file button.upload_abort').fadeOut();
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

                                else if(response.responseText === "518 Too short Filename")
                                    uploadState.html(`${this.__errors.too_short_error} <i class='fas fa-times'></i>`);

                                else
                                    $(`.modal-upload-download #upload_${fileToUpload.index} .upload_state`).html("Téléchargement annulé suite à une erreur interne ! <i class='fas fa-times'></i>");

                                console.log(response.responseText); debugger
                            }
                        });

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

    rebuildYearFieldContent(index, dateData)
    {

        let now = new Date();

        // -5 year, utile ??
        // pour pouvoir laisser le choix de selectionner une date passée (dans le cas de la date de debut de diffusion)
        let startYear = (dateData.type === 'end') ? now.getFullYear() : now.getFullYear() - 5;

        // +30 par defaut
        // pour simuler qu'un média à une date de diffusion "illimité"
        let endYear = startYear + 30;
        let options = '';

        for (let i = startYear; i <= endYear; i++)
        {
            options += `<option value="${ i }" ${ (i === dateData.choice) ? 'selected' : '' }>${ i }</option>`;
        }

        return options;
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

                    $.each( this.__filesToUpload, async (index, fileToUpload) => {

                        const file = fileToUpload.file;
                        const fileExtension = file.name.split('.').pop();
                        const fileName = file.name.split('.')[0];

                        const fileMimeType = file.type;

                        // @TODO: get file size
                        const img = new Image();
                        img.src = window.URL.createObjectURL(file);

                        // @TODO: recupérer les miniatures des medias
                        let preview = `<img class="preview" src="/build/images/not_ready.png" alt="/build/images/not_ready.png" />`;
                        /*

                        const mediaProcessStatus = await this.getMediaProcessStatus(file.name);
                        const mediaMiniaturePath = await this.getMediaMiniaturePath(fileName);
                        console.log(mediaMiniaturePath);


                        if(this.__uploadMediaType === 'image' || mediaProcessStatus !== 'Finished')
                            preview = `<img class="preview" src="${ mediaProcessStatus === 'Finished' ? mediaMiniaturePath : '/build/images/not_ready.png' }" alt="${ mediaProcessStatus === 'Finished' ? mediaMiniaturePath : '/build/images/not_ready.png' }" />`;

                        else
                            preview = `<video class="preview" src="${ mediaMiniaturePath }" > <source src="${ mediaMiniaturePath }" type="${ fileMimeType }" /> </video>`;*/

                        const fileNameIsValid = this.checkMediaNameValidity(fileName);

                        const i = ( this.__$mediasCollection.find('li').length > 0 ) ? this.__$mediasCollection.find('li').length - 1 : 0;

                        //console.log(fileNameIsValid); debugger
                        let html = `<tr data-index="media_${ i }"> 

                                        <td> 
                                            ${ preview }
                                        </td>
                                        
                                        <td>
                                            <span>${fileExtension}</span> <br>
                                            <span>${img.width}*${img.height}px</span> <br>
                                            <span>__RESOLUTION__</span>
                                        </td>
                                        
                                        <td class="media-name-container">
                                            <span class="error ${ !fileNameIsValid ? '' : 'hidden'}"> ${ !fileNameIsValid ? this.__dataCheckingErrors : '' } </span> <br>
                                            <input type="text" name="files[${index}][name]" class="form_input fileName ${ !fileNameIsValid ? 'invalid' : ''}" placeholder="Nom du fichier" value="${fileName}" required="required">
                                        </td>
                                        
                                        <td class="media-diff-date-container">
                                            <button type="button" id="files[${index}]" data-media="files[${index}]" class="addDiffDate">Définir la période de diffusion</button>
                                        </td>
                                        
                                        <td>
                                            <button type="button" class="associate-tag association-btn" data-media="${ fileName }">Associer TAGS</button>
                                            <div class="associated-tags-container"></div>
                                        </td>
                                        
                                        <td>
                                            <button type="button" class="associate-product association-btn" data-media="${ fileName }">Associer produit</button>
                                            <div class="associated-products-container"></div>
                                        </td>
                                        
                                        <td>
                                            <div> 
                                                <label><input type="radio" name="files[${index}][add-price-incruste]" value="yes" required="required">Oui</label>
                                                <label><input type="radio" name="files[${index}][add-price-incruste]" value="no" required="required" checked>Non</label> 
                                            </div>
                                        </td>
                                     
                                    </tr>`;

                        $(html).appendTo( $(".edit_media_info tbody") )

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

            $(".edit_media_info tbody").on("click.onClickOnAddDiffusionDateShowInput", ".addDiffDate", e => {

                $(e.currentTarget).fadeOut();

                let date = new Date();
                date.setMonth( date.getMonth() + 1 );
                const minDate = `${date.getFullYear()}-${(date.getMonth() < 10) ? '0' + date.getMonth() : date.getMonth()}-${date.getDate()}`;

                let html = `<button type="button" class="removeDiffusionDate">Supprimer la periode de diffusion</button> <br>
                            <span class="error"></span> <br>
                            <label for="${e.currentTarget.id}[diffusionStartDate]">Du</label><input type="date" class="form_input diffusionDates start" id="${ e.currentTarget.id }[diffusionStartDate]" name="${ $(e.currentTarget).data('media') }[diffusionStartDate]" required="required"> <br>
                            <label for="${e.currentTarget.id}[diffusionEndDate]">Au</label><input type="date" min="${minDate}" class="form_input diffusionDates end" id="${ e.currentTarget.id }[diffusionEndDate]" name="${ $(e.currentTarget).data('media') }[diffusionEndDate]" required="required">`;

                $(e.currentTarget).parent().html( html );

            })
        }
        else
        {
            $(".edit_media_info tbody").off("click.onClickOnAddDiffusionDateShowInput", ".addDiffDate");
        }

        return this;
    }

    onClickOnRemoveDiffusionDateButton(active)
    {

        if(active)
        {
            $(".edit_media_info tbody").on("click.onClickOnRemoveDiffusionDateButton", ".removeDiffusionDate", e => {

                $(e.currentTarget).parent().html( `<button class="addDiffDate">Définir la période de diffusion</button>` );

            })
        }
        else
        {
            $(".edit_media_info tbody").off("click.onClickOnRemoveDiffusionDateButton", ".removeDiffusionDate");
        }

        return this;
    }

    mediaCharacteristicDataIsValid()
    {

        let formIsValid = false;

        this.__$location.find('.media_list .form_input').each( (index, input) => {

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

        return formIsValid;
    }

    onClickOnSaveButtonSendMediaInfo(active)
    {

        if(active)
        {
            $('.save-media-modif').on('click.onClickOnSaveButtonSendMediaInfo', e => {

                if( this.__$location.find('.media_list tbody form.input.invalid').length === 0 && this.mediaCharacteristicDataIsValid())
                {

                    $.ajax({
                        type: 'post',
                        url: '/edit/media',
                        data: this.__$location.find('form#medias_list_form').serialize(),
                        success: (response) => {
                            console.log(response); debugger
                        },
                        error: (response) => {

                            let error = response.responseJSON;
                            let subject = error.subject;
                            console.log(response);
                            console.log(error);
                            console.log(subject);
                            console.log((error.text === "518 Too short Filename")); debugger

                            if(error.text === "515 Duplicate File")
                            {
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-name-container span.error`).text( this.__errors.duplicate_file );
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-name-container .form_input.fileName`).addClass('invalid');
                            }

                            if(error.text === "516 Invalid Filename")
                            {
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-name-container span.error`).text( this.__errors.invalid_error );
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-name-container .form_input.fileName`).addClass('invalid');
                            }

                            if(error.text === "517 Empty Filename")
                            {
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-name-container span.error`).text( this.__errors.empty_error );
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-name-container .form_input.fileName`).addClass('invalid');
                            }

                            if(error.text === "518 Too short Filename")
                            {
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-name-container span.error`).text( this.__errors.too_short_error );
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-name-container .form_input.fileName`).addClass('invalid');
                            }

                            if(error.text === "519 Invalid diffusion date")
                            {
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-diff-date-container span.error`).text( this.__errors.invalid_diffusion_date );
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-name-container .diffusionDates`).addClass('invalid');
                            }

                            if(error.text === "519.1 Invalid diffusion start date")
                            {
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-diff-date-container span.error`).text( this.__errors.invalid_diffusion_start_date );
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-name-container .diffusionDates.start`).addClass('invalid');
                            }

                            if(error.text === "519.2 Invalid diffusion end date")
                            {
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-diff-date-container span.error`).text( this.__errors.invalid_diffusion_end_date );
                                this.__$location.find(`.media_list tbody tr.${ subject } .media-diff-date-container .diffusionDates.end`).addClass('invalid');
                            }

                            else
                                console.log(error.text); debugger

                        },
                    });

                }

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
            $(".edit_media_info tbody").on("input.onTypingFileNewNameCheckValidity", ".form_input.fileName", e => {

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

                    // update hidden media list
                    let index = input.parents('tr').data('index');
                    this.__$mediasCollection.find(`li[data-index='${ index }'] .media_name`).attr('value', input.val() );

                    input.parents('tr').find('.association-btn').attr('data-media', input.val())

                }

            })
        }

        else
        {
            $(".edit_media_info .tbody").off("input.onTypingFileNewNameCheckValidity", ".form_input.fileName");
        }

        return this;
    }

    onDiffusionDateChangeUpdateDateInCollection(active)
    {

        if(active)
        {
            $(".edit_media_info tbody").on("change.onDiffusionDateChangeUpdateDateInCollection", ".diffusionDates", e => {

                //console.log( $(e.currentTarget).val() ); //debugger

                const explode = $(e.currentTarget).val().split('-');
                const day = explode[2].replace(/^0/,'');
                const month = explode[1].replace(/^0/,'');
                const year = explode[0];

                let index = $(e.currentTarget).parents('tr').data('index');
                //console.log(index); //debugger

                const collectionItem = this.__$mediasCollection.find(`li[data-index='${ index }'] `);

                if( $(e.currentTarget).hasClass('start') )
                {
                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index.replace('media_', '') }_diffusionStart_day option:selected`).attr('selected', false);
                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index.replace('media_', '') }_diffusionStart_day option[value='${day}']`).attr('selected', true);

                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index.replace('media_', '') }_diffusionStart_month option:selected`).attr('selected', false);
                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index.replace('media_', '') }_diffusionStart_month option[value='${month}']`).attr('selected', true);

                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index.replace('media_', '') }_diffusionStart_year option:selected`).attr('selected', false);
                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index.replace('media_', '') }_diffusionStart_year option[value='${year}']`).attr('selected', true);
                }
                else if( $(e.currentTarget).hasClass('end') )
                {
                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index.replace('media_', '') }_diffusionEnd_day option:selected`).attr('selected', false);
                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index.replace('media_', '') }_diffusionEnd_day option[value='${day}']`).attr('selected', true);

                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index.replace('media_', '') }_diffusionEnd_month option:selected`).attr('selected', false);
                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index.replace('media_', '') }_diffusionEnd_month option[value='${month}']`).attr('selected', true);

                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index.replace('media_', '') }_diffusionEnd_year option:selected`).attr('selected', false);
                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index.replace('media_', '') }_diffusionEnd_year option[value='${year}']`).attr('selected', true);
                }

            })
        }
        else
        {
            $(".edit_media_info tbody").off("change.onDiffusionDateChangeUpdateDateInCollection", ".diffusionDates");
        }

        return this;
    }

    enable()
    {
        super.enable();
        this.onClickOnUploadButtonShowModal(true)
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
            .onDiffusionDateChangeUpdateDateInCollection(true)
        ;
    }

    disable()
    {
        super.disable();
        // call function with 'false' for remove events (if event was applied on DOM element by function)
        this.onClickOnUploadButtonShowModal(false)
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
            .onDiffusionDateChangeUpdateDateInCollection(false)
        ;
    }

}

export default UploadHandlerTool;