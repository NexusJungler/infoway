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

            medias: [
                'image/jpg', 'image/jpeg', 'image/png', 'image/bmp', 'image/gif', 'image/x-windows-bmp', 'image/pjpeg', 'image/svg+xml',
                '.jpg', '.jpeg', '.png', '.bmp', '.gif', '.svg', 'video/*', 'video/mp4', 'video/avi', 'video/x-matroska', 'video/3gpp', 'video/quicktime',
                '.mp4', '.avi', '.3gp'
            ],

            element_graphic: [

            ],

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

            encode_error: "Erreur durant l'enodage du fichier",

            bad_ratio: "Ce fichier ne possède pas un ratio valide",

        };

        this.__dataCheckingErrors = "";

        this.__uploadMediaType = $('.main-media').data('media_displayed');
        if(this.__uploadMediaType === null)
        {
            console.error("Error : cannot found data-media_displayed on element : .main-media !"); //debugger
        }

        let type = this.__uploadMediaType;
        $(".uploadbtn").attr("accept", this.__authorizedFiles[ type ]);

        this.__total_files_allowed = 50;
        this.__max_file_size = 524288000;
        this.__filesToUpload = [];
        this.__mediaInfos = [];
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

    closeModal()
    {
        $('.add-popup').removeClass('is-open');

        $('.upload-file-list').empty();
        $('.media_list tbody').empty();

        $('.start_upload_button').fadeOut()

        $(".upload-title").text("Préparation de l'Upload");
        $(".files_selection").fadeIn();
        $(".edit_media_info").fadeOut();

        this.__filesToUpload = [];
        this.__$mediasCollection.empty();
        this.__uploadAuthorized = false;
    }

    onClickOnModalCloseButtonsCloseModal(active)
    {

        if(active)
        {
            $('.btn-popupclose').on("click.onClickOnModalCloseButtonsCloseModal",e => {


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
    
    async addItemInUploadList(item)
    {

        let fileName = item.name;
        let fileNameWithoutExtension = fileName.replace( '.' + fileName.split('.').pop(), '' )

        console.log(item); //debugger
        console.log(fileNameWithoutExtension); //debugger

        // don't duplicate file in upload list
        if( $(`.upload-file-list tr[data-file='${fileName}']`).length === 0 )
        {

            const fileNameContainMultipleDot = this.fileNameContainMultipleDot(fileName);
            const fileExtension = (!fileNameContainMultipleDot) ? '.' + fileName.split('.').pop() : '';
            const fileIsAlreadyUploaded = await this.fileIsAlreadyUploaded(fileName);
            const fileMimeType = item.type;

            //console.log(fileNameContainMultipleDot, fileMimeType, fileExtension); //debugger

            // sometime file mimetype was empty
            // in this case, use extension for check if file can be upload
            const fileExtensionIsAccepted = this.fileMimeTypeIsAccepted( (fileMimeType !== "") ? fileMimeType : fileExtension );
            //console.log(fileExtensionIsAccepted); //debugger
            const fileSize = item.size;
            let fileIsAccepted = true;

            let newUploadFileItem = $("<tr>", {
                //class: (!fileExtensionIsAccepted || fileIsAlreadyUploaded || fileNameContainMultipleDot || (fileNameWithoutExtension.length < 5) ) ? 'invalid_upload_item' : 'valid_upload_item'
                class: (!fileExtensionIsAccepted || fileIsAlreadyUploaded || fileNameContainMultipleDot ) ? 'invalid_upload_item' : 'valid_upload_item'
            }).attr("data-file", fileName);

            let newUploadItem = `<td>${fileName}</td>`;

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

            /*else if(fileNameWithoutExtension.length < 5)
            {
                html += `<td><span class="error">Le nom de votre fichier doit contenir au moins 5 caractères !</span></td>`;
                fileIsAccepted = false;
            }*/

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
                                 <td><i data-target="${fileName}" class="fas fa-trash-alt remove_file_from_list"></i></td>`;

            newUploadFileItem.html(newUploadItem);

            newUploadFileItem.appendTo( $(`.upload-file-list`) );

            if(fileIsAccepted)
                this.__filesToUpload.push( {index: this.__filesToUpload.length, name: fileName, file: item} );

            console.log(this.__filesToUpload); //debugger

            if($(".upload-file-list tr.valid_upload_item").length > this.__total_files_allowed)
            {
                this.__uploadAuthorized = false;
                $('.start-upload-container .error_over-max-file').text(`Vous avez sélectionné ${$(".upload-info table tbody tr.valid_upload_item").length} fichiers, le maximum autorisé est de ${this.__total_files_allowed} !"`).fadeIn();
                $(".model-upload-file .start_upload_button").fadeOut();
            }

            else if($(".upload-file-list tr.valid_upload_item").length < 1)
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

    async cancelUploadedMedia(media)
    {

        return new Promise( (resolve, reject) => {

            $.ajax({
                url: "/remove/media",
                type: "POST",
                data: {media: media},
                success: (response) => {
                    console.log(response); //debugger
                    resolve(true);

                },
                error: (response, status, error) => {

                    console.error(response); //debugger
                    resolve(false);

                },
            })

        } );

    }

    onClickOnUploadCancelButton(active)
    {
        if(active)
        {
            this.__$location.on("click.onClickOnUploadCancelButton", ".cancel-upload", async (e) => {

                if(!$(e.currentTarget).parents('tr').hasClass('invalid-download') && confirm("Etes-vous sûr de vouloir supprimer ce média ?"))
                {

                    const index = $(e.currentTarget).parents('tr').data('index');

                    let mediaToDelete = this.__$mediasCollection.find(`li[data-index='${ index }'] .media_id`).attr('value');
                    //console.log(mediaToDelete); debugger
                    const mediaIsDeleted = await this.cancelUploadedMedia(mediaToDelete);
                    if(mediaIsDeleted)
                    {
                        $(e.currentTarget).parents('tr').remove();
                        this.__$mediasCollection.find(`li[data-index='${ index }']`).remove();

                        if($('.media_list tbody tr').length === 0)
                        {
                            $('.edit_media_info .action-btn-container').fadeOut();
                        }

                    }

                    else
                        alert("Erreur interne durant la suppression du media");

                }
                else if($(e.currentTarget).parents('tr').hasClass('invalid-download'))
                {
                    $(e.currentTarget).parents('tr').remove();
                }

            })
        }
        else
        {
            this.__$location.off("click.onClickOnUploadCancelButton", ".cancel-upload");
        }

        return this;
    }

    onClickOnCancelAllUpload(active)
    {

        if(active)
        {
            this.__$location.find(".edit_media_info .cancel-all-upload").on("click.onClickOnCancelAllUpload", (e) => {

                if(confirm("Etes-vous sûr de vouloir supprimer tout les médias ?"))
                {

                    this.__$mediasCollection.find('li').each( async(index, collectionItem) => {

                        let mediaToDelete = $(collectionItem).find('.media_id').attr('value');
                        //console.log(mediaToDelete); debugger
                        const mediaIsDeleted = await this.cancelUploadedMedia(mediaToDelete);
                        if(mediaIsDeleted)
                        {
                            $(`.edit_media_info tr[data-index='${$(collectionItem).data('index')}']`).remove();
                            $(collectionItem).remove();

                            if($('.media_list tbody tr').length === 0)
                            {
                                $('.edit_media_info .action-btn-container').fadeOut();
                            }
                        }

                        else
                            alert("Erreur interne durant la suppression du media");

                    } )

                }

            })
        }
        else
        {
            this.__$location.find(".edit_media_info .cancel-all-upload").off("click.onClickOnCancelAllUpload")
        }

        return this;
    }

    addNewItemInMediaCollection(item)
    {
        console.log(item); //debugger
        if( $('.medias-list-to-upload').find(`.media_name[value='${ item.fileName }']`).length === 0 )
        {

            let list = this.__$mediasCollection;
            //console.log(item); debugger
            // Try to find the counter of the list or use the length of the list
            let counter = list.children().length;

            // grab the prototype template
            let newWidget = list.attr('data-prototype');
            //console.log(newWidget); debugger
            // replace the "__name__" used in the id and name of the prototype
            // with a number that's unique to your emails
            // end name attribute looks like name="contact[emails][2]"
            newWidget = newWidget.replace(/__name__/g, counter);
            newWidget = newWidget.replace(/__MEDIA_ID__/g, item.id);
            newWidget = newWidget.replace(/__MEDIA_NAME__/g, item.fileName);
            newWidget = newWidget.replace(/__MEDIA_TYPE__/g, item.type);
            newWidget = newWidget.replace(/__MEDIA_EXTENSION__/g, item.extension);

            // create a new list element and add it to the list
            let newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
            newElem.attr( 'data-index', counter );

            // put data in input
            //newElem.find('.media_name').val(fileName);

            let now = new Date();
            let month = (now.getMonth() + 1);
            let day = now.getDate();
            let year = now.getFullYear();

            //newElem.find(`.media_diffusion_date_start select`).attr('disabled', true);
            newElem.find(`.media_diffusion_date_start #medias_list_medias_${counter}_diffusionStart_day option[value='${day}']`).attr('selected', true);
            newElem.find(`.media_diffusion_date_start #medias_list_medias_${counter}_diffusionStart_month option[value='${month}']`).attr('selected', true);

            // rebuild year field
            // par defaut symfony construit le select avec un interval : année - 5 < année < année + 5
            newElem.find(`.media_diffusion_date_start #medias_list_medias_${counter}_diffusionStart_year`).html(this.rebuildYearFieldContent(counter, {type: 'start', choice: year}));

            //newElem.find(`.media_diffusion_date_end select`).attr('disabled', true);
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
    }

    buildUploadList()
    {

        $.each( this.__filesToUpload, (index, fileToUpload) => {

            let html = `<tr data-index="${ index }" id="upload_${index}" class="unregistered">
                                <td class="file-name-container uploaded-file-name-col">
                                    <p><i class="fas fa-trash-alt cancel-upload" style="display: none"></i>${fileToUpload.name}</p>
                                </td>
                                
                                <td class="file_progress_bar_container file-upload-state-col">
                                    <progress class="progress_bar" id="progress_${index}" max="100" value="0"></progress>
                                </td>
                                
                                <td class="media-choice-input-col" style="display: none">
                                    <input type="checkbox" class="choice-media" data-media="">
                                </td>
                                
                                <td class="preview-container">
                                    <span class="upload_state"></span>
                                </td>
                                
                                <td class="characteristic-container">
                                    
                                </td>
                                
                                <td class="edit-name-container">
                                    
                                </td>
                                
                                <td class="media-diff-date-container">
                                    
                                </td>
                                        
                                <td class="criterions-affectation-container">
                                    
                                </td>        
                                        
                                <td class="tags-affectation-container">
                                    
                                </td>
                                        
                                <td class="products-affectation-container">
                                    
                                </td>
                                        
                                <td class="contain-incrustations-container">
                                    
                                </td>
                                
                            </tr>`;

            $(html).appendTo( $('.edit_media_info .tbody') );

        } )

    }

    onClickOnStartUploadButtonStartUpload(active)
    {

        if (active)
        {
            $(".model-upload-file .start_upload_button").on("click.onClickOnStartUploadButtonStartUpload", e => {
                console.log(this.__uploadAuthorized); //debugger
                if(this.__uploadAuthorized)
                {

                    this.buildUploadList();

                    let uploadFinished = 0;

                    $(".files_selection").fadeOut();
                    $(".edit_media_info").fadeIn();

                    $(".files_selection table tbody").empty();
                    $(".upload-title").text("Médias à caractériser");
                    //$(".modal-upload-download").fadeIn();
                    let ajax = null;

                    // for each file in upload list
                    $.each( this.__filesToUpload, (index, fileToUpload) => {


                        const uploadState = $(`.edit_media_info .tbody #upload_${fileToUpload.index} .upload_state`);
                        uploadState.html("Téléchargement en cours ...");
                        let formData = new FormData();
                        formData.append('file', fileToUpload.file);
                        formData.append('media_type', this.__uploadMediaType);

                        const fileExtension = fileToUpload.file.name.split('.').pop();
                        const fileName = fileToUpload.file.name.replace( '.' + fileExtension , '');


                        ajax = $.ajax({
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
                                        $(`#upload_${fileToUpload.index} progress`).addClass("on_upload").attr("value", percent);


                                        uploadState.html(`Téléchargement en cours ... (${percent}%)`);
                                        //jQuery('#progress' + (index + 1) + ' .progress-bar').css("left", +percent + "%");

                                        if(percent === 100 && this.__uploadMediaType === 'video')
                                            uploadState.html("Encodage en cours ...");

                                        //jQuery('#progress' + (index + 1) + ' .status').text(percent + "%");
                                    }, true);
                                }

                                return xhr;
                            },
                            mimeType: "multipart/form-data",
                            success: async (response) => {

                                response = JSON.parse(response);

                                if(response.type === 'image')
                                {

                                    let mediaInfos = {
                                        id: response.id,
                                        customer: response.customer,
                                        index: fileToUpload.index,
                                        type: response.type,
                                        fileName: fileToUpload.file.name,
                                        name: fileName,
                                        extension: response.extension,
                                        height: response.height,
                                        width: response.width,
                                        dpi: response.dpi,
                                        mimeType: response.mimeType,
                                        highestFormat: response.highestFormat,
                                    };

                                    this.__filesToUpload.splice(index , 1);

                                    uploadState.html("Téléchargement terminé !");

                                    $(`#upload_${fileToUpload.index} .cancel-upload`).fadeIn();
                                    $(`#upload_${fileToUpload.index} progress`).removeClass("on_upload");
                                    $(`#upload_${fileToUpload.index}`).removeClass("valid-download");

                                    uploadFinished++;

                                    if( $(`#upload_${fileToUpload.index} .file_progress_bar_container i`).length === 0 )
                                        $('<i>', { class: 'fas fa-check' }).appendTo( $(`#upload_${fileToUpload.index} .file_progress_bar_container`) )

                                    // new item
                                    this.addNewItemInMediaCollection( {id: response.id, fileName: fileName, extension: fileExtension, type: mediaInfos.type} );

                                    this.showMediaCharacteristic(mediaInfos);

                                    $('.edit-btn-container').fadeIn();

                                }
                                else
                                {

                                    uploadState.html("Encodage en cours ...");

                                    // check status every 10sec
                                    let videoEncodingResult = await this.checkVideoEncodingStatus(response.id, fileToUpload.index);

                                    while (videoEncodingResult.status !== 'Finished')
                                    {
                                        // wait 10s before checking again
                                        this.sleep(10000);
                                        videoEncodingResult = await this.checkVideoEncodingStatus(response.id, fileToUpload.index);
                                    }

                                    $(`#upload_${fileToUpload.index} progress`).removeClass("on_upload");
                                    uploadFinished++;

                                    console.log(videoEncodingResult); //debugger

                                    if(videoEncodingResult.status === "Finished")
                                    {

                                        $(`#upload_${fileToUpload.index} .cancel-upload`).fadeIn();

                                        if( $(`#upload_${fileToUpload.index} .file_progress_bar_container i`).length === 0 )
                                            $('<i>', { class: (typeof videoEncodingResult.error !== "undefined") ? 'fas fa-times' : 'fas fa-check' }).appendTo( $(`#upload_${fileToUpload.index} .file_progress_bar_container`) )

                                        if(typeof videoEncodingResult.error === "undefined")
                                        {

                                            $(`#upload_${fileToUpload.index}`).removeClass("valid-download");

                                            this.addNewItemInMediaCollection( {id: videoEncodingResult.id, fileName: fileName, extension: videoEncodingResult.extension, type: videoEncodingResult.type} );

                                            let videoInfos = {
                                                id: videoEncodingResult.id,
                                                customer: videoEncodingResult.customer,
                                                index: fileToUpload.index,
                                                type: videoEncodingResult.type,
                                                fileName: videoEncodingResult.fileName,
                                                name: videoEncodingResult.name,
                                                extension: videoEncodingResult.extension,
                                                height: videoEncodingResult.height,
                                                width: videoEncodingResult.width,
                                                dpi: videoEncodingResult.dpi,
                                                codec: videoEncodingResult.codec,
                                                mimeType: videoEncodingResult.mimeType,
                                                highestFormat: videoEncodingResult.highestFormat,
                                            };

                                            this.showMediaCharacteristic(videoInfos);

                                            $('.edit-btn-container').fadeIn();

                                        }
                                        else
                                        {
                                            $(`#upload_${fileToUpload.index}`).addClass('invalid-download');
                                            uploadState.html(`${this.__errors.encode_error}`);
                                        }

                                    }

                                }

                                if($('.media_list tbody tr.valid-download').length > 0)
                                    $('.edit_media_info .action-btn-container').fadeIn();

                            },
                            error: (response, status, error) => {

                                //ajax.abort();

                                $(`#upload_${fileToUpload.index} .cancel-upload`).fadeIn();

                                //$(".modal-upload-download .show_media_edit_container").fadeOut();
                                this.__filesToUpload.splice(index , 1);
                                //$(`.modal-upload-download #upload_${fileToUpload.index} progress`).css({ 'color': 'red' });

                                $(`#upload_${fileToUpload.index} progress`).removeClass("on_upload");
                                $(`#upload_${fileToUpload.index}`).removeClass("unregistered");
                                $(`#upload_${fileToUpload.index}`).addClass('invalid-download');
                                $('<i>', { class: 'fas fa-times' }).appendTo( $(`#upload_${fileToUpload.index} .file_progress_bar_container`) );

                                switch (response.responseText)
                                {

                                    case "512 Bad Extension":
                                        uploadState.html(`${this.__errors.bad_extension}`);
                                        break;

                                    case "513 Bad Resolution":
                                        uploadState.html(`${this.__errors.bad_resolution}`);
                                        break;

                                    case "514 Corrupt File":
                                        uploadState.html(`${this.__errors.corrupt_file}`);
                                        break;

                                    case "515 Duplicate File":
                                        uploadState.html(`${this.__errors.duplicate_file}`);
                                        break;

                                    case "516 Invalid Filename":
                                        // <i class='fas fa-times'></i>
                                        uploadState.html(`${this.__errors.invalid_error}`);
                                        break;

                                    case "517 Empty Filename":
                                        uploadState.html(`${this.__errors.empty_error}`);
                                        break;

                                    case "518 Too short Filename":
                                        uploadState.html(`${this.__errors.too_short_error}`);
                                        break;

                                    case "521 Bad ratio":
                                        uploadState.html(`${this.__errors.bad_ratio}`);
                                        break;

                                    default:
                                        $(`#upload_${fileToUpload.index} .upload_state`).html("Téléchargement annulé suite à une erreur interne !");
                                        console.log(response.responseText); debugger

                                }

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

    sleep(milliseconds) {
        const date = Date.now();
        let currentDate = null;
        do {
            currentDate = Date.now();
        } while (currentDate - date < milliseconds);
    }

    async checkVideoEncodingStatus(id)
    {

        // check status every 10sec
        return new Promise( (resolve, reject) => {

            $.ajax({
                url: "/get/video/encoding/status",
                type: "POST",
                data: {id: id},
                success: (response) => {
                    //console.log(response);
                    resolve(response);

                },
                error: (response, status, error) => {

                    console.error(response); //debugger
                    resolve(response);

                },
            })

        } );

    }

    showMediaCharacteristic(mediaInfos)
    {

        //console.log(mediaInfos); debugger

        this.__mediaInfos.push(mediaInfos);

        $(`#upload_${mediaInfos.index} .choice-media`).attr('data-media', mediaInfos.index);

        // show miniatures
        $(`#upload_${mediaInfos.index} .preview-container`).empty();
        let preview = null;

        if(mediaInfos.type === 'image')
            preview = `<img class="preview" src="/miniatures/${mediaInfos.customer}/images/low/${mediaInfos.id}.png" alt="/miniatures/${mediaInfos.customer}/image/${mediaInfos.id}.png" />`;

        else
            preview = `<video class="preview" controls>
                            <source src="/miniatures/${mediaInfos.customer}/videos/low/${mediaInfos.id}.${mediaInfos.extension}" type="${mediaInfos.mimeType}">
                       </video>`;

        preview +=  `<i class="fas fa-expand-alt expand-miniature" data-media_id="${mediaInfos.id}"></i>`;

        $(preview).appendTo( $(`#upload_${mediaInfos.index} .preview-container`) );

        // show characteristics
        let characteristics = `<span>${mediaInfos.extension}</span> <br> <span>${mediaInfos.width} * ${mediaInfos.height} px</span> <br> <span>${ (mediaInfos.type === 'image') ? mediaInfos.dpi + ' dpi' :  mediaInfos.codec}</span>`;
        $(characteristics).appendTo( $(`#upload_${mediaInfos.index} .characteristic-container`) );


        // show input for edit media name
        let nameEditorInput = `<span class="error hidden"></span> <br> <input type="text" class="form_input fileName" placeholder="Nom du media" value="${mediaInfos.name}" required>`;
        $(nameEditorInput).appendTo( $(`#upload_${mediaInfos.index} .edit-name-container`) )


        // show input for edit media diffusion dates
        let now = new Date();
        let month = (now.getMonth() + 1);
        month = (month < 10) ? '0' + month : month;
        let day = (now.getDate() < 10 ) ? '0' + now.getDate() : now.getDate();
        let year = now.getFullYear();

        let mediaDiffusionDatesEditorInputs = `<div class="diff-start-container">
                                                    <span class="error hidden"></span> <br> 
                                                    <label for="media_${mediaInfos.index}_diff_start">Du</label>
                                                    <input type="date" id="media_${mediaInfos.index}_diff_start" class="diffusion_dates start form_input" value="${year}-${month}-${day}">
                                               </div>

                                               <div class="diff-end-container">
                                                    <span class="error hidden"></span> <br> 
                                                    <label for="media_${mediaInfos.index}_diff_end">Au</label>
                                                    <input type="date" id="media_${mediaInfos.index}_diff_end" class="diffusion_dates end form_input" min="${year}-${month}-${day}" value="${year + 10}-${month}-${day}">
                                               </div>`;

        $(mediaDiffusionDatesEditorInputs).appendTo( $(`#upload_${mediaInfos.index} .media-diff-date-container`) )

        // show criteres association button
        //let criterionsAssociationButton = `<button type="button" class="associate-criterion" data-media="${mediaInfos.name}">Critères</button><div class="associated-criterions-container"></div>`;
        let criterionsAssociationButton = `<div class="associated-criterions-container"></div>`;
        $(criterionsAssociationButton).appendTo( $(`#upload_${mediaInfos.index} .criterions-affectation-container`) );

        // show tags association button
        let tagsAssociationButton = `<button type="button" class="associate-tag association-btn" data-media="${mediaInfos.name}">TAGS</button><div class="associated-tags-container"></div>`;
        $(tagsAssociationButton).appendTo( $(`#upload_${mediaInfos.index} .tags-affectation-container`) );

        // show products association button
        let productsAssociationButton = `<button type="button" class="associate-product association-btn" data-media="${mediaInfos.name}">Associer</button><div class="associated-products-container"></div>`;

        $(productsAssociationButton).appendTo( $(`#upload_${mediaInfos.index} .products-affectation-container`) );

        // show input (type radio) for inscrustes
        let containIncrustationsInput = `<div class="choice-media-contain-incruste-container">
                                            <label class=""><input type="radio" name="media_${mediaInfos.index}_contain_incrustations" class="form_input choice-media-contain-incruste" value="yes">Oui</label> 
                                            <label class=""><input type="radio" name="media_${mediaInfos.index}_contain_incrustations" class="form_input choice-media-contain-incruste" checked value="no">Non</label>
                                         </div>
                                         
                                         <div class="add-price-incruste-btn-container" style="display: none">
                                            <button type="button" class="add-price-incruste-btn">Incruster Prix</button>
                                         </div>
                                         `;

        $(containIncrustationsInput).appendTo( $(`#upload_${mediaInfos.index} .contain-incrustations-container`) );

    }

    rebuildYearFieldContent(index, dateData)
    {

        let now = new Date();

        // -5 year, utile ??
        // pour pouvoir laisser le choix de selectionner une date passée (dans le cas de la date de debut de diffusion)
        let startYear = (dateData.type === 'end') ? now.getFullYear() : now.getFullYear() - 5;

        // +10 par defaut
        // pour simuler qu'un média à une date de diffusion "illimité"
        let endYear = startYear + 10;
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

    onClickOnSaveButtonSendMediaInfo(active)
    {

        if(active)
        {
            $('.save-media-modif').on('click.onClickOnSaveButtonSendMediaInfo', e => {

                this.allFormInputIsNotEmpty();

                if( this.__$location.find('.media_list tbody .form_input.invalid').length === 0)
                {

                    let formData = new FormData($('form#medias_list_form')[0]);
                    console.log(formData); debugger

                    //console.log( this.__$location.find('form#medias_list_form') ); debugger
                    $.ajax({
                        type: 'post',
                        //url: '/edit/media',
                        url: `/mediatheque/${this.__uploadMediaType}`,
                        data: formData,
                        processData: false,
                        contentType: false,
                        mimeType: 'multipart/form-data',
                        success: (response) => {

                            $(`.edit_media_info .unregistered`).removeClass('unregistered');
                            $('.show-media-info-resume').fadeIn();

                            //@TODO: insert media card in mediatheque after saving

                        },
                        error: (response) => {

                            let error = response;
                            console.log(response);
                            console.log(error);
                            console.log(error.subject); debugger
                            let subject = error.subject;

                            // on supprime la class 'unregistered' sur les elements enregistrés jusqu'à l'erreur
                            for (let i = 0; i < subject; i++)
                            {
                                $(`.media_list tbody tr[data-index='${ i }']`).removeClass('unregistered');
                            }


                            switch (error.text)
                            {

                                case "515 Duplicate File":
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container span.error`).text( this.__errors.duplicate_file );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container span.error`).removeClass( 'hidden' );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container .form_input.fileName`).addClass('invalid');
                                    break;

                                case "516 Invalid Filename":
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container span.error`).text( this.__errors.invalid_error );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container span.error`).removeClass( 'hidden' );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container .form_input.fileName`).addClass('invalid');
                                    break;

                                case "517 Empty Filename":
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container span.error`).text( this.__errors.empty_error );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container span.error`).removeClass( 'hidden' );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container .form_input.fileName`).addClass('invalid');
                                    break;

                                case "518 Too short Filename":
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container span.error`).text( this.__errors.too_short_error );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container span.error`).removeClass( 'hidden' );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container .form_input.fileName`).addClass('invalid');
                                    break;

                                case "519 Invalid diffusion date":
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-diff-date-container span.error`).text( this.__errors.invalid_diffusion_date );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-diff-date-container span.error`).removeClass( 'hidden' );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container .diffusionDates`).addClass('invalid');
                                    break;

                                case "519.1 Invalid diffusion start date":
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-diff-date-container span.error`).text( this.__errors.invalid_diffusion_start_date );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-diff-date-container span.error`).removeClass( 'hidden' );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-name-container .diffusionDates.start`).addClass('invalid');
                                    break;

                                case "519.2 Invalid diffusion end date":
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-diff-date-container span.error`).text( this.__errors.invalid_diffusion_end_date );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-diff-date-container span.error`).removeClass( 'hidden' );
                                    this.__$location.find(`.media_list tbody tr[data-index='${ subject }'] .media-diff-date-container .diffusionDates.end`).addClass('invalid');
                                    break;

                                default:
                                    console.error(error.text); //debugger

                            }

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

    allFormInputIsNotEmpty()
    {
        this.__$location.find('.media_list tbody .form_input').each( (index, element) => {

            if( $(element).val() === '' )
            {
                $(element).addClass('invalid');
                $(element).parent().find('span.error').text('Ce champ ne peut pas être vide').removeClass('hidden');
            }

        } )

    }

    onTypingFileNewNameCheckValidity(active)
    {

        if(active)
        {
            $(".edit_media_info tbody").on("input.onTypingFileNewNameCheckValidity", ".form_input.fileName", e => {

                const input = $(e.currentTarget);
                const nameIsValid = this.checkMediaNameValidity(input.val());

                $(e.currentTarget).parents('tr').addClass('unregistered');
                $('.show-media-info-resume').fadeOut();

                input.parents('tr').addClass('unregistered');

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
                    //console.log(index);
                    //console.log(this.__$mediasCollection.find(`li[data-index='${ index }'] .media_name`)); debugger
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
            $(".edit_media_info tbody").on("change.onDiffusionDateChangeUpdateDateInCollection", ".diffusion_dates", e => {

                //console.log( $(e.currentTarget).val() ); //debugger

                $(e.currentTarget).parents('tr').addClass('unregistered');

                const explode = $(e.currentTarget).val().split('-');
                const day = explode[2].replace(/^0/,'');
                const month = explode[1].replace(/^0/,'');
                const year = explode[0];

                let index = $(e.currentTarget).parents('tr').data('index');
                //console.log(index); //debugger

                const collectionItem = this.__$mediasCollection.find(`li[data-index='${ index }'] `);

                //console.log(day, month, year); //debugger

                if( $(e.currentTarget).hasClass('start') )
                {
                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index }_diffusionStart_day option:selected`).attr('selected', false);
                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index }_diffusionStart_day option[value='${day}']`).attr('selected', true);

                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index }_diffusionStart_month option:selected`).attr('selected', false);
                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index }_diffusionStart_month option[value='${month}']`).attr('selected', true);

                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index }_diffusionStart_year option:selected`).attr('selected', false);
                    collectionItem.find(`.media_diffusion_date_start #medias_list_medias_${ index }_diffusionStart_year option[value='${year}']`).attr('selected', true);
                }
                else if( $(e.currentTarget).hasClass('end') )
                {
                    collectionItem.find(`.media_diffusion_date_end #medias_list_medias_${ index }_diffusionEnd_day option:selected`).attr('selected', false);
                    collectionItem.find(`.media_diffusion_date_end #medias_list_medias_${ index }_diffusionEnd_day option[value='${day}']`).attr('selected', true);

                    collectionItem.find(`.media_diffusion_date_end #medias_list_medias_${ index }_diffusionEnd_month option:selected`).attr('selected', false);
                    collectionItem.find(`.media_diffusion_date_end #medias_list_medias_${ index }_diffusionEnd_month option[value='${month}']`).attr('selected', true);

                    collectionItem.find(`.media_diffusion_date_end #medias_list_medias_${ index }_diffusionEnd_year option:selected`).attr('selected', false);
                    collectionItem.find(`.media_diffusion_date_end #medias_list_medias_${ index }_diffusionEnd_year option[value='${year}']`).attr('selected', true);
                }

            })
        }
        else
        {
            $(".edit_media_info tbody").off("change.onDiffusionDateChangeUpdateDateInCollection", ".diffusionDates");
        }

        return this;
    }

    onFormInputChangeAddClassUnregistered(active)
    {
        if(active)
        {
            this.__$location.on('change.onFormInputChangeAddClassUnregistered', '.form_input', e => {

                const input = $(e.currentTarget);

                input.removeClass('invalid');
                input.parent().find('.error').text("").addClass('hidden');

                input.parents('tr').addClass('unregistered');
                $('.show-media-info-resume').fadeOut();

                if( input.hasClass('choice-media-contain-incruste') )
                {
                    let index = input.parents('tr').data('index');
                    this.__$mediasCollection.find(`li[data-index='${ index }'] .media_contain_incruste input`).removeAttr('checked');
                    this.__$mediasCollection.find(`li[data-index='${ index }'] .media_contain_incruste input[value='${input.val()}']`).attr('checked', true);
                    input.parents('tr').find('.add-price-incruste-btn').attr('disable', (input.val() === 'no'));
                }

            })
        }
        else
        {
            this.__$location.off('change.onFormInputChangeAddClassUnregistered', '.form_input');
        }

        return this;
    }

    onClickOnExpandMiniatureButton(active)
    {

        if(active)
        {
            this.__$location.on("click.onClickOnExpandMiniatureButton", ".expand-miniature", e => {

                const mediaId = $(e.currentTarget).data('media_id');
                const index = this.__mediaInfos.findIndex( mediaInfo => mediaInfo.id === mediaId );

                if(index !== -1)
                {
                    const mediaInfo = this.__mediaInfos[index];
                    $('.expand-miniature-container .modal-body').html(`<img src="/miniatures/${mediaInfo.customer}/images/${mediaInfo.highestFormat}/${mediaId}.png" alt="/miniatures/${mediaInfo.customer}/image/${mediaInfo.highestFormat}/${mediaId}.png">`);
                    this.__$location.css({ 'z-index': '0' });
                    $('.expand-miniature-container').fadeIn();
                }

            })
        }
        else
        {
            this.__$location.off("click.onClickOnExpandMiniatureButton", ".expand-miniature");
        }

        return this;
    }

    onClickOnMiniatureExpandedPopupCloseBtn(active)
    {
        if(active)
        {
            $('.expand-miniature-container .close-btn').on('click.onClickOnMiniatureExpandedPopupCloseBtn', e => {

                this.__$location.css({ 'z-index': '' });
                $('.expand-miniature-container').fadeOut();
                $('.expand-miniature-container .modal-body').empty();

            })
        }
        else
        {
            $('.expand-miniature-container .close-btn').off('click.onClickOnMiniatureExpandedPopupCloseBtn');;
        }

        return this;
    }

    onClickOnShowResumeButton(active)
    {
        if(active)
        {
            this.__$location.find('.show-media-info-resume').on('click.onClickOnShowResumeButton', e => {

                if( $('tr.unregistered').length === 0 )
                {
                    $('.edit-btn-container').fadeOut();
                    $('.main-btn-container').fadeIn();
                    $('.uploaded-file-name-col').fadeOut();
                    $('.file-upload-state-col').fadeOut();
                    $('.association-btn').fadeOut();
                    $('.media-choice-input-col').fadeIn();
                    $('.form_input').attr('readonly', 'true');
                    $('.choice-media-contain-incruste-container').fadeOut();
                    $('.add-price-incruste-btn-container').fadeIn();
                }
                else
                    alert("Vous devez enregistrer vos modifications pour continuer !");

            })
        }
        else
        {
            this.__$location.find('.show-media-info-resume').off('click.onClickOnShowResumeButton');
        }

        return this;
    }

    onClickOnPreviousBtnShowMediaEditContainer(active)
    {
        if(active)
        {
            this.__$location.find('.show-media-edit-btn').on('click.onClickOnPreviousBtnShowMediaEditContainer', e => {

                $('.form_input').removeAttr('readonly');
                $('.edit-btn-container').fadeIn();
                $('.main-btn-container').fadeOut();
                $('.uploaded-file-name-col').fadeIn();
                $('.file-upload-state-col').fadeIn();
                $('.association-btn').fadeIn();
                $('.media-choice-input-col').fadeOut();
                $('.choice-media-contain-incruste-container').fadeIn();
                $('.add-price-incruste-btn-container').fadeOut();

            })

        }
        else
        {
            this.__$location.find('.show-media-edit-btn').off('click.onClickOnPreviousBtnShowMediaEditContainer');
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
            .onClickOnRemoveFileButtonRemoveFileFromList(true)
            .onClickOnSaveButtonSendMediaInfo(true)
            .onTypingFileNewNameCheckValidity(true)
            .onDiffusionDateChangeUpdateDateInCollection(true)
            .onClickOnExpandMiniatureButton(true)
            .onClickOnUploadCancelButton(true)
            .onClickOnCancelAllUpload(true)
            .onClickOnShowResumeButton(true)
            .onClickOnPreviousBtnShowMediaEditContainer(true)
            .onFormInputChangeAddClassUnregistered(true)
            .onClickOnMiniatureExpandedPopupCloseBtn(true)
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
            .onClickOnRemoveFileButtonRemoveFileFromList(false)
            .onClickOnSaveButtonSendMediaInfo(false)
            .onTypingFileNewNameCheckValidity(false)
            .onDiffusionDateChangeUpdateDateInCollection(false)
            .onClickOnExpandMiniatureButton(false)
            .onClickOnUploadCancelButton(false)
            .onClickOnCancelAllUpload(false)
            .onClickOnShowResumeButton(false)
            .onClickOnPreviousBtnShowMediaEditContainer(false)
            .onFormInputChangeAddClassUnregistered(false)
            .onClickOnMiniatureExpandedPopupCloseBtn(false)
        ;
    }

}

export default UploadHandlerTool;