import Tool from "../../Tool"
import SubTool from "../../SubTool";
import UploadElementGraphicSubTool from "./UploadElementGraphicSubTool/UploadElementGraphicSubTool";
import UploadMediaDiffSubTool from "./UploadMediaDiffSubTool/UploadMediaDiffSubTool";
import UploadVideoSynchroSubTool from "./UploadVideoSynchroSubTool/UploadVideoSynchroSubTool";
import UploadVideoThematicSubTool from "./UploadVideoThematicSubTool/UploadVideoThematicSubTool";


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

        this.__subTools = [
            new UploadElementGraphicSubTool(),
            new UploadMediaDiffSubTool(),
            new UploadVideoSynchroSubTool(),
            new UploadVideoThematicSubTool(),
        ];

        this.__currentUploadManager = null;

        // W3C authors recommend to specify both MIME-types and corresponding extensions in input type file "accept" attribute
        // @see : https://html.spec.whatwg.org/multipage/input.html#attr-input-accept
        this.__authorizedFiles = {

            medias: [
                'image/jpg', 'image/jpeg', 'image/png', 'image/bmp', 'image/gif', 'image/x-windows-bmp', 'image/pjpeg', 'image/svg+xml',
                '.jpg', '.jpeg', '.png', '.bmp', '.gif', '.svg', 'video/*', 'video/mp4', 'video/avi', 'video/x-matroska', 'video/3gpp', 'video/quicktime',
                '.mp4', '.avi', '.3gp'
            ],

            video: [
                'video/*', 'video/mp4', 'video/avi', 'video/x-matroska', 'video/3gpp', 'video/quicktime', '.mp4', '.avi'
            ]

            /*element_graphic: [

            ],*/

        };

        this.__errors = {

            bad_extension: "Ce type de fichier n'est pas accepté",

            bad_resolution: "Cette résolution n'est pas accepté",

            corrupt_file: "Fichier corrompu",

            duplicate_file: "Ce nom est déjà utilisé !",

            unauthorized_characters: "Ce champ contient des caractères non autorisés !",

            invalid_error: "Ce champ n'est pas valide !",

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
        if(this.isActive() && ( this.__uploadMediaType === null || typeof this.__uploadMediaType === 'undefined' || this.__uploadMediaType === 'undefined') )
        {
            console.error("Error : cannot found data-media_displayed on element : .main-media !"); //debugger
        }

        let type = this.__uploadMediaType;

        if(this.__uploadMediaType === 'video_synchro' || this.__uploadMediaType === 'video_thematic')
        {

            this.__uploadMediaType = (this.__uploadMediaType === 'video_synchro') ? 'synchros' : 'thematics';

            $(".default_upload_input").attr("accept", this.__authorizedFiles[ 'video' ]);
        }

        else
            $(".default_upload_input").attr("accept", this.__authorizedFiles[ 'medias' ]);

        this.__total_files_allowed = 50;
        this.__max_file_size = 524288000;
        this.__filesToUpload = [];
        this.__mediaInfos = [];
        this.__authorized_char_regex = /^[a-zA-Z0-9_.-\s*]*$/;
        this.__uploadAuthorized = false;
        this.__$mediasCollection = $('.medias_collection');

        this.__availableAssociationItems = [];

        this.__encodedMediaInfos = [];

        this.getAllAvailableAssociationItems();

        this.__filesSelectedSize = 0;

        //this.showStep(2)

    }

    getLocation()
    {
        return this.__$location;
    }

    activeUploadSubTool()
    {

        switch (this.__uploadMediaType)
        {

            case "medias":
                this.activeSubTool("UploadMediaDiffSubTool");
                break;

            case "synchros":
                this.activeSubTool("UploadVideoSynchroSubTool");
                break;

            case "thematics":
                this.activeSubTool("UploadVideoThematicSubTool");
                break;

            case "element_graphic":
                this.activeSubTool("UploadElementGraphicSubTool");
                break;

            default:
                throw new Error(`Error : Unrecognized media type (${ this.__uploadMediaType }) !`);
        }

        return this;
    }

    disableUploadSubTool()
    {

        this.__subTools.map( subTool => {

            subTool.disable();

        } )

    }

    activeSubTool(subToolName, subToolToolsToActive = [])
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        this.__currentUploadManager = this.__subTools[ this.getSubToolIndex(subToolName) ];
        //console.log(this.__parent); debugger
        this.__subTools[ this.getSubToolIndex(subToolName) ].setToolBox(this.getToolBox());
        this.__subTools[ this.getSubToolIndex(subToolName) ].setParent(this);
        this.__subTools[ this.getSubToolIndex(subToolName) ].enable();
        //console.log(this.__currentUploadManager ); debugger
        return this;
    }

    disableSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        this.__subTools[ this.getSubToolIndex(subToolName) ].disable();
        this.__currentUploadManager = null;

        return this;

    }

    getSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        return this.__subTools[ this.getSubToolIndex(subToolName) ];

    }

    subToolIsRegistered(subToolName)
    {
        return this.getSubToolIndex( subToolName ) !== -1;
    }

    getSubToolIndex(subToolName)
    {
        return this.__subTools.findIndex( subTool =>  subTool.getName() === subToolName );
    }

    getAllAvailableAssociationItems()
    {

        $('.popup_associate_product .products_choice_list tr').each( (index,tr) => {

            this.__availableAssociationItems.push( { id: $(tr).data('product_id'), name: $(tr).find('.product_name').text(), criterions: $(tr).find('.criterions_col .cirterions_content').html(), type: 'products' } );

        } );

        $('.popup_associate_tag .tag_list tr').each( (index,tr) => {

            this.__availableAssociationItems.push( { id: $(tr).data('tag_id'), name: $(tr).find('.tag_name').text(), style: $(tr).find('.mini-cercle').attr('style'), type: 'tags' } );

        } );

        return this;

    }

    buildAssociationInputsHtml(associationItem, index)
    {
        let inputs = '';
        let counter = 0;

        this.__availableAssociationItems.forEach( (item) => {

            if(item.type === associationItem)
            {

                let dataAttributes = (item.type === 'tags' || item.type === 'tag') ? `data-style="${ item.style }"` : "";

                console.log(dataAttributes); //debugger

                inputs += `<input type="checkbox" id="medias_list_medias_${ index }_${ item.type }_${ counter }" ${ dataAttributes } name=medias_list[medias][${index}][${ item.type }][]" value="${item.id}"> 
                           <label for="medias_list_medias_${ index }_${ item.type }_${ counter }">${ item.name }</label>`;
                counter++;

            }

        } );

        return inputs;

    }

    getProductCriterions(productName)
    {

        let criterions = "";

        this.__availableAssociationItems.forEach( (item) => {

            if(item.name === productName)
            {
                criterions = item.criterions;
                return null;
            }

        } );

        return criterions;
    }

    buildProductCriterionsContainer( productName )
    {

        let criterions = $( this.getProductCriterions(productName) );
        console.log(criterions); debugger
    }

    buildAvailableProductsCriterionsContainer()
    {

        let containers = [];

        this.__availableAssociationItems.forEach( (item) => {

            if(item.type === "products" || item.type === "product")
            {

                let container = `<div class="criterions_container invisible" data-product="${ item.id }"> ${ item.criterions } </div>`;

                containers.push( container );

            }

        } );

        //console.log(containers); debugger

        return containers;

    }

    fileMimeTypeIsAccepted(mime_type)
    {
        // search mime_type in authorized extension using upload current tab (image, video, video synchro, ...)
        //return this.__authorizedFiles[this.__uploadMediaType].indexOf(mime_type) !== -1;

        if(this.__uploadMediaType === 'synchros' || this.__uploadMediaType === 'thematics')
            return this.__authorizedFiles['video'].indexOf(mime_type) !== -1;

        return this.__authorizedFiles['medias'].indexOf(mime_type) !== -1;
    }

    fileIsAlreadyUploaded(file)
    {

        //this.__$location.find('.checking_file_existing_helper').removeClass('hidden');

        super.changeLoadingPopupText("Vérification de l'existance du ou des média(s)...");
        super.showLoadingPopup();

        return new Promise( (resolve, reject) => {

            $.ajax({
                type: 'post',
                url: '/file/is/uploaded',
                data: { file: file },
                success: (response) => {
                    resolve(parseInt(response) === 0);
                },
                error: (response, status, error) => {
                    console.error(response); debugger;
                    resolve(true);
                },
                complete: () => {

                }
            });

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

        //console.log(item); //debugger
        //console.log(fileNameWithoutExtension); //debugger

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

            let newUploadFileItem = $(`<tr>`, {
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
                this.__filesToUpload.push( { id: null, index: this.__filesToUpload.length, name: fileName, nameWithoutExtension: fileNameWithoutExtension, extension: fileExtension, file: item} );

                if(this.__uploadMediaType === 'synchros')
                {
                    this.__currentUploadManager.saveSynchroElement( { name: fileNameWithoutExtension } );
                }

                //console.log("new element added in upload list")
                //console.table(this.__filesToUpload); //debugger
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

                if(this.__uploadMediaType === 'synchros' && this.__$location.find(`.file_to_upload_list tr.valid_upload_item`).length < 2)
                {
                    this.__uploadAuthorized = false;
                    this.__$location.find('.pre_upload_error').text(`Vous devez uploader au moins 2 videos !`).parent().removeClass('hidden');
                    this.__$location.find('.start_upload_btn').attr('disabled', true);
                }
                else
                {
                    this.__uploadAuthorized = true;
                    this.__$location.find('.pre_upload_error').empty().parent().addClass('hidden');
                    //this.__$location.find('.start_upload_btn').removeAttr('disabled');
                }

            }

            if(this.__$location.find(`.file_to_upload_list tr`).length >= this.__filesSelectedSize)
            {
                //this.__$location.find('.checking_file_existing_helper').addClass('hidden');
                super.hideLoadingPopup();
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
                this.__parent.__popupIsOpen = true;

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
        this.__parent.__popupIsOpen = false;

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

                if(this.__$fileToCharacterisationList.find('tr progress.on_upload').length > 0)
                {

                    if(confirm("Certains de vos téléchargement ne sont pas terminés ! En continuant, vos devrez recommencer les téléchargement en cours. Etes-vous sûr de vouloir continuer ?"))
                    {
                        // @TODO: abort download or send ajax to server for delete media which is not finish

                        console.log( this.__filesToUpload ); debugger

                        this.closeModal();
                    }
                }

                else if(this.__$fileToCharacterisationList.find('tr.unregistered').length > 0)
                {
                    if(confirm("Vous n'avez pas enregistrés toutes vos modifications ! En continuant, vos perderez vos modifications. Etes-vous sûr de vouloir continuer ?"))
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
                //console.log(`${e.originalEvent.dataTransfer.files.length} File(s) dropped !`);
                let droppedFiles = e.originalEvent.dataTransfer.files;

                //console.table(droppedFiles); //debugger

                this.__filesSelectedSize = droppedFiles.length;

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

                this.__filesSelectedSize = filesSelected.length;

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
                    //console.log("upload element removed !");
                    //console.log(this.__filesToUpload.length);

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
                            <button class="btn save_edits_button" type="button" >Enregistrer</button>
                        </div>`;

            }
            else
            {

                this.__$location.find('.upload_step_title').text("Médias prêts");

                html = `
                    <div class="actions_buttons_container">
                        <button class="btn show_prev_step" type="button"><i class="fas fa-arrow-left"></i>Précedent</button>
                    </div>`;

                if(this.__uploadMediaType === 'synchros')
                {
                    html += `
                    
                        <div class="actions_buttons_container"> 
                            <button class="btn save_synchro_edits_button" type="button">Enregistrer</button> 
                        </div>
                    
                    `;
                }

                html += `
                
                <div class="actions_buttons_container">
                    <button class="btn" type="button">Remplacer un média</button>
                </div>
                
                <div class="actions_buttons_container">
                    <button class="btn" type="button"><i class="far fa-calendar"></i>Programmer</button>
                </div>

                <div class="actions_buttons_container">
                    <button class="btn" type="button" disabled><i class="icon icon-picto-mediathque"></i>Médiathèque</button>
                </div>
                
                `;

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
                    html = `<tr data-index="${ index }" id="upload_${index}" class="">
                                <td class="file-name-container uploaded-file-name-col">
                                    <p title="${fileToUpload.name}"><i class="fas fa-trash-alt cancel-upload"></i>${fileToUpload.name}</p>
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
                                
                            </tr>`;
                }
                else
                {

                    html = `<tr data-index="${ index }" id="upload_${index}" class="${ (this.__uploadMediaType === 'synchros') ? 'waiting_encode unregistered' : '' }">
                                <td>
                                    <p title="${fileToUpload.name}"><i class="fas fa-trash-alt cancel_upload" aria-hidden="true"></i>${fileToUpload.name}</p>
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

                    if(this.__uploadMediaType === 'sync' && this.__filesToUpload.length < 2)
                        throw new Error("Vous devez uploader au moins 2 videos !");

                    this.__$fileToUploadList.empty();

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

                            const currentUpload = this.__$fileToCharacterisationList.find(`#upload_${fileToUpload.index}`);
                            const currentUploadProgressBar = this.__$fileToCharacterisationList.find(`#upload_${fileToUpload.index} progress`);

                            let formData = new FormData();
                            formData.append('file', fileToUpload.file);
                            formData.append('media_type', this.__uploadMediaType);

                            const fileExtension = fileToUpload.file.name.split('.').pop();
                            const fileName = fileToUpload.file.name.replace( '.' + fileExtension , '');
                            //console.log(this.__currentUploadManager.getSynchroElementByName( fileName )); debugger
                            if(this.__uploadMediaType === 'synchros')
                                formData.append('synchro', this.__currentUploadManager.getSynchroElementByName( fileName ))

                            fileToUpload.xhr = $.ajax({
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
                                            currentUploadProgressBar.addClass("on_upload").attr("value", percent);

                                            uploadStateIndicator.html(`Téléchargement en cours ... (${percent}%)`);
                                            //jQuery('#progress' + (index + 1) + ' .progress-bar').css("left", +percent + "%");

                                            if(percent === 100 && this.__uploadMediaType === 'video')
                                                uploadStateIndicator.html("Encodage en cours ...");

                                            else if(percent === 100 && this.__uploadMediaType !== 'video')
                                                uploadStateIndicator.html("Veuillez patienter ...");

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

                                        // dans le cas des videos, on attend la fin de l'encodage pour envoyer les infos au subTool
                                        if(response.fileType !== 'video')
                                        {

                                            fileToUpload.id = response.id;

                                            currentUpload.addClass('upload_finished');

                                            //this.__currentUploadManager.saveMediaInfos(response);
                                            response.index = fileToUpload.index;
                                            //this.__currentUploadManager.showMediaInfoForEdit(response);
                                            this.__$fileToCharacterisationList.find(`#upload_${response.index}`).html( $(this.__currentUploadManager.showMediaInfoForEdit(response)) );
                                            //this.showMediaInfoForEdit(response, fileToUpload.index);

                                            if(this.__uploadMediaType !== 'synchros')
                                                this.__currentUploadManager.buildMediaCard(response);

                                        }

                                        else
                                        {

                                            uploadStateIndicator.html("Encodage en cours ...");

                                            let videoEncodingResult = {};

                                            let intervalId = setInterval( async() => {

                                                videoEncodingResult = await this.checkVideoEncodingStatus(response.id, fileToUpload.index);
                                                if( videoEncodingResult.status === 'Finished' )
                                                {
                                                    clearInterval(intervalId);

                                                    fileToUpload.id = videoEncodingResult.id;

                                                    currentUpload.addClass('upload_finished');

                                                    currentUploadProgressBar.removeClass("on_upload");
                                                    uploadFinished++;

                                                    console.log(videoEncodingResult); //debugger

                                                    //currentUpload.attr('data-orientation', videoEncodingResult.orientation);

                                                    if( currentUploadProgressBar.parents('.file_progress_bar_container').find('i').length === 0 )
                                                        $('<i>', { class: (typeof videoEncodingResult.error !== "undefined" && videoEncodingResult.error === "" && videoEncodingResult.error === null) ? 'fas fa-times' : 'fas fa-check' }).appendTo( $(`#upload_${fileToUpload.index} .file_progress_bar_container`) )

                                                    if(typeof videoEncodingResult.error === "undefined" || videoEncodingResult.error === "" || videoEncodingResult.error === null)
                                                    {

                                                        /*currentUpload.removeClass("valid_download");*/

                                                        videoEncodingResult.index = fileToUpload.index;

                                                        if( this.__uploadMediaType === 'synchros' )
                                                        {

                                                            console.log("synchros"); //debugger

                                                            currentUpload.removeClass("waiting_encode");

                                                            this.__encodedMediaInfos.push( videoEncodingResult );

                                                            if( this.__$location.find('.file_to_characterisation_list tr.waiting_encode').length === 0 )
                                                            {

                                                                console.log("all files was encoded"); //debugger

                                                                this.__currentUploadManager.updateSynchroElements( this.__encodedMediaInfos );

                                                                if(this.__$location.find('.file_to_characterisation_list tr').length === 1)
                                                                    //this.showMediaInfoForEdit(videoEncodingResult, fileToUpload.index);
                                                                    this.__currentUploadManager.showMediaInfoForEdit(this.__encodedMediaInfos);

                                                                else
                                                                {

                                                                    console.log("get all"); //debugger

                                                                    //this.__currentUploadManager.showMediaInfoForEdit(this.__encodedMediaInfos);

                                                                    this.__currentUploadManager.showMediaInfoForEdit(this.__encodedMediaInfos);

                                                                    //this.__$fileToCharacterisationList.find(`#upload_${videoEncodingResult.index}`).replaceWith( $(this.__currentUploadManager.showMediaInfoForEdit(videoEncodingResult)) );

                                                                    /*this.__encodedMediaInfos.forEach( encodedMediaInfos => {

                                                                        //console.table(encodedMediaInfos); //debugger

                                                                        this.showMediaInfoForEdit(encodedMediaInfos, encodedMediaInfos.index);

                                                                    } )*/

                                                                }
                                                            }
                                                            else
                                                            {
                                                                console.log("wait"); //debugger

                                                                //this.__encodedMediaInfos.push( videoEncodingResult );

                                                                uploadStateIndicator.html("En attente du traitement des autres videos...");
                                                            }

                                                        }
                                                        else
                                                        {
                                                            //this.showMediaInfoForEdit(videoEncodingResult, fileToUpload.index);
                                                            //this.__currentUploadManager.showMediaInfoForEdit(videoEncodingResult);

                                                            this.__$fileToCharacterisationList.find(`#upload_${videoEncodingResult.index}`).html( $(this.__currentUploadManager.showMediaInfoForEdit(videoEncodingResult)) );

                                                        }

                                                        if(this.__$location.find('.file_to_characterisation_list tr.upload_finished').length > 0)
                                                            this.__$location.find('.save_edits_button').removeAttr('disabled');

                                                    }
                                                    else
                                                    {
                                                        currentUpload.addClass('invalid-download');
                                                        uploadStateIndicator.html(`${this.__errors.encode_error} : ${ videoEncodingResult.error }`);

                                                        if(this.__uploadMediaType === "synchros")
                                                        {

                                                            this.__$fileToCharacterisationList.find(`.upload_state`).each( (index, element) => {

                                                                if( $(element).parents('tr').attr('id') !== `upload_${fileToUpload.index}` )
                                                                {
                                                                    $(element).text("Erreur(s) sur 1 ou plusieurs vidéos de la synchros !");
                                                                }

                                                            } )

                                                            this.__currentUploadManager.notifyServerToDeleteSynchroElements();

                                                        }

                                                    }

                                                }


                                            }, 15000 )

                                        }

                                    }
                                    else
                                    {

                                        $(`#upload_${fileToUpload.index} .cancel-upload`).fadeIn();

                                        //$(".modal-upload-download .show_media_edit_container").fadeOut();

                                        //$(`.modal-upload-download #upload_${fileToUpload.index} progress`).css({ 'color': 'red' });

                                        currentUploadProgressBar.removeClass("on_upload");
                                        currentUpload.removeClass("unregistered").addClass('invalid_upload');
                                        $('<i>', { class: 'fas fa-times' }).appendTo( currentUploadProgressBar.parents('.file_progress_bar_container') );

                                        switch (response.error)
                                        {

                                            case "Bad Extension":
                                                uploadStateIndicator.html(`${this.__errors.bad_extension}`);
                                                break;

                                            case "Bad Resolution":
                                                uploadStateIndicator.html(`${this.__errors.bad_resolution}`);
                                                break;

                                            case "Corrupt File":
                                                uploadStateIndicator.html(`${this.__errors.corrupt_file}`);
                                                break;

                                            case "Duplicate File":
                                                uploadStateIndicator.html(`${this.__errors.duplicate_file}`);
                                                break;

                                            case "Invalid Filename":
                                                // <i class='fas fa-times'></i>
                                                uploadStateIndicator.html(`${this.__errors.unauthorized_characters}`);
                                                break;

                                            case "Empty Filename":
                                                uploadStateIndicator.html(`${this.__errors.empty_error}`);
                                                break;

                                            case "Too short Filename":
                                                uploadStateIndicator.html(`${this.__errors.too_short_error}`);
                                                break;

                                            case "Bad ratio":
                                                uploadStateIndicator.html(`${this.__errors.bad_ratio}`);
                                                break;

                                            case "Invalid File type":
                                                uploadStateIndicator.html(`${this.__errors.bad_extension}`);
                                                break;

                                            default:
                                                uploadStateIndicator.html("Téléchargement annulé suite à une erreur interne !");
                                                console.log(response.error); debugger

                                        }

                                    }

                                } )

                            ;

                        }

                    } )

                    if( this.__$fileToCharacterisationList.find('.on_upload').length === 0 )
                        this.__$location.find('.start_upload_btn').removeAttr('disabled');

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

        return `<td> 
                    <p title="${ elementGraphicInfos.fileName }"><i class="fas fa-trash-alt cancel-upload" aria-hidden="true"></i> ${ elementGraphicInfos.fileName }</p> 
                </td>
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
                    <span class="error hidden"></span> <br>
                    <input type="text" name="medias_list[medias][${elementGraphicInfos.index}][name]" class="form_input media_name" placeholder="Nom du media" value="${ elementGraphicInfos.fileNameWithoutExtension }" required>
                 </td>
                 <td class="associated_criterions_container">
                
                </td>
                <td class="products_affectation_container"> 
                    <button type="button" class="btn product_association_btn association_btn">Associer produits</button>
                    <div class="associated_products_container">
                        ${ this.buildAssociationInputsHtml('products', elementGraphicInfos.index) }
                    </div> 
                </td>`;

    }

    addNewMediaCardInMediatheque(mediaCards)
    {

        mediaCards.map( mediaCard => {

            $(mediaCard).appendTo(this.__parent.getMediasContainer());

        } )



    }

    reformateDate(date, onlyDate = false, dateSeparator = '-', clockSeparator = ':')
    {

        date = new Date(date);
        date.setMonth( date.getMonth() +1 );

        const year = date.getFullYear();
        const month = ( date.getMonth() < 10 ) ? '0' + date.getMonth() : date.getMonth();
        const day= date.getUTCDate();
        const hour = (date.getHours() < 10) ? '0' + date.getHours() : date.getHours();
        const minutes = (date.getMinutes() < 10) ? '0' + date.getMinutes() : date.getMinutes();
        const second = (date.getSeconds() < 10) ? '0' + date.getSeconds() : date.getSeconds();

        if(!onlyDate)
        {
            return year + dateSeparator + month + dateSeparator + day + ' ' + hour + clockSeparator + minutes + clockSeparator + second;
        }
        else
        {
            return year + dateSeparator + month + dateSeparator + day;
        }

    }

    getDaysDiffBetweenDates(date1, date2)
    {
        date1 = ( date1 instanceof Date) ? date1 : new Date(date1);
        date2 = ( date2 instanceof Date) ? date2 : new Date(date2);
        const diffTime = Math.abs(date1 - date2);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }

    onClickOnSaveButton(active)
    {
        if(active)
        {
            this.__$location.find('.popup_footer').on('click.onClickOnSaveButton', '.save_edits_button', e => {

                if( this.__$fileToCharacterisationList.find('.on_upload').length === 0 )
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
            else if( $(input).hasClass('media_name') && form.find(`input.media_name[value='${ $(input).val() }']`).length > 1 )
            {
                isValid = false;
                form.find(`input.media_name[value='${ $(input).val() }']`).addClass('invalid');
                form.find(`input.media_name[value='${ $(input).val() }']`).parent().find('span.error').text( this.__errors.duplicate_file ).removeClass('hidden');
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
            else if( $(input).hasClass('media_thematic') && ( isNaN(parseInt($(input).val(), 10)) || (parseInt($(input).val(), 10) === 0) ) )
            {
                isValid = false;
                $(input).addClass('invalid');
                inputFirstParent.find('span.error').text( this.__errors.invalid_error ).removeClass('hidden');
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

                if( this.mediaInfosEditFormIsValid( $(e.currentTarget) ) && this.__$fileToCharacterisationList.find('tr').length > 0 )
                {

                    super.changeLoadingPopupText("Enregistrement en cours...");
                    super.showLoadingPopup();

                    $.ajax({
                        //url: `/mediatheque/${this.__uploadMediaType}`,
                        url: `/save/upload/medias/infos`,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: (response) => {

                            //console.log(response); //debugger

                            if(response.errors.length > 0)
                            {

                                response.errors.forEach( (error) => {

                                    switch (error.text)
                                    {

                                        case "Duplicate File":
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_name_container span.error`).text( this.__errors.duplicate_file ).removeClass( 'hidden' );
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .form_input.media_name`).addClass('invalid');
                                            break;

                                        case "Invalid Filename":
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_name_container span.error`).text( this.__errors.unauthorized_characters ).removeClass( 'hidden' );
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_name_container .form_input.media_name`).addClass('invalid');
                                            break;

                                        case "Empty Filename":
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_name_container span.error`).text( this.__errors.empty_error ).removeClass( 'hidden' );
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_name_container .form_input.media_name`).addClass('invalid');
                                            break;

                                        case "Too short Filename":
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_name_container span.error`).text( this.__errors.too_short_error ).removeClass( 'hidden' );
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_name_container .form_input.media_name`).addClass('invalid');
                                            break;

                                        case "Invalid diffusion date":
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_diff_date_container span.error`).text( this.__errors.invalid_diffusion_date ).removeClass( 'hidden' );
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_diff_date_container .diffusion_dates`).addClass('invalid');
                                            break;

                                        case "Invalid diffusion start date":
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_diff_date_container span.error`).text( this.__errors.invalid_diffusion_start_date ).removeClass( 'hidden' );
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_diff_date_container .diffusion_dates.start`).addClass('invalid');
                                            break;

                                        case "Invalid diffusion end date":
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_diff_date_container span.error`).text( this.__errors.invalid_diffusion_end_date ).removeClass( 'hidden' );
                                            this.__$fileToCharacterisationList.find(`tr[data-index='${ error.subject }'] .media_diff_date_container .diffusion_dates.end`).addClass('invalid');
                                            break;

                                        default:
                                            console.error(error); debugger

                                    }

                                } )

                                this.__$fileToCharacterisationList.find('.unregistered').forEach( element => {

                                    if( $(element).find('.invalid') < 0 )
                                    {
                                        $(element).removeClass('unregistered');
                                    }

                                } );

                                this.__$location.find('.popup_footer .save_edits_button').attr('disabled', true);

                                if(this.__uploadMediaType !== 'synchros')
                                    this.__currentUploadManager.buildMediaCard(response);

                            }
                            else
                            {
                                this.__$fileToCharacterisationList.find('.unregistered').removeClass('unregistered');

                                response.medias.forEach( (media, index) => {

                                    this.__$fileToCharacterisationList.find(`tr[data-index='${ index }'] .media_id`).val( media.id );

                                    /*if( this.__$fileToCharacterisationList.find(`tr[data-index='${ index }'] video`).length > 0 )
                                    {
                                        let newSrc = this.__$fileToCharacterisationList.find(`tr[data-index='${ index }'] .preview source`).attr('src').replace( this.__$fileToCharacterisationList.find(`tr[data-index='${ index }'] .media_name`).val(), media.id );
                                        this.__$fileToCharacterisationList.find(`tr[data-index='${ index }'] .preview source`).attr('src', newSrc);

                                        this.__$fileToCharacterisationList.find(`tr[data-index='${ index }'] .preview`)[0].load();
                                    }
                                    else
                                    {
                                        let newSrc = this.__$fileToCharacterisationList.find(`tr[data-index='${ index }'] .preview`).attr('src').replace( this.__$fileToCharacterisationList.find(`tr[data-index='${ index }'] .media_name`).val(), media.id );
                                        this.__$fileToCharacterisationList.find(`tr[data-index='${ index }'] .preview`).attr('src', newSrc);
                                    }*/

                                } )

                                if(this.__uploadMediaType !== 'synchros')
                                    this.__currentUploadManager.buildMediaCard(response);

                                this.showMediaEditingResume();
                            }

                        },
                        error: (response) => {

                            alert("Erreur interne !");

                            console.log(response); debugger

                        },
                        complete: () => {
                            super.hideLoadingPopup();

                            //this.__filesToUpload = [];
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

    showMediaEditingResume()
    {

        this.__currentUploadManager.showMediaEditingResume();

        /*let html = "";

        this.__$location.find('.file_to_characterisation_list tr').each( (index, element) => {

            const preview = $(element).find('.preview')[0].outerHTML;

            html += `

                    <tr>

                        <td>
                            <input type="checkbox" class="choice_media">
                            ${ preview }
                        </td>

                        <td>
                            <div class="media_name_container"> <p>${ $(element).find('.media_name').val() }</p> </div>
                        </td>

                        <td>
                            <div> ${ $(element).find('.associated_criterions_container').html() } </div>
                        </td>

                `;


            if(this.__uploadMediaType !== 'element_graphic')
                html += `<td> ${ $(element).find('.associated_tags_container').html() } </td>`;

            html += `<td> ${ $(element).find('.associated_products_container').html() } </td>`;

            if(this.__uploadMediaType === 'medias')
            {

                const mediaContaintIncrustation = parseInt($(element).find('.form_input.media_contain_incruste:checked').val());

                html += `<td> <div class="redirect_to_module_incruste_btn_container"> <button type="button" class="btn redirect_to_module_incruste_btn" ${ (mediaContaintIncrustation === 0) ? 'disabled' : '' }>Incruster PRIX</button> </div> </td>`;

            }

            html += "</tr>";

        } )

        this.__$location.find('.step_3 .media_characterisation_resume_list').html( html );*/

        this.showStep(3);

    }

    onClickOnPreviousButton(active)
    {
        if(active)
        {
            this.__$location.find('.popup_footer').on('click.onClickOnPreviousButton', '.show_prev_step', e => {

                const currentStep = parseInt($('.step.current_step').data('step'), 10);

                if( !isNaN(currentStep) && currentStep > 2)
                    this.showStep( currentStep -1 )

            })
        }
        else
        {
            this.__$location.find('.step').off('click.onClickOnPreviousButton', '.show_prev_step');
        }

        return this;
    }

    onClickOnNextButtonShowMediasEditingResume(active)
    {

        if(active)
        {
            this.__$location.on('click.onClickOnNextButtonShowMediasEditingResume', '.show_media_editing_resume', e => {

                this.showMediaEditingResume();

            })
        }
        else
        {
            this.__$location.off('click.onClickOnNextButtonShowMediasEditingResume', '.show_media_editing_resume');
        }

        return this;
    }

    handleMediaNameChange(active)
    {
        if(active)
        {
            this.__$fileToCharacterisationList.on('input.handleMediaNameChange', '.media_name', e => {

                $(e.currentTarget).parents('tr').addClass('unregistered');

                $(e.currentTarget).attr('value', $(e.currentTarget).val());

            })
        }
        else
        {
            this.__$fileToCharacterisationList.off('input.handleMediaNameChange', 'tr .media_name');
        }

        return this;
    }

    onClickOnMediaDeleteButton(active)
    {

        if(active)
        {
            this.__$location.on('click.onClickOnMediaDeleteButton', '.step_2 .cancel_upload', async(e) => {

                let deleteBtn = $(e.currentTarget);
                let deleteBtnParent = $(deleteBtn).parents('tr');
                let index = parseInt( deleteBtnParent.data('index') );

                if( !isNaN(index) && confirm("Etes-vous sûr de vouloir supprimer le média sélectionné ?") )
                {

                    /*console.log( this.__filesToUpload[index] );
                    console.log( this.__filesToUpload ); debugger*/

                    let mediaInfos = {};

                    if( deleteBtnParent.find('progress.on_upload').length > 0 )
                    {
                        this.__filesToUpload[index].xhr.abort();
                        mediaInfos = { name: this.__filesToUpload[index].nameWithoutExtension };
                    }
                    else
                    {
                        mediaInfos = { id: this.__filesToUpload[index].id };
                    }

                    let mediaIsDeleted = await this.notifyServerToDeleteMedia(mediaInfos, deleteBtnParent);

                    if( mediaIsDeleted )
                    {

                        //console.log( this.__$fileToUploadList.find(`tr.valid_upload_item .file_name:contains('${ this.__filesToUpload[index].name }')`) ); debugger
                        this.__$fileToUploadList.find(`tr.valid_upload_item .file_name:contains('${ this.__filesToUpload[index].name }')`).remove();

                        if( this.__filesToUpload[index].id !== null )
                            $(`#card_${ this.__filesToUpload[index].id }`).remove();

                        this.__filesToUpload.splice( index, 1 );

                        if( this.__$fileToCharacterisationList.find('tr').length === 0 )
                        {
                            this.__$fileToUploadList.empty();
                            this.__$location.find('.save_edits_button').attr('disabled', true);
                        }

                    }

                }

            })
        }
        else
        {
            this.__$location.off('click.onClickOnMediaDeleteButton', '.step_2 .cancel_upload')
        }

        return this;
    }


    async notifyServerToDeleteMedia(mediaInfos = { id: null, name: null }, mediaContainer)
    {

        //console.log(mediaInfos); debugger

        if( (typeof mediaInfos !== 'object') )
            throw new Error(`Invalid parameter given to ${this.__name}.notifyServerToDeleteMedia() ! Parameter must be object but '${ (typeof mediaInfos) }' given`);

        let url = "";

        if( (typeof mediaInfos.id !== "undefined" && mediaInfos.id !== null ) )
            url = `/remove/media/${ mediaInfos.id }`;

        else if( (typeof mediaInfos.name !== "undefined" && mediaInfos.name !== null ) )
            url = `/remove/media`;

        else
            throw new Error(`Invalid parameter given to ${this.__name}.notifyServerToDeleteMedia() ! Parameter must be object and contain id or name !`);

        super.changeLoadingPopupText("Suppression du média...")
        super.showLoadingPopup();

        //let mediaIsDeleted = false;

        return new Promise( (resolve, reject) => {

            $.ajax({
                url: url,
                type: "POST",
                data: (url === `/remove/media`) ? { mediaName: mediaInfos.name } : {},
                success: (response) => {

                    if(response.status === "200 OK")
                    {
                        mediaContainer.remove();
                        //mediaIsDeleted = true;
                        resolve(true);
                    }
                    else
                    {
                        //console.log(response); debugger
                        alert(`Erreur durant la suppression du media`);
                        resolve(false);
                    }

                },
                error: (response, status, error) => {

                    //console.error(response); debugger
                    alert(`Erreur durant la suppression du media`)
                    resolve(false);
                },
                complete: () => {

                    super.hideLoadingPopup();

                }
            });

        } )


        //return mediaIsDeleted;
    }

    enable()
    {
        super.enable();

        this.activeUploadSubTool();

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
            .onClickOnNextButtonShowMediasEditingResume(true)
            .handleMediaNameChange(true)
            .onClickOnMediaDeleteButton(true)
        ;
    }

    disable()
    {
        super.disable();

        this.disableUploadSubTool();

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
            .onClickOnNextButtonShowMediasEditingResume(false)
            .handleMediaNameChange(false)
            .onClickOnMediaDeleteButton(false)
        ;
    }

}

export default UploadHandlerTool;