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
        if(this.__uploadMediaType === null || typeof this.__uploadMediaType === 'undefined' || this.__uploadMediaType === 'undefined')
        {
            console.error("Error : cannot found data-media_displayed on element : .main-media !"); //debugger
        }

        let type = this.__uploadMediaType;
        $(".default_upload_input").attr("accept", this.__authorizedFiles[ type ]);

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

    onClickOnModalCloseButtonsCloseModal(active)
    {

        if(active)
        {
            $('.close_modal_button').on("click.onClickOnModalCloseButtonsCloseModal",e => {

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
            $('.close_modal_button').off("click.onClickOnModalCloseButtonsCloseModal");
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

    async cancelUploadedMedia(mediaId)
    {

        return new Promise( (resolve, reject) => {

            $.ajax({
                url: `/remove/media/${mediaId}`,
                type: "POST",
                data: {},
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

    /*onClickOnUploadCancelButton(active)
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
        if( this.__$fileToCharacterisationList.find(`.media_name[value='${ item.fileName }']`).length === 0 )
        {


            this.__$fileToCharacterisationList.find(`.media_name[value='${ item.fileName }']`).parents('tr')

            let list = this.__$mediasCollection;
            //console.log(item); debugger
            // Try to find the counter of the list or use the length of the list
            let counter = this.__$fileToCharacterisationList.children('tr').length;

            // grab the prototype template
            let newWidget = list.attr('data-prototype');
            //console.log(newWidget); debugger
            // replace the "__name__" used in the id and name of the prototype
            // with a number that's unique to your emails
            // end name attribute looks like name="contact[emails][2]"
            newWidget = newWidget.replace(/__name__/g, counter);
            newWidget = newWidget.replace(/__MEDIA_ID__/g, item.id);
            newWidget = newWidget.replace(/__MEDIA_NAME__/g, item.fileName);
            //newWidget = newWidget.replace(/__MEDIA_TYPE__/g, item.type);
            newWidget = newWidget.replace(/__MEDIA_EXTENSION__/g, item.extension);

            // create a new list element and add it to the list
            let newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
            newElem.attr( 'data-index', counter );

            newElem.find(`#medias_list_medias_${ counter }`).css( { 'display': 'flex', 'flex-direction': 'row' } )

            newElem.find('.media_id, .media_type, .media_extension').parent().remove();

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
            newElem.appendTo( this.__$fileToCharacterisationList );
            //console.log(list); debugger

            // Increase the counter
            counter++;
            // And store it, the length cannot be used if deleting widgets is allowed
            list.data('widget-counter', counter);
        }
    }*/

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
                            success: async (response) => {

                                response = JSON.parse(response);

                                if(response.fileType === 'image')
                                {

                                    let mediaInfos = {
                                        id: response.id,
                                        customer: response.customer,
                                        index: fileToUpload.index,
                                        fileType: response.fileType,
                                        fileName: response.fileName,
                                        fileNameWithoutExtension: response.fileNameWithoutExtension,
                                        extension: response.extension,
                                        height: response.height,
                                        width: response.width,
                                        dpi: response.dpi,
                                        miniatureExist: response.miniatureExist,
                                        //highestFormat: response.highestFormat,
                                    };

                                    uploadStateIndicator.html("Téléchargement terminé !");

                                    $(`#upload_${fileToUpload.index} .cancel-upload`).fadeIn();
                                    $(`#upload_${fileToUpload.index} progress`).removeClass("on_upload");
                                    $(`#upload_${fileToUpload.index}`).removeClass("valid_download");

                                    uploadFinished++;

                                    if( $(`#upload_${fileToUpload.index} .file_progress_bar_container i`).length === 0 )
                                        $('<i>', { class: 'fas fa-check' }).appendTo( $(`#upload_${fileToUpload.index} .file_progress_bar_container`) )

                                    // new item
                                    //this.addNewItemInMediaCollection( { id: response.id, fileName: fileName, extension: fileExtension, type: mediaInfos.type } );

                                    this.showMediaInfoForEdit(mediaInfos);

                                    $('.edit-btn-container').fadeIn();

                                }
                                else
                                {

                                    uploadStateIndicator.html("Encodage en cours ...");

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

                                            $(`#upload_${fileToUpload.index}`).removeClass("valid_download");

                                            //this.addNewItemInMediaCollection( {id: videoEncodingResult.id, fileName: fileName, extension: videoEncodingResult.extension, type: videoEncodingResult.type} );

                                            let videoInfos = {
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
                                            };

                                            this.showMediaInfoForEdit(videoInfos);

                                        }
                                        else
                                        {
                                            $(`#upload_${fileToUpload.index}`).addClass('invalid-download');
                                            uploadStateIndicator.html(`${this.__errors.encode_error}`);
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

                                //$(`.modal-upload-download #upload_${fileToUpload.index} progress`).css({ 'color': 'red' });

                                $(`#upload_${fileToUpload.index} progress`).removeClass("on_upload");
                                $(`#upload_${fileToUpload.index}`).removeClass("unregistered").addClass('invalid_download');
                                $('<i>', { class: 'fas fa-times' }).appendTo( $(`#upload_${fileToUpload.index} .file_progress_bar_container`) );

                                switch (response.responseText)
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
                                        console.log(response.responseText); debugger

                                }

                            },
                            complete: () => {
                                this.__filesToUpload.splice(index , 1);
                            }
                        });

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

    showElementGraphicInfos(elementGraphicInfos, preview)
    {

        return `<td> <p><i class="fas fa-trash-alt cancel-upload" aria-hidden="true"></i> ${ elementGraphicInfos.fileName }</p> </td>
                     <td>
                        <progress class="progress_bar" id="progress_${ elementGraphicInfos.index }" max="100" value="100"></progress>
                        <i class="fas fa-check" aria-hidden="true"></i>
                     </td>
                     <td> 
                        ${ preview } 
                        <i class="fas fa-expand-alt show_expanded_miniature" data-media_id="95" aria-hidden="true"></i>
                     </td>
                     <td> 
                        <input type="hidden" name="medias_list[medias][${elementGraphicInfos.index}][id]" value="${ elementGraphicInfos.id }"> <input type="text" name="medias_list[medias][${elementGraphicInfos.index}][name]" class="form_input fileName" placeholder="Nom du media" value="${ elementGraphicInfos.fileNameWithoutExtension }" required>
                     </td>`;

    }
    
    showMediaInfos(mediaInfos, preview)
    {

        let now = new Date();
        let month = (now.getMonth() + 1);
        month = (month < 10) ? '0' + month : month;
        let day = (now.getDate() < 10 ) ? '0' + now.getDate() : now.getDate();
        let year = now.getFullYear();

        return `<td> 
                    <div class="upload_title_container">
                        <i class="fas fa-trash-alt cancel-upload" aria-hidden="true"></i> 
                        <p> ${ mediaInfos.fileName } <abbr title="${ mediaInfos.fileName }">...</abbr></p> 
                    </div>
                </td>
                <td>
                    <progress class="progress_bar" id="progress_${ mediaInfos.index }" max="100" value="100"></progress>
                    <i class="fas fa-check" aria-hidden="true"></i>
                </td>
                <td> 
                <div class="content_visual"> 
                        ${ preview } 
                        <i class="fas fa-expand-alt show_expanded_miniature" data-media_id="95" aria-hidden="true"></i>
                </div>
                   
                    
                </td>
                <td>
                    <span>${mediaInfos.extension}</span> <br> <span>${mediaInfos.width} * ${mediaInfos.height} px</span> <br> <span>${ (mediaInfos.fileType === 'image') ? mediaInfos.dpi + ' dpi' :  mediaInfos.codec}</span>
                </td>
                <td> <span class="error hidden"></span> <br> <input type="hidden" name="medias_list[medias][${mediaInfos.index}][id]" value="${ mediaInfos.id }"> <input type="hidden" name="medias_list[medias][${mediaInfos.index}][extension]" value="${ mediaInfos.extension }"> <input type="text" name="medias_list[medias][${mediaInfos.index}][name]" class="form_input fileName" placeholder="Nom du media" value="${mediaInfos.fileNameWithoutExtension}" required> </td>
                <td> 
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
                    <button type="button" class="associate-tag association-btn" data-media="${mediaInfos.name}">
                        <span class="mini-cercle"><i class="fas fa-plus" aria-hidden="true"></i></span>TAGS
                    </button>
                    <div class="associated_tags_container">
                        ${ this.buildAssociationInputsHtml('tags', mediaInfos.index) }
                    </div> 
                </td>
                <td class="products_affectation_container"> 
                    <button type="button" class="btn associate-product association-btn" data-media="${mediaInfos.name}">Associer</button>
                    <div class="associated_products_container">
                        ${ this.buildAssociationInputsHtml('products', mediaInfos.index) }
                    </div> 
                </td>
                <td> 
                
                 <div class="submit-rating" id="d">
                        <input type="radio" name="medias_list[medias][${mediaInfos.index}][containIncrustations]" class="form_input media_contain_incruste medias_list " id="medias_list[medias][${mediaInfos.index}][containIncrustations]oui" value="1">
                        <input type="radio" name="medias_list[medias][${mediaInfos.index}][containIncrustations]" class="form_input choice_media_contain_incruste " id="medias_list[medias][${mediaInfos.index}][containIncrustations]non" value="0" checked>
                        
                          <label for="medias_list[medias][${mediaInfos.index}][containIncrustations]non" class="rating-label rating-label-non"><span class="non">Non</span><span class="oui"></span></label>

                         <div class="smile-rating-toggle"></div>
                         <div class="toggle-rating-pill"></div>
                         <label for="medias_list[medias][${mediaInfos.index}][containIncrustations]oui" class="rating-label rating-label-oui">Oui</label>

                         
                    </div>
                </td>`;
    }

    buildAssociationInputsHtml(associationItem, index)
    {
        let inputs = '';
        let counter = 0;

        this.__availableAssociationItems.forEach( (item) => {

            if(item.type === associationItem)
            {

                inputs += `<input type="radio" id="medias_list_medias_${ index }_${ item.type }_${ counter }" name=medias_list[medias][${index}][${ item.type }][]" value="${item.id}"> 
                           <label for="medias_list_medias_${ index }_${ item.type }_${ counter }">${ item.name }</label>`;
                counter++;

            }

        } );

        return inputs;

    }

    showMediaInfoForEdit(mediaInfos)
    {

        //console.log(mediaInfos); debugger

        const index = this.__$fileToCharacterisationList.children('tr').length;

        this.__mediaInfos.push(mediaInfos);

        $(`#upload_${mediaInfos.index} .choice_media`).attr('data-media', mediaInfos.index);

        // show miniatures
        $(`#upload_${mediaInfos.index} .preview_container`).empty();

        let preview = null;

        if(mediaInfos.miniatureExist)
        {

            if(mediaInfos.fileType === 'image')
                preview = `<img class="preview" src="/miniatures/${mediaInfos.customer}/images/low/${mediaInfos.id}.png" alt="/miniatures/${mediaInfos.customer}/image/${mediaInfos.id}.png" />`;

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

    }

    /*rebuildYearFieldContent(index, dateData)
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
    }*/

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

    onMediaInfoEditingFormSubmit(active)
    {
        if(active)
        {

            this.__$location.find('#medias_list_form').on('submit.', e => {

                e.preventDefault();

                let formData = new FormData( $(e.currentTarget)[0] );
                console.log(formData); debugger

                super.showLoadingPopup();

                $.ajax({
                   url: `/mediatheque/${this.__uploadMediaType}`,
                   type: 'POST',
                   data: formData,
                   processData: false,
                   contentType: false,
                   success: (response) => {

                       this.__$fileToCharacterisationList.find('.unregistered').removeClass('unregistered');

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
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).text( this.__errors.duplicate_file );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).removeClass( 'hidden' );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container .form_input.fileName`).addClass('invalid');
                               break;

                           case "516 Invalid Filename":
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).text( this.__errors.invalid_error );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).removeClass( 'hidden' );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container .form_input.fileName`).addClass('invalid');
                               break;

                           case "517 Empty Filename":
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).text( this.__errors.empty_error );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).removeClass( 'hidden' );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container .form_input.fileName`).addClass('invalid');
                               break;

                           case "518 Too short Filename":
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).text( this.__errors.too_short_error );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container span.error`).removeClass( 'hidden' );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_name_container .form_input.fileName`).addClass('invalid');
                               break;

                           case "519 Invalid diffusion date":
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container span.error`).text( this.__errors.invalid_diffusion_date );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container span.error`).removeClass( 'hidden' );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container .diffusionDates`).addClass('invalid');
                               break;

                           case "519.1 Invalid diffusion start date":
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container span.error`).text( this.__errors.invalid_diffusion_start_date );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container span.error`).removeClass( 'hidden' );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container .diffusionDates.start`).addClass('invalid');
                               break;

                           case "519.2 Invalid diffusion end date":
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container span.error`).text( this.__errors.invalid_diffusion_end_date );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container span.error`).removeClass( 'hidden' );
                               this.__$fileToCharacterisationList.find(`tr[data-index='${ subject }'] .media_diff_date_container .diffusionDates.end`).addClass('invalid');
                               break;

                           default:
                               console.error(error.text); //debugger

                       }
                   },
                    complete: () => {
                        super.hideLoadingPopup();
                    },

                });

            })


        }
        else
        {

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

    /*checkMediaNameValidity(mediaName)
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

        if( input.val().match(/(\w)*\.(\w)*!/) && input.attr('type') === "text" )
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

                /!*const mediaId = $(e.currentTarget).data('media_id');
                const index = this.__mediaInfos.findIndex( mediaInfo => mediaInfo.id === mediaId );

                if(index !== -1)
                {
                    const mediaInfo = this.__mediaInfos[index];
                    $('.expand-miniature-container .modal-body').html(`<img src="/miniatures/${mediaInfo.customer}/images/medium/${mediaId}.png" alt="/miniatures/${mediaInfo.customer}/image/medium/${mediaId}.png">`);
                    this.__$location.css({ 'z-index': '0' });
                    $('.expand-miniature-container').fadeIn();
                }*!/

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
            $('.popup_media_expanded_miniature .close_modal_button').on('click.onClickOnMiniatureExpandedPopupCloseBtn', e => {

                /!*this.__$location.css({ 'z-index': '' });
                $('.expand-miniature-container').fadeOut();
                $('.expand-miniature-container .modal-body').empty();*!/

            })
        }
        else
        {
            $('.popup_media_expanded_miniature .close_modal_button').off('click.onClickOnMiniatureExpandedPopupCloseBtn');;
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
    }*/

    enable()
    {
        super.enable();
        /*this.onClickOnUploadButtonShowModal(true)
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
            .onClickOnMiniatureExpandedPopupCloseBtn(true)*/

        this.onClickOnUploadButtonShowModal(true)
            .onClickOnModalCloseButtonsCloseModal(true)
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
        // call function with 'false' for remove events (if event was applied on DOM element by function)
        /*this.onClickOnUploadButtonShowModal(false)
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
            .onClickOnMiniatureExpandedPopupCloseBtn(false)*/

        this.onClickOnUploadButtonShowModal(false)
            .onClickOnModalCloseButtonsCloseModal(false)
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