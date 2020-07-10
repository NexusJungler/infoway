import Tool from "../../Tool"
import SubTool from "../../SubTool";


class UploadHandlerTool extends SubTool
{

    constructor()
    {
        super();

        this.__name = this.constructor.name;

        this.__$container= $('.popup_upload_container');
        this.__$location = $('.popup_upload');
        this.__$fileToUploadList = $('.file_to_upload_list');
        this.__$fileToCharacterisationList = $('.file_to_characterisation_list');

        // W3C authors recommend to specify both MIME-types and corresponding extensions in input type file "accept" attribute
        // @see : https://html.spec.whatwg.org/multipage/input.html#attr-input-accept
        this.__authorizedFiles = {

            medias: [
                'image/jpg', 'image/jpeg', 'image/png', 'image/bmp', 'image/gif', 'image/x-windows-bmp', 'image/pjpeg', 'image/svg+xml',
                '.jpg', '.jpeg', '.png', '.bmp', '.gif', '.svg', 'video/*', 'video/mp4', 'video/avi', 'video/x-matroska', 'video/3gpp', 'video/quicktime',
                '.mp4', '.avi', '.3gp'
            ],

            /*element_graphic: [

            ],*/

        };

        this.__errors = {

            bad_extension: "Ce type de fichier n'est pas accepté",

            bad_resolution: "Cette résolution n'est pas accepté",

            corrupt_file: "Fichier corrompu",

            duplicate_file: "Ce nom est déjà utilisé !",

            invalid_error: "Ce champ contient des caractères non autorisés !",

            empty_error: "Ce champ ne peut pas être vide",

            too_short_error: "Ce champ doit contenir au moins 5 caractères !",

            uploaded_file_not_found_error: "Le fichier n'existe plus sur le serveur ou a été déplacé !",

            invalid_diffusion_date: "La date de fin de diffusion doit être supérieur à la date de début !",

            invalid_diffusion_start_date: "La date de début de diffusion n'est pas valide !",

            invalid_diffusion_end_date: "La date de fin de diffusion n'est pas valide !",

            encode_error: "Erreur durant l'encodage du fichier",

            bad_ratio: "Ce fichier ne possède pas un ratio valide",

        };

        this.__dataCheckingErrors = "";

        this.__uploadMediaType = $('.main-media').data('media_displayed');
        if(this.__uploadMediaType === null || typeof this.__uploadMediaType === 'undefined' || this.__uploadMediaType === 'undefined')
        {
            console.error("Error : cannot found data-media_displayed on element : .main-media !"); //debugger
        }

        let type = this.__uploadMediaType;
        $(".default_upload_input").attr("accept", this.__authorizedFiles[ 'medias' ]);

        this.__total_files_allowed = 50;
        this.__max_file_size = 524288000;
        this.__filesToUpload = [];
        this.__mediaInfos = [];
        this.__authorized_char_regex = /^[a-zA-Z0-9_.-\s*]*$/;
        this.__uploadAuthorized = false;
        this.__$mediasCollection = $('.medias_collection');

        this.__availableAssociationItems = [];

        this.getAllAvailableAssociationItems();
    }

    getAllAvailableAssociationItems()
    {

        $('.popup_associate_product .products_choice_list tr').each( (index,tr) => {

            this.__availableAssociationItems.push( { id: $(tr).data('product_id'), name: $(tr).find('.product_name').text(), type: 'products' } );

        } );

        $('.popup_associate_tag .tags_list tr').each( (index,tr) => {

            this.__availableAssociationItems.push( { id: $(tr).data('tag_id'), name: $(tr).find('.tag_name').text(), type: 'tags' } );

        } );

        return this;

    }

    fileMimeTypeIsAccepted(mime_type)
    {
        // search mime_type in authorized extension using upload current tab (image, video, video synchro, ...)
        //return this.__authorizedFiles[this.__uploadMediaType].indexOf(mime_type) !== -1;
        return this.__authorizedFiles['medias'].indexOf(mime_type) !== -1;
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
                    console.error(errorType.responseText); //debugger
                    resolve(true);
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

    fileIsAlreadyInUploadList(fileName)
    {

        this.__$location.find(`.file_to_upload_list tr`).each( ( tr ) => {

            if( $(tr).find('td').text() === fileName )
                return true;

        } )

        return false;

    }

    async addItemInUploadList(item)
    {

        let fileName = item.name;
        let fileNameWithoutExtension = fileName.replace( '.' + fileName.split('.').pop(), '' )

        console.log(item); //debugger
        console.log(fileNameWithoutExtension); //debugger

        // don't duplicate file in upload list
        if(!this.fileIsAlreadyInUploadList(fileName))
        {

            const fileNameContainMultipleDot = this.fileNameContainMultipleDot(fileName);
            const fileExtension = (!fileNameContainMultipleDot) ? '.' + fileName.split('.').pop() : '';
            const fileIsAlreadyUploaded = await this.fileIsAlreadyUploaded(fileName);
            const fileMimeType = item.type;

            // sometime file mimetype was empty
            // in this case, use extension for check if file can be upload
            const fileExtensionIsAccepted = this.fileMimeTypeIsAccepted( (fileMimeType !== "") ? fileMimeType : fileExtension );
            //console.log(fileExtensionIsAccepted); //debugger
            const fileSize = item.size;
            let fileIsAccepted = true;

            let newUploadFileItem = $("<tr>", {
                class: (!fileExtensionIsAccepted || fileIsAlreadyUploaded || fileNameContainMultipleDot ) ? 'invalid_upload_item' : 'valid_upload_item'
            });

            let newUploadItem = `<td class="file_name">${fileName}</td>`;

            if(!fileExtensionIsAccepted)
            {
                newUploadItem += `<td><span class="error">Ce type de fichier n'est pas accepté !</span></td>`;
                fileIsAccepted = false;
            }
            else if(fileIsAlreadyUploaded)
            {
                newUploadItem += `<td><span class="error">Un fichier portant le même nom a déjà été uploadé !</span></td>`;
                fileIsAccepted = false;
            }
            else if(fileSize > this.__max_file_size)
            {
                newUploadItem += `<td><span class="error">Vous avez selectionné un fichier volumineux. Le temps de chargement pour un tel volume de données peut prendre un temps conséquent en fonction de votre connexion</span></td>`;
                fileIsAccepted = true; // on permet le telechargement des fichiers volimineux
            }

            else if(!this.__authorized_char_regex.test(fileName))
            {
                newUploadItem += `<td><span class="error">Le nom de ce fichier comporte un ou plusieurs caractère(s) non autorisé !</span></td>`;
                fileIsAccepted = false;
            }

            else if(fileNameContainMultipleDot)
            {
                newUploadItem += `<td><span class="error">Le nom de ce fichier comporte plusieurs extensions !</span></td>`;
                fileIsAccepted = false;
            }

            else
                newUploadItem += `<td></td>`;

            newUploadItem += `<td><i class="${ (!fileIsAccepted) ? 'fas fa-times' : 'fas fa-check' }"></i></td>
                              <td><i data-target="${fileName}" class="fas fa-trash-alt remove_file_from_upload_list"></i></td>`;


            newUploadFileItem.html(newUploadItem);

            newUploadFileItem.appendTo( this.__$location.find(`.file_to_upload_list`) );

            if(fileIsAccepted)
            {
                this.__filesToUpload.push( {index: this.__filesToUpload.length, name: fileName, file: item} );
                console.log("new element added in upload list")
                console.table(this.__filesToUpload); //debugger
            }

            if( this.__$location.find(`.file_to_upload_list tr.valid_upload_item`).length > this.__total_files_allowed)
            {
                this.__uploadAuthorized = false;

                this.__$location.find('.pre_upload_error').text(`Vous avez sélectionné ${this.__$location.find(`.file_to_upload_list tr.valid_upload_item`)} fichiers, le maximum autorisé est de ${this.__total_files_allowed} !"`);
                //$('.start-upload-container .error_over-max-file').text(`Vous avez sélectionné ${$(".upload-info table tbody tr.valid_upload_item").length} fichiers, le maximum autorisé est de ${this.__total_files_allowed} !"`).fadeIn();
                this.__$location.find('.start_upload_btn').attr('disabled', true);
            }

            else if(this.__$location.find(`.file_to_upload_list tr.valid_upload_item`).length < 1)
            {
                this.__uploadAuthorized = false;
                this.__$location.find('.start_upload_btn').attr('disabled', true);
            }

            else
            {
                this.__uploadAuthorized = true;
                this.__$location.find('.pre_upload_error').empty();
                this.__$location.find('.start_upload_btn').removeAttr('disabled');
            }

        }

    }

    onClickOnUploadButtonShowModal(active)
    {

        if(active)
        {
            $(".btn-upload").on("click.onClickOnUploadButtonShowModal", e => {

                this.__$container.addClass('is_open');

            })
        }
        else
        {
            $(".btn-upload").off("click.onClickOnUploadButtonShowModal");
        }

        return this;
    }

    closeModal()
    {
        this.__$container.removeClass('is_open');

        this.__$location.find('.upload_step_title').text("Préparation de l'upload");

        this.__filesToUpload = [];
        this.__$mediasCollection.empty();
        this.__uploadAuthorized = false;
        this.__$fileToUploadList.empty();
        this.__$fileToCharacterisationList.empty();
        this.showStep(1);
    }

    onClickOnCloseButtonCloseUploadPopup(active)
    {

        if(active)
        {
            this.__$location.on("click.onClickOnCloseButtonCloseUploadPopup", '.close_modal_button',e => {

                return this.closeModal();

                if($('.file_progress_bar_container progress.on_upload').length > 0)
                {
                    // if user choice "yes"
                    if(confirm("Certains de vos téléchargement ne sont pas terminés ! En fermant cet fênetre, vos devrez recommencer les téléchargement en cours. Etes-vous sûr de vouloir continuer ?"))
                    {
                        // @TODO: abort download or send ajax to server for delete media which is not finish

                        this.closeModal();
                    }
                }

                else if($('.edit_media_info .unregistered').length > 0)
                {
                    // if user choice "yes"
                    if(confirm("Vous n'avez pas enregistrés toutes vos modifications ! En fermant cet fênetre, vos perderez vos modifications non enregistrés. Etes-vous sûr de vouloir continuer ?"))
                    {
                        this.closeModal();
                    }
                }

                else
                    this.closeModal();

            })

        }
        else
        {
            this.__$location.on("click.onClickOnCloseButtonCloseUploadPopup", '.close_modal_button');
        }

        return this;
    }

    handleFileDragNDrop(active)
    {

        if(active)
        {

            this.__$location.find('.drag_n_drop_container')

                            .on("dragover", e => {

                                e.preventDefault();

                            })

                            .on('dragenter', e => {
                                e.preventDefault();
                                $(e.currentTarget).addClass("on_dragenter");

                            })

                            .on("dragleave", e => {
                                e.preventDefault();
                                $(e.currentTarget).removeClass("on_dragenter");

                            });

        }
        else
        {

            this.__$location.find('.drag_n_drop_container')

                            .off("dragover")

                            .off('dragenter')

                            .off("dragleave");

        }

        return this;

    }

    onFileDropAddFileInUploadList(active)
    {
        if(active)
        {

            this.__$location.find('.drag_n_drop_container').on("drop", e => {

                e.preventDefault();
                $(e.currentTarget).removeClass("on_dragenter");
                console.log(`${e.originalEvent.dataTransfer.files.length} File(s) dropped !`);
                let droppedFiles = e.originalEvent.dataTransfer.files;

                console.table(droppedFiles); //debugger

                droppedFiles.forEach( (droppedFile) => {

                    this.addItemInUploadList(droppedFile);

                } )

            });

        }
        else
        {
            this.__$location.find('.drag_n_drop_container').off("drop");
        }

        return this;
    }

    onClickOnCustomFileButtonActiveDefaultFileInput(active)
    {
        if(active)
        {
            this.__$location.find('.custom_upload_input').on('click.onClickOnCustomFileButtonActiveDefaultFileInput', e => {

                this.__$location.find('.default_upload_input').click();

            })

            this.onFileSelectAddFileInList(true);
        }
        else
        {
            this.__$location.find('.custom_upload_input').off('click.onClickOnCustomFileButtonActiveDefaultFileInput');
            this.onFileSelectAddFileInList(false);
        }

        return this;
    }

    onFileSelectAddFileInList(active)
    {

        if(active)
        {

            this.__$location.find('.default_upload_input').on('change.onFileSelectAddFileInList', e => {

                const uploadButton = $(e.currentTarget);
                let filesSelected = uploadButton[0].files;

                filesSelected.forEach( (file) => {

                    console.table(file); //debugger
                    this.addItemInUploadList(file);

                } );

                uploadButton.val("");

            })

        }
        else
        {
            this.__$location.find('.default_upload_input').off('change.onFileSelectAddFileInList');
        }

        return this;
    }

    onClickOnRemoveFileButtonRemoveFileFromUploadList(active)
    {

        if(active)
        {
            this.__$fileToUploadList.on("click.onClickOnRemoveFileButtonRemoveFileFromUploadList", ".remove_file_from_upload_list", e => {

                const fileName = $(e.currentTarget).parents('tr').find('.file_name').text();

                const index = this.__filesToUpload.findIndex( fileToUpload => fileToUpload.name === fileName );

                $(e.currentTarget).parents('tr').remove();

                if(index !== -1)
                {
                    this.__filesToUpload.splice( index, 1 );
                    console.log("upload element removed !");
                    console.log(this.__filesToUpload.length);

                    if( this.__$fileToUploadList.find('tr').length < 1 )
                        this.__$location.find('.start_upload_btn').attr('disabled', true);
                }

            })

        }
        else
        {
            this.__$fileToUploadList.off("click.onClickOnRemoveFileButtonRemoveFileFromUploadList", ".remove_file_from_upload_list");
        }

        return this;
    }

    showStep(stepNumber)
    {

        if(this.__$location.find(`.step_${ stepNumber }`).length > 0)
        {

            let html = "";

            if(stepNumber === 1)
            {

                this.__$location.find('.upload_step_title').text("Préparation de l'upload");

                html = `<div class="actions_buttons_container">
                        <button class="btn close_modal_button cancel" type="button">Annuler</button>
                    </div>

                    <div class="actions_buttons_container">
                        <button class="btn start_upload_btn" type="button" ${ (this.__$fileToUploadList.find('tr').length === 0) ? 'disabled' : '' }><i class="fas fa-arrow-right"></i> Démarrer l'upload</button>
                    </div>`;

            }
            else if(stepNumber === 2)
            {

                this.__$location.find('.upload_step_title').text("Médias à caractériser");

                html = `<div class="actions_buttons_container">
                        <button class="btn close_modal_button cancel" type="button">Annuler</button>
                    </div>
                    
                    <div class="actions_buttons_container">
                        <button class="btn show_prev_step" type="button"><i class="fas fa-arrow-left"></i>Précedent</button>
                    </div>

                    <div class="actions_buttons_container">
                        <button class="btn save_edits_button" type="button">Enregistrer</button>
                    </div>`;

            }
            else
            {

                this.__$location.find('.upload_step_title').text("Médias prêts");

                html = `<div class="actions_buttons_container">
                        <button class="btn show_prev_step" type="button"><i class="fas fa-arrow-left"></i>Précedent</button>
                    </div>
                    
                    <div class="actions_buttons_container">
                        <button class="btn" type="button"><i class="fas fa-arrow-left"></i>Remplacer un média</button>
                    </div>
                    
                    <div class="actions_buttons_container">
                        <button class="btn" type="button"><i class="far fa-calendar"></i>Programmer</button>
                    </div>

                    <div class="actions_buttons_container">
                        <button class="btn" type="button" disabled><i class="icon icon-picto-mediathque"></i>Médiathèque</button>
                    </div>`;

            }

            this.__$location.find('.step.current_step').removeClass('current_step');

            this.__$location.find('.popup_footer').html(html);
            this.__$location.find(`.step_${ stepNumber }`).addClass('current_step');

        }

        return this;
    }

    buildUploadList()
    {

        $.each( this.__filesToUpload, (index, fileToUpload) => {

            if( this.__$location.find(`.file_to_characterisation_list #upload_${index}`).length === 0 )
            {

                let html = '';

                if( this.__uploadMediaType === 'element_graphic' )
                {
                    html = `<tr data-index="${ index }" id="upload_${index}" class="unregistered">
                                <td class="file-name-container uploaded-file-name-col">
                                    <p><i class="fas fa-trash-alt cancel-upload" style="display: none"></i>${fileToUpload.name}</p>
                                </td>
                                
                                <td class="file_progress_bar_container">
                                    <progress class="progress_bar" id="progress_${index}" max="100" value="0"></progress>
                                </td>
                                
                                <td>
                                    <span class="upload_state"></span>
                                </td>
                                
                                <td>
                                    
                                </td>
                                
                            </tr>`;
                }
                else
                {

                    html = `<tr data-index="${ index }" id="upload_${index}" class="unregistered">
                                <td>
                                    <p>${fileToUpload.name}</p>
                                </td>
                                
                                <td class="file_progress_bar_container">
                                    <progress class="progress_bar" id="progress_${index}" max="100" value="0"></progress>
                                </td>
                                
                                <td>
                                    <span class="upload_state"></span>
                                </td>
                                
                                <td>
                                    
                                </td>
                                
                                <td>
                                    
                                </td>
                                
                                <td>
                                    
                                </td>
                                        
                                <td>
                                    
                                </td>        
                                        
                                <td>
                                    
                                </td>
                                        
                                <td>
                                    
                                </td>
                                        
                                <td>
                                    
                                </td>
                                
                            </tr>`;

                }

                $(html).appendTo( this.__$location.find('.file_to_characterisation_list') );

            }



        } )

        return this;

    }

    onClickOnStartUploadButton(active)
    {

        if (active)
        {
            this.__$location.on("click.onClickOnStartUploadButton", '.start_upload_btn', e => {

                if(this.__uploadAuthorized)
                {

                    this.buildUploadList()
                        .showStep(2)
                    ;

                    let uploadFinished = 0;

                    //this.__$fileToUploadList.empty();

                    //console.log(this.__filesToUpload.length); debugger
                    // for each file in upload list
                    $.each( this.__filesToUpload, (index, fileToUpload) => {

                        //console.log("ajax"); debugger

                        if( !$(`#upload_${fileToUpload.index}`).hasClass('upload_finished') )
                        {

                            const uploadStateIndicator = this.__$fileToCharacterisationList.find(`#upload_${fileToUpload.index} .upload_state`);
                            uploadStateIndicator.html("Téléchargement en cours ...");

                            let formData = new FormData();
                            formData.append('file', fileToUpload.file);
                            formData.append('media_type', this.__uploadMediaType);

                            const fileExtension = fileToUpload.file.name.split('.').pop();
                            const fileName = fileToUpload.file.name.replace( '.' + fileExtension , '');


                            $.ajax({
                                url: "/upload/media",
                                type: "POST",
                                data: formData,
                                contentType: false,
                                cache: false,
                                processData: false,
                                xhr: () => {
                                    //upload Progress
                                    let xhr = jQuery.ajaxSettings.xhr();
                                    if (xhr.upload) {
                                        xhr.upload.addEventListener('progress',  (event) => {
                                            let percent = 0;
                                            let position = event.loaded || event.position;
                                            let total = event.total;
                                            if (event.lengthComputable) {
                                                percent = Math.ceil(position / total * 100);
                                            }

                                            //update progressbar
                                            this.__$fileToCharacterisationList.find(`#upload_${fileToUpload.index} progress`).addClass("on_upload").attr("value", percent);

                                            uploadStateIndicator.html(`Téléchargement en cours ... (${percent}%)`);
                                            //jQuery('#progress' + (index + 1) + ' .progress-bar').css("left", +percent + "%");

                                            if(percent === 100 && this.__uploadMediaType === 'video')
                                                uploadStateIndicator.html("Encodage en cours ...");

                                            //jQuery('#progress' + (index + 1) + ' .status').text(percent + "%");
                                        }, true);
                                    }

                                    return xhr;
                                },
                                mimeType: "multipart/form-data",
                            })

                                .done( (response) => {

                                    response = JSON.parse(response);

                                    if(typeof response.error === "undefined")
                                    {

                                        if(response.fileType === 'image')
                                        {

                                            /*let mediaInfos = {
                                                id: response.id,
                                                customer: response.customer,
                                                index: fileToUpload.index,
                                                fileType: response.fileType,
                                                mediaType: response.mediaType,
                                                fileName: response.fileName,
                                                fileNameWithoutExtension: response.fileNameWithoutExtension,
                                                extension: response.extension,
                                                height: response.height,
                                                width: response.width,
                                                dpi: response.dpi,
                                                miniatureExist: response.miniatureExist,
                                                //highestFormat: response.highestFormat,
                                            };*/

                                            uploadStateIndicator.html("Téléchargement terminé !");

                                            $(`#upload_${fileToUpload.index} .cancel-upload`).fadeIn();
                                            $(`#upload_${fileToUpload.index} progress`).removeClass("on_upload");

                                            uploadFinished++;

                                            if( $(`#upload_${fileToUpload.index} .file_progress_bar_container i`).length === 0 )
                                                $('<i>', { class: 'fas fa-check' }).appendTo( $(`#upload_${fileToUpload.index} .file_progress_bar_container`) )

                                            // new item
                                            //this.addNewItemInMediaCollection( { id: response.id, fileName: fileName, extension: fileExtension, type: mediaInfos.type } );

                                            this.showMediaInfoForEdit(response ,fileToUpload.index);

                                            $('.edit-btn-container').fadeIn();

                                        }
                                        else
                                        {

                                            uploadStateIndicator.html("Encodage en cours ...");

                                            // check status every 10sec
                                            /*let videoEncodingResult = await this.checkVideoEncodingStatus(response.id, fileToUpload.index);
                                            while (videoEncodingResult.status !== 'Finished')
                                            {
                                                // wait 10s before checking again
                                                //this.sleep(10000);


                                            }*/

                                            let videoEncodingResult = {};

                                            let intervalId = setInterval( async() => {

                                                videoEncodingResult = await this.checkVideoEncodingStatus(response.id, fileToUpload.index);
                                                if( videoEncodingResult.status === 'Finished' )
                                                {
                                                    clearInterval(intervalId);

                                                    $(`#upload_${fileToUpload.index} progress`).removeClass("on_upload");
                                                    uploadFinished++;

                                                    //console.log(videoEncodingResult); //debugger

                                                    $(`#upload_${fileToUpload.index} .cancel-upload`).fadeIn();

                                                    if( $(`#upload_${fileToUpload.index} .file_progress_bar_container i`).length === 0 )
                                                        $('<i>', { class: (typeof videoEncodingResult.error !== "undefined") ? 'fas fa-times' : 'fas fa-check' }).appendTo( $(`#upload_${fileToUpload.index} .file_progress_bar_container`) )

                                                    if(typeof videoEncodingResult.error === "undefined")
                                                    {

                                                        $(`#upload_${fileToUpload.index}`).removeClass("valid_download");

                                                        //this.addNewItemInMediaCollection( {id: videoEncodingResult.id, fileName: fileName, extension: videoEncodingResult.extension, type: videoEncodingResult.type} );

                                                        /*let videoInfos = {
                                                            id: videoEncodingResult.id,
                                                            customer: videoEncodingResult.customer,
                                                            index: fileToUpload.index,
                                                            fileType: videoEncodingResult.type,
                                                            fileName: videoEncodingResult.fileName,
                                                            fileNameWithoutExtension: videoEncodingResult.fileNameWithoutExtension,
                                                            miniatureExist: videoEncodingResult.miniatureExist,
                                                            name: videoEncodingResult.name,
                                                            extension: videoEncodingResult.extension,
                                                            height: videoEncodingResult.height,
                                                            width: videoEncodingResult.width,
                                                            dpi: videoEncodingResult.dpi,
                                                            codec: videoEncodingResult.codec,
                                                            mimeType: videoEncodingResult.mimeType,
                                                            //highestFormat: videoEncodingResult.highestFormat,
                                                        };*/

                                                        this.showMediaInfoForEdit(videoEncodingResult, fileToUpload.index);

                                                    }
                                                    else
                                                    {
                                                        $(`#upload_${fileToUpload.index}`).addClass('invalid-download');
                                                        uploadStateIndicator.html(`${this.__errors.encode_error} : ${ videoEncodingResult.error }`);
                                                    }

                                                }


                                            }, 10000 )

                                        }

                                        if($('.media_list tbody tr.valid-download').length > 0)
                                            $('.edit_media_info .action-btn-container').fadeIn();

                                    }
                                    else
                                    {

                                        $(`#upload_${fileToUpload.index} .cancel-upload`).fadeIn();

                                        //$(".modal-upload-download .show_media_edit_container").fadeOut();

                                        //$(`.modal-upload-download #upload_${fileToUpload.index} progress`).css({ 'color': 'red' });

                                        $(`#upload_${fileToUpload.index} progress`).removeClass("on_upload");
                                        $(`#upload_${fileToUpload.index}`).removeClass("unregistered").addClass('invalid_upload');
                                        $('<i>', { class: 'fas fa-times' }).appendTo( $(`#upload_${fileToUpload.index} .file_progress_bar_container`) );

                                        switch (response.error)
                                        {

                                            case "512 Bad Extension":
                                                uploadStateIndicator.html(`${this.__errors.bad_extension}`);
                                                break;

                                            case "513 Bad Resolution":
                                                uploadStateIndicator.html(`${this.__errors.bad_resolution}`);
                                                break;

                                            case "514 Corrupt File":
                                                uploadStateIndicator.html(`${this.__errors.corrupt_file}`);
                                                break;

                                            case "515 Duplicate File":
                                                uploadStateIndicator.html(`${this.__errors.duplicate_file}`);
                                                break;

                                            case "516 Invalid Filename":
                                                // <i class='fas fa-times'></i>
                                                uploadStateIndicator.html(`${this.__errors.invalid_error}`);
                                                break;

                                            case "517 Empty Filename":
                                                uploadStateIndicator.html(`${this.__errors.empty_error}`);
                                                break;

                                            case "518 Too short Filename":
                                                uploadStateIndicator.html(`${this.__errors.too_short_error}`);
                                                break;

                                            case "521 Bad ratio":
                                                uploadStateIndicator.html(`${this.__errors.bad_ratio}`);
                                                break;

                                            default:
                                                $(`#upload_${fileToUpload.index} .upload_state`).html("Téléchargement annulé suite à une erreur interne !");
                                                console.log(response.error); debugger

                                        }

                                    }

                                    /*console.table( this.__filesToUpload ); debugger
                                    this.__filesToUpload.splice(index , 1);
                                    console.table( this.__filesToUpload ); debugger*/

                                    $(`#upload_${fileToUpload.index}`).addClass("upload_finished");

                                } )

                            ;

                        }

                    } )

                }

            })
        }
        else
        {
            this.__$location.off("click.onClickOnStartUploadButton", '.start_upload_btn');
        }

        return this;
    }

    async checkVideoEncodingStatus(id)
    {

        // check status every 10sec
        return new Promise( (resolve, reject) => {

            $.ajax({
                url: "/get/video/encoding/status",
                type: "POST",
                data: {id: id},
/*                success: (response) => {
                    //console.log(response);
                    resolve(response);

                },
                error: (response, status, error) => {

                    //console.log(resolve); //debugger
                    reject(response);

                }*/
            })

                .done( (response) => {

                    resolve(response);

                } )

        } );

    }

    showElementGraphicInfos(elementGraphicInfos, preview)
    {

        return `<td> <p><i class="fas fa-trash-alt cancel-upload" aria-hidden="true"></i> ${ elementGraphicInfos.fileName }</p> </td>
                     <td>
                        <progress class="progress_bar" id="progress_${ elementGraphicInfos.index }" max="100" value="100"></progress>
                        <i class="fas fa-check" aria-hidden="true"></i>
                     </td>
                     <td> 
                        ${ preview } 
                        <i class="fas fa-expand-alt show_expanded_miniature" data-media_id="${ elementGraphicInfos.id }" aria-hidden="true"></i>
                     </td>
                     <td> 
                        <input type="hidden" class="media_id" name="medias_list[medias][${elementGraphicInfos.index}][id]" value="${ elementGraphicInfos.id }">
                        <input type="hidden" name="medias_list[medias][${elementGraphicInfos.index}][id]" value="${ elementGraphicInfos.id }"> 
                        <span class="error hidden"></span>
                        <input type="text" name="medias_list[medias][${elementGraphicInfos.index}][name]" class="form_input file_name" placeholder="Nom du media" value="${ elementGraphicInfos.fileNameWithoutExtension }" required>
                 </td>`;

    }
    
    showMediaInfos(mediaInfos, preview)
    {

        let now = new Date();
        let month = (now.getMonth() + 1);
        month = (month < 10) ? '0' + month : month;
        let day = (now.getDate() < 10 ) ? '0' + now.getDate() : now.getDate();
        let year = now.getFullYear();

        return `<td> <p><i class="fas fa-trash-alt cancel-upload" aria-hidden="true"></i> ${ mediaInfos.filename }</p> </td>
                <td>
                    <progress class="progress_bar" id="progress_${ mediaInfos.index }" max="100" value="100"></progress>
                    <i class="fas fa-check" aria-hidden="true"></i>
                </td>
                <td> 
                    ${ preview } 
                    <i class="fas fa-expand-alt show_expanded_miniature" data-media_id="${ mediaInfos.id }" aria-hidden="true"></i>
                </td>
                <td>
                    <span>${mediaInfos.extension}</span> <br> <span>${mediaInfos.width} * ${mediaInfos.height} px</span> <br> <span>${ (mediaInfos.fileType === 'image') ? mediaInfos.dpi + ' dpi' :  mediaInfos.codec}</span>
                </td>
                <td class="media_name_container"> 
                    <input type="hidden" class="media_id" name="medias_list[medias][${mediaInfos.index}][id]" value="${ mediaInfos.id }"> 
                    <span class="error hidden"></span> <br>
                    <input type="text" name="medias_list[medias][${mediaInfos.index}][name]" class="form_input media_name" placeholder="Nom du media" value="${mediaInfos.fileNameWithoutExtension}" required> </td>
                <td class="media_diff_date_container"> 
                    <div class="diff_start_container">
                        <span class="error hidden"></span> <br> 
                        <label for="media_${mediaInfos.index}_diff_start">Du</label>
                        <input type="date" name="medias_list[medias][${mediaInfos.index}][diffusionStart]" id="media_${mediaInfos.index}_diff_start" class="diffusion_dates start form_input" value="${year}-${month}-${day}">
                   </div>

                   <div class="diff_end_container">
                        <span class="error hidden"></span> <br> 
                        <label for="media_${mediaInfos.index}_diff_end">Au</label>
                        <input type="date" name="medias_list[medias][${mediaInfos.index}][diffusionEnd]" id="media_${mediaInfos.index}_diff_end" class="diffusion_dates end form_input" min="${year}-${month}-${day}" value="${year + 10}-${month}-${day}">
                   </div>
                </td>
                <td class="associated_criterions_container">
                
                </td>
                <td class="tags_affectation_container"> 
                    <button type="button" class="btn tag_association_btn association_btn">Associer tags</button>
                    <div class="associated_tags_container">
                        ${ this.buildAssociationInputsHtml('tags', mediaInfos.index) }
                    </div> 
                </td>
                <td class="products_affectation_container"> 
                    <button type="button" class="btn product_association_btn association_btn">Associer produits</button>
                    <div class="associated_products_container">
                        ${ this.buildAssociationInputsHtml('products', mediaInfos.index) }
                    </div> 
                </td>
                <td> 
                    <label class=""><input type="radio" name="medias_list[medias][${mediaInfos.index}][containIncrustations]" class="form_input media_contain_incruste" value="1">Oui</label> 
                    <label class=""><input type="radio" name="medias_list[medias][${mediaInfos.index}][containIncrustations]" class="form_input choice_media_contain_incruste" value="0" checked>Non</label>
                </td>`;
    }

    buildAssociationInputsHtml(associationItem, index)
    {
        let inputs = '';
        let counter = 0;

        this.__availableAssociationItems.forEach( (item) => {

            if(item.type === associationItem)
            {

                inputs += `<input type="checkbox" id="medias_list_medias_${ index }_${ item.type }_${ counter }" name=medias_list[medias][${index}][${ item.type }][]" value="${item.id}"> 
                           <label for="medias_list_medias_${ index }_${ item.type }_${ counter }">${ item.name }</label>`;
                counter++;

            }

        } );

        return inputs;

    }

    showMediaInfoForEdit(mediaInfos, index)
    {

        mediaInfos.index = index;

        console.table(mediaInfos); //debugger

        //const index = this.__$fileToCharacterisationList.children('tr').length;

        this.__mediaInfos.push(mediaInfos);

        $(`#upload_${mediaInfos.index} .choice_media`).attr('data-media', mediaInfos.index);

        // show miniatures
        $(`#upload_${mediaInfos.index} .preview_container`).empty();

        let preview = '';

        if(mediaInfos.miniatureExist)
        {

            if(mediaInfos.fileType === 'image')
            {
                //mediaInfos.mediaType === 'diff'
                if( mediaInfos.mediaType === 'diff' )
                    preview = `<img class="preview" src="/miniatures/${mediaInfos.customer}/images/low/${mediaInfos.id}.png" alt="/miniatures/${mediaInfos.customer}/image/low/${mediaInfos.id}.png" />`;

                else if( mediaInfos.mediaType === 'elmt' )
                    preview = `<img class="preview" style="height: 50px;" src="/miniatures/${mediaInfos.customer}/piece/${mediaInfos.id}.png" alt="/miniatures/${mediaInfos.customer}/piece/${mediaInfos.id}.png" />`;

            }


            else
                preview = `<video class="preview" controls>
                            <source src="/miniatures/${mediaInfos.customer}/videos/low/${mediaInfos.id}.mp4" type="${mediaInfos.mimeType}">
                       </video>`;

        }
        else
            preview = `<img class="media_miniature miniature_${ mediaInfos.fileType }" src="/build/images/no-available-image.png" alt="/build/images/no-available-image.png">`;



        let html = `<tr data-index="${ mediaInfos.index }" id="upload_${ mediaInfos.index }" class="unregistered">`;

        if( this.__uploadMediaType === 'element_graphic' )
            html += this.showElementGraphicInfos(mediaInfos, preview);

        else
            html += this.showMediaInfos(mediaInfos, preview);

        html += `</tr>`;

        this.__$fileToCharacterisationList.find(`#upload_${mediaInfos.index}`).replaceWith( $(html) );

        this.addNewMediaCardInMediatheque(mediaInfos);

    }

    addNewMediaCardInMediatheque(mediaInfos)
    {

    }

    updateMediaCard(mediaInfos)
    {

    }

    onClickOnSaveButton(active)
    {
        if(active)
        {
            this.__$location.find('.popup_footer').on('click.onClickOnSaveButton', '.save_edits_button', e => {

                this.__$location.find('#medias_list_form').submit();

            })
        }
        else
        {
            this.__$location.find('.popup_footer').off('click.onClickOnSaveButton', '.save_edits_button');
        }

        return this;
    }

    mediaInfosEditFormIsValid(form)
    {
        let isValid = true;

        form.find('.form_input').each( (index, input) => {

            const inputFirstParent = $(input).parent();
            const inputParent = $(input).parents('tr');

            if( $(input).val() === '' )
            {
                isValid = false;
                $(input).addClass('invalid');
                inputFirstParent.find('span.error').text( this.__errors.empty_error ).removeClass('hidden');
            }
            else if( $(input).hasClass('file_name') && form.find(`input.file_name[value='${ $(input).val() }']`).length > 1 )
            {
                isValid = false;
                form.find(`input.file_name[value='${ $(input).val() }']`).addClass('invalid');
                form.find(`input.file_name[value='${ $(input).val() }']`).parent().find('span.error').text( this.__errors.duplicate_file ).removeClass('hidden');
            }
            else if( $(input).hasClass('diffusion_dates') )
            {

                const diffStartDateInput = inputParent.find('.diffusion_dates.start');
                const diffEndDateInput = inputParent.find('.diffusion_dates.end');

                const diffStartDate = new Date( diffStartDateInput.val() );
                const diffEndDate = new Date( diffEndDateInput.val() );

                if( !(diffStartDate instanceof Date) )
                {
                    isValid = false;
                    diffStartDateInput.addClass('invalid');
                    diffStartDateInput.parent().find('span.error').text( this.__errors.invalid_diffusion_start_date ).removeClass('hidden');
                }

                else if( !(diffEndDate instanceof Date) )
                {
                    isValid = false;
                    diffEndDateInput.addClass('invalid');
                    diffEndDateInput.parent().find('span.error').text( this.__errors.invalid_diffusion_end_date ).removeClass('hidden');
                }

                else if( diffEndDate < diffStartDate )
                {
                    isValid = false;
                    $(input).addClass('invalid');
                    inputFirstParent.find('span.error').text( this.__errors.invalid_diffusion_date ).removeClass('hidden');
                }

            }
            else
            {
                //isValid = true;
                $(input).removeClass('invalid');
                inputFirstParent.find('span.error').text('').addClass('hidden');
            }

        } )

        return isValid;

    }

    onMediaInfoEditingFormSubmit(active)
    {
        if(active)
        {

            this.__$location.find('#medias_list_form').on('submit.onMediaInfoEditingFormSubmit', e => {

                e.preventDefault();

                let formData = new FormData( $(e.currentTarget)[0] );
                //console.log(formData); debugger

                if( this.mediaInfosEditFormIsValid( $(e.currentTarget) ) )
                {

                    super.showLoadingPopup();

                    $.ajax({
                        url: `/mediatheque/${this.__uploadMediaType}`,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: (response) => {

                            this.__$fileToCharacterisationList.find('.unregistered').removeClass('unregistered');

                            this.addNewMediaCardInMediatheque(response);

                            this.__filesToUpload = [];

                        },
                        error: (response) => {
                            let error = response.responseJSON;
                            console.log(response);
                            console.log(error);
                            console.log(error.subject); //debugger
                            let subject = error.subject;

                            // on supprime la class 'unregistered' sur les elements enregistrés jusqu'à l'erreur
                            for (let i = 0; i < subject; i++)
                            {
                                $(`.media_list tbody tr[data-index='${ i }']`).removeClass('unregistered');
                            }


                            switch (error.text)
                            {

                                case "515 Duplicate File":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).text( this.__errors.duplicate_file ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .form_input.file_name`).addClass('invalid');
                                    break;

                                case "516 Invalid Filename":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).text( this.__errors.invalid_error ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container .form_input.file_name`).addClass('invalid');
                                    break;

                                case "517 Empty Filename":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).text( this.__errors.empty_error ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container .form_input.file_name`).addClass('invalid');
                                    break;

                                case "518 Too short Filename":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).text( this.__errors.too_short_error ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container .form_input.file_name`).addClass('invalid');
                                    break;

                                case "519 Invalid diffusion date":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container span.error`).text( this.__errors.invalid_diffusion_date ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container .diffusion_dates`).addClass('invalid');
                                    break;

                                case "519.1 Invalid diffusion start date":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container span.error`).text( this.__errors.invalid_diffusion_start_date ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container .diffusion_dates.start`).addClass('invalid');
                                    break;

                                case "519.2 Invalid diffusion end date":
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container span.error`).text( this.__errors.invalid_diffusion_end_date ).removeClass( 'hidden' );
                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container .diffusion_dates.end`).addClass('invalid');
                                    break;

                                default:
                                    console.error(error); debugger

                            }
                        },
                        complete: () => {
                            super.hideLoadingPopup();
                        },

                    });

                }

            })


        }
        else
        {
            this.__$location.find('#medias_list_form').off('submit.onMediaInfoEditingFormSubmit');
        }

        return this;
    }

    onClickOnPreviousButton(active)
    {
        if(active)
        {
            this.__$location.find('.popup_footer').on('click.onClickOnPreviousButton', '.show_prev_step', e => {

                const currentStep = parseInt($('.step.current_step').data('step'), 10);

                if( !isNaN(currentStep) && currentStep > 1)
                    this.showStep( currentStep -1 )

            })
        }
        else
        {
            this.__$location.find('.step').off('click.onClickOnPreviousButton', '.show_prev_step');
        }

        return this;
    }


    enable()
    {
        super.enable();

        this.onClickOnUploadButtonShowModal(true)
            .onClickOnCloseButtonCloseUploadPopup(true)
            .onClickOnCustomFileButtonActiveDefaultFileInput(true)
            .handleFileDragNDrop(true)
            .onFileDropAddFileInUploadList(true)
            .onClickOnRemoveFileButtonRemoveFileFromUploadList(true)
            .onClickOnStartUploadButton(true)
            .onMediaInfoEditingFormSubmit(true)
            .onClickOnSaveButton(true)
            .onClickOnPreviousButton(true)
        ;
    }

    disable()
    {
        super.disable();

        this.onClickOnUploadButtonShowModal(false)
            .onClickOnCloseButtonCloseUploadPopup(false)
            .onClickOnCustomFileButtonActiveDefaultFileInput(false)
            .handleFileDragNDrop(false)
            .onFileDropAddFileInUploadList(false)
            .onClickOnRemoveFileButtonRemoveFileFromUploadList(false)
            .onClickOnStartUploadButton(false)
            .onMediaInfoEditingFormSubmit(false)
            .onClickOnSaveButton(false)
            .onClickOnPreviousButton(false)
        ;
    }

}

export default UploadHandlerTool;