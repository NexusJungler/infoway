import Tool from "../../../Tool";
import SubTool from "../../../SubTool";

class MediaProductAssociationHandlerTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name; // get class name dynamically
        this.__$productsList = $('.popup_associate_product .products_choice_list');
        this.__$associatedList = $('.popup_associate_product .products_associated_list');
        this.__$mediasCollection = $('.medias_collection');
        this.__$container = $('.popup_associate_product_container');
        this.__$location = $('.popup_associate_product');
        this.__currentMediaName = null;
        this.__currentMediaId = null;
        this.__currentMediaTags = null;
        this.__currentPos = null;
        this.__mediasAssociationInfo = [];
        this.__isUpload = false;
    }

    addItemInAssociatedList(item)
    {

        if(this.__$associatedList.find(`tr[data-product_id="${ item.productId }"]`).length > 0)
        {

            this.__$productsList.find(`tr[data-product_id='${ item.productId }'] .choice_product`).prop('checked', true);

            this.__$associatedList.find(`tr[data-product_id="${ item.productId }"]`).removeClass('dissociated').removeClass('new_association');
            this.__$associatedList.find(`tr[data-product_id="${ item.productId }"] .dissociation_btn_container button`).show();

        }

        else if( this.__$associatedList.find(`tr[data-product_id="${ item.productId }"]`).length === 0 )
        {

            let registeredMediaInfosIndex = this.__mediasAssociationInfo.findIndex( mediaInfo =>  mediaInfo.media === this.__currentMediaName );

            if(registeredMediaInfosIndex !== -1)
            {

                this.__$productsList.find(`tr[data-product_id='${ item.productId }'] .choice_product`).prop('checked', true);

                let newLine = $(`<tr data-product_id="${ item.productId }" data-product_criterions="${ item.productCriterions }"></tr>`);

                let mediaProductsIds = this.__mediasAssociationInfo[registeredMediaInfosIndex].products;

                if(!mediaProductsIds.includes(item.productId))
                    newLine.addClass('new_association');

                $(`<td class="product_name_container"><span class="product_name">${ item.name }</span></td>
                   <td class="dissociation_btn_container"><button class="dissociation_btn">X</button></td>`).appendTo( newLine );

                $(newLine).appendTo(this.__$associatedList);

            }

        }


        this.handleMediaInfosModification();

    }

    removeItemFromAssociatedList(productId, removeElementFromList = false)
    {

        this.__$associatedList.find(`tr[data-product_id="${ productId }"]`).removeClass('new_association').addClass('dissociated');
        this.__$productsList.find(`tr[data-product_id='${ productId }'] .choice_product`).prop('checked', false);
        this.__$associatedList.find(`tr[data-product_id="${ productId }"] .dissociation_btn_container button`).hide();

        if(removeElementFromList)
            this.__$associatedList.find(`tr[data-product_id="${ productId }"]`).remove();

        this.handleMediaInfosModification();

        //$(`.edit_media_info .associated_products_container span[data-product='${ item.productId }']`).remove();
    }

    updateMediaAssociatedProducts(mediaAssociationInfos = [])
    {

        if( this.__currentMediaId !== null )
        {

            let mediaId = this.__currentMediaId.replace('media_', '');

            $('.popup_loading_container').css({ 'z-index': 100000 }).addClass('is_open');

            $.ajax({
                url: `/update/media/${mediaId}/associated/products`,
                type: "POST",
                data: {productsToAssociation: mediaAssociationInfos},
                success: (response) => {

                    //console.log(response); debugger

                    $('.popup_loading_container').css({ 'z-index': '' }).removeClass('is_open');

                    this.__parent.getMediasContainer().find(`.card#${this.__currentMediaId}`).attr('data-products', mediaAssociationInfos.join(', '));

                    this.updateAssociatedListItemState();

                },
                error: (response, status, error) => {

                    $('.popup_loading_container').css({ 'z-index': '' }).removeClass('is_open');

                    alert("Erreur interne durant l'enregistrements !");

                    console.error(response); debugger

                },
            });

        }
        else
        {

            mediaAssociationInfos.map( (productId) => {

                $(`.media_available_products_list_container #edit_media_products_${productId}`).attr('checked', true);

                if( $(`.media_products_list tr[data-product_id='${productId}']`).length === 0 )
                    $(`<tr data-product_id="${productId}"><td>${ this.__$productsList.find(`tr[data-product_id='${productId}'] td.product_name`).text() }</td></tr>`).appendTo( $('.media_products_list') );

            } )

        }

    }

    updateAssociatedListItemState()
    {

        this.__$associatedList.find(`tr.dissociated`).each( (index, element) => {

            if( $(`.media_products_list tr[data-product_id='${ $(element).data('product_id') }']`).length > 0 )
            {
                $(`.media_available_products_list_container #edit_media_products_${ $(element).data('product_id') }`).removeAttr('checked');
                $(`.media_products_list tr[data-product_id='${ $(element).data('product_id') }']`).remove();
            }

            this.__$productsList.find(`tr[data-product_id='${ $(element).data('product_id') }'] .choice_product`).prop('checked', false);
            $(element).remove();

        } );

        this.__$associatedList.find(`tr.new_association`).removeClass('new_association');

    }

    updateUploadMediaAssociatedProducts(mediaAssociationInfos)
    {

        let registeredMediaInfosIndex = this.__mediasAssociationInfo.findIndex( mediaInfo =>  mediaInfo.media === mediaInfos.media );

        if( registeredMediaInfosIndex === -1 )
            this.__mediasAssociationInfo.push( mediaAssociationInfos );

        else
            this.__mediasAssociationInfo[ registeredMediaInfosIndex ].products = mediaAssociationInfos.products;

        let mediaIndex = this.__$mediasCollection.find(`.media_name[value='${ mediaAssociationInfos.media }']`).parents('li').data('index');

        $(`#medias_list_medias_${mediaIndex}_products input[type='checkbox']`).each( (index, input) => {

            $(input).attr('checked', ( mediaAssociationInfos.products.indexOf( parseInt($(input).val()) ) !== -1 ) );

        } )

    }


    rebuildMediaProductsAssociatedList(mediaProductsIds = [])
    {

        // if media is associate with products
        if( mediaProductsIds.length > 0 && mediaProductsIds[0] !== 'none' )
        {

            mediaProductsIds.forEach( (mediaProductId) => {

                this.__$productsList.find(`tr[data-product_id='${mediaProductId}'] .choice_product`).prop('checked', true);

                let productName = this.__$productsList.find(`tr[data-product_id='${mediaProductId}'] .product_name`).text();
                let productCriterions = this.__$productsList.find(`tr[data-product_id='${mediaProductId}']`).data('product_criterions');

                let item = `<tr data-product_id="${ mediaProductId }" data-product_criterions="${ productCriterions }">
                                            <td class="product_name_container"><span class="product_name">${ productName }</span></td>
                                            <td class="dissociation_btn_container"><button class="dissociation_btn">X</button></td>
                                        </tr>`;

                $(item).appendTo( this.__$associatedList );

            } );

        }

    }


    onClickOnChevronUpdateMediaAssociatedProductsList(active)
    {
        if(active)
        {

            this.__$location.find('.update_media_associated_products_list').on('click.onClickOnChevronUpdateMediaAssociatedProductsList', e => {

                this.__$productsList.find(`input[type='checkbox'].choice_product`).each( (index, element) => {

                    let productId = $(element).parents('tr').data('product_id');
                    let productName = $(element).parents('tr').find('.product_name').text();
                    let productCriterions = $(element).parents('tr').data('product_criterions');

                    if( $(element).is(':checked') )
                        this.addItemInAssociatedList({ productId: productId, productCriterions: productCriterions, name: productName });

                    else
                        this.removeItemFromAssociatedList(productId);

                } );

            })
        }
        else
        {
            this.__$location.find('.update_media_associated_products_list').off('click.onClickOnChevronUpdateMediaAssociatedProductsList');
        }

        return this;
    }

    onClickOnProductAssociationShortcutShowModal(active)
    {
        if(active)
        {
            this.__parent.getMediasContainer().on('click.onClickOnProductAssociationShortcutShowModal', '.shortcut_product_association', e => {

                this.__currentMediaId = $(e.currentTarget).parents('.card').attr('id');
                this.__currentMediaName = $(e.currentTarget).parents('.card').find('.media_name').text();
                this.__currentMediaTags = $(e.currentTarget).parents('.card').data('tags');

                this.__$location.find('.media_name_container .media_name').text( this.__currentMediaName );

                let mediaProductsAssociatedIds = [];

                if($(e.currentTarget).parents('.card').attr('data-products').includes(', '))
                    mediaProductsAssociatedIds = $(e.currentTarget).parents('.card').attr('data-products').split(', ');

                else
                    mediaProductsAssociatedIds.push( $(e.currentTarget).parents('.card').attr('data-products') );

                this.initializePopupContent(mediaProductsAssociatedIds);

            })
        }
        else
        {
            this.__parent.getMediasContainer().off('click.onClickOnProductAssociationShortcutShowModal', '.media_miniature');
        }

        return this;
    }


    onClickOnProductAssociationButtonShowModal(active)
    {

        if(active)
        {
            $('.product_association_btn').on("click.onClickOnAssociationButtonShowModal", e => {

                if( $('.media_products_list').length > 0 )
                {

                    this.__currentMediaName = $('.tab_content_body_title_container .media_name').text();
                    this.__$location.find('.media_name_container .media_name').text( this.__currentMediaName );

                    let mediaProductsAssociatedIds = [];

                    $('.media_products_list').find('tr').each( (index, tr) => {

                        mediaProductsAssociatedIds.push( $(tr).data('product_id') );

                    } )

                    this.__mediasAssociationInfo.push( { media: this.__currentMediaName, products: mediaProductsAssociatedIds } );

                    this.initializePopupContent(mediaProductsAssociatedIds);

                }

                /*$('.add-popup').css({ 'z-index': '-30' });
                this.__currentMediaName = $(e.currentTarget).data('media');
                this.__currentPos = $(e.currentTarget).parents('tr').attr('id');

                // check if media is already associated with product (in this case, update popup)
                this.getMediaAssociationInfos();

                this.__$location.find('.modal-title-container .media_name').text( this.__currentMediaName );
                this.__$location.fadeIn();*/

            })
        }
        else
        {
            $('.modal').off("click.onClickOnAssociationButtonShowModal", ".associate-product");
        }

        return this;
    }


    initializePopupContent(mediaProductsAssociatedIds)
    {

        // if media is associate with products
        if( mediaProductsAssociatedIds.length > 0 && mediaProductsAssociatedIds[0] !== 'none' )
        {

            mediaProductsAssociatedIds.forEach( (mediaProductId) => {

                //console.log(mediaProductsAssociatedIds); debugger

                let productName = this.__$productsList.find(`tr[data-product_id='${mediaProductId}'] .product_name`).text();
                let productCriterions = this.__$productsList.find(`tr[data-product_id='${mediaProductId}']`).data('product_criterions');

                this.addItemInAssociatedList({ productId: mediaProductId, productCriterions: productCriterions, name: productName });

            } );

        }

        //this.rebuildMediaProductsAssociatedList(mediaProductsAssociatedIds);

        this.getToolBox().getTool('FilterMediasTool').getSubTool('FilterMediasByAssociatedDataSubTool')
            .setFilterTarget('association_popup_item', this.__$container);

        this.__$container.addClass('is_open');

    }


    getMediaAssociationInfos()
    {

        let registeredMediaInfosIndex = this.__mediasAssociationInfo.findIndex( mediaInfo =>  mediaInfo.media === this.__currentMediaName );
        if(registeredMediaInfosIndex !== -1)
        {
            this.__mediasAssociationInfo[ registeredMediaInfosIndex ].products.forEach( (id, index) => {

                const productId = id;
                this.__$productsList.find(`tr[data-product_id='${ productId }'] .choice_product`).prop('checked', true);
                const productName = this.__$productsList.find(`tr[data-product_id='${ id }'] .product_name`).text();
                const productCriterions = this.__$productsList.find(`tr[data-product_id='${ id }']`).data('product_criterions');
                this.addItemInAssociatedList( { name: productName, productId: productId, productCriterions: productCriterions } );

            } )
        }

    }


    onClickOnCloseButtonCloseProductAssociationModal(active)
    {

        if(active)
        {
            $('.popup_associate_product .close_modal_button').on('click.onClickOnCloseButtonCloseProductAssociationModal', e => {

                //$('.add-popup').css({ 'z-index': '' });
                //this.__$location.fadeOut();

                if( ( (this.__$associatedList.find('.new_association').length > 0 || this.__$associatedList.find('.dissociated').length > 0 ) && confirm("Vous n'avez pas validés vos dernières modifications ! Voulez-vous vraiment continuer ?") ) ||
                    (this.__$associatedList.find('.new_association').length === 0 && this.__$associatedList.find('.dissociated').length === 0) )
                {

                    this.__$container.removeClass('is_open');

                    this.getToolBox().getTool('FilterMediasTool').removeAllFilters();

                    // reset
                    this.__$productsList.find('.choice_product').prop('checked', false);
                    this.__$location.find('.select_all_products').prop('checked', false);
                    this.__$associatedList.empty();

                    if( $(e.currentTarget).hasClass('cancel') && this.__isUpload )
                    {
                        this.updateUploadMediaAssociatedProducts( { media: this.__currentMediaName, products: [] } );
                    }

                }

            })
        }
        else
        {
            $('.popup_associate_product .close_modal_button').off('click.onClickOnCloseButtonCloseProductAssociationModal');
        }

        return this;
    }

    onClickOnValidationButtonUpdateMediaAssociatedInfos(active)
    {

        if(active)
        {
            this.__$location.find('.validate_association_btn').on('click.onClickOnValidationButtonUpdateMediaAssociatedInfos', e => {

                const mediaAssociatedProducts = $.map( this.__$productsList.find('.choice_product:checked'), (element) =>  $(element).parents('tr').data('product_id'));

                this.updateMediaAssociatedProducts(mediaAssociatedProducts);

                this.updateAssociatedListItemState();

                this.__$location.find('.validate_association_btn').attr('disabled', true);

                // this.handleMediaInfosModification();

                /*let productsToMedia = [];
                this.__$associatedList.find('tr').each( (index, element) => {

                    $(`.edit_media_info #${this.__currentPos}`).addClass('unregistered');

                    productsToMedia.push( $(element).data('product_id') );

                    if( this.__isUpload && $(`.edit_media_info #${this.__currentPos} .associated-products-container span[data-product='${ $(element).data('product_id') }']`).length === 0 )
                    {
                        $(`<span>`, {
                            text: $(element).find('.product-name').text(),
                        }).attr('data-product', $(element).data('product_id'))
                          .appendTo( $(`.edit_media_info #${this.__currentPos} .associated-products-container`) );

                        const productCriterions = $(element).data('product_criterions').split(', ');

                        productCriterions.forEach( (productCriterions) => {

                            $(`<span>`, {
                                text: productCriterions,
                            }).appendTo( $(`.edit_media_info #${this.__currentPos} .criterions-affectation-container`) );

                        } )

                    }

                } );

                if( this.__isUpload && this.__$associatedList.find('tr').length === 0 )
                {
                    $(`.edit_media_info #${this.__currentPos} .associated_products_container`).empty();
                    $(`.edit_media_info #${this.__currentPos} .criterions_affectation_container`).empty();
                }

                this.updateMediaAssociatedProducts( { media: this.__currentMediaName, products: productsToMedia } );*/

            })
        }
        else
        {
            this.__$location.find('.validate_association_btn').off('click.onClickOnValidationButtonUpdateMediaAssociatedInfos');
        }

        return this;
    }

    onClickOnProductDissociationButton(active)
    {
        if(active)
        {
            this.__$associatedList.on('click.onClickOnProductDissociationButton', '.dissociation_btn', e => {

                this.removeItemFromAssociatedList( $(e.currentTarget).parents('tr').data('product_id') );

                /*
                let button = $(e.currentTarget);

                this.__$productsList.find(`.choice_product[data-product_name="${ button.parents('tr').find('.product_name').text() }"]`).prop('checked', false)

                button.parents('tr').addClass('dissociated');
                button.hide();

                this.handleMediaInfosModification();*/

            })
        }
        else
        {
            this.__$associatedList.off('click.onClickOnProductDissociationButton', '.dissociation_btn');
        }

        return this;
    }

    onClickOnSelectAllButtonSelectAllProducts(active)
    {
        if(active)
        {
            this.__$location.find('.select_all_products').on('click.onClickOnSelectAllButtonSelectAllProducts', e => {

                if( $(e.currentTarget).is(':checked') )
                {
                    this.__$productsList.find('.choice_product').each( (index, input) => {
                        $(input).prop('checked', true);
                        const productName = $(input).data('product_name');
                        const productId = $(input).parents('tr').data('product_id');
                        const productCriterions = $(input).parents('tr').data('product_criterions');
                        //this.addItemInAssociatedList( { name: productName, productId: productId, productCriterions: productCriterions } );
                    } );
                }
                else
                {
                    this.__$productsList.find('.choice_product').prop('checked', false)
                    //this.__$associatedList.empty();
                    this.__$associatedList.find('tr').addClass('dissociated');
                }

                
                this.handleMediaInfosModification();

            })
        }
        else
        {
            this.__$location.find('.select_all_products').off('click.onClickOnSelectAllButtonSelectAllProducts');
        }

        return this;
    }

    onProductSelectionAddProductInAssociatedList(active)
    {
        if(active)
        {
            this.__$productsList.find('.choice_product').on('change.onClickOnAddProductInListCheckbox', e => {

                /*const productName = $(e.currentTarget).data('product_name');
                const productId = $(e.currentTarget).parents('tr').data('product_id');
                const productCriterions = $(e.currentTarget).parents('tr').data('product_criterions');

                if( $(e.currentTarget).is(':checked') )
                    this.addItemInAssociatedList( {name: productName, productId: productId, productCriterions: productCriterions} );

                else
                    this.removeItemFromAssociatedList(productId);*/

                if(this.__$productsList.find('.choice_product:checked').length === 0)
                    this.__$location.find('.select_all_products').prop('checked', false);

            })
        }
        else
        {
            this.__$productsList.off('change.onClickOnAddProductInListCheckbox', '.choice_product');
        }

        return this;
    }

    handleMediaInfosModification()
    {

        this.__$location.find('.validate_association_btn').attr('disabled', ( (this.__$associatedList.find('tr.dissociated').length === 0)
            && (this.__$associatedList.find('tr.new_association').length === 0)  ) );

        // if( this.__$associatedList.find('tr.dissociated').length > 0 || this.__$associatedList.find('tr.new_association').length > 0 )
        //             this.__$location.find('.validate_association_btn').prop('disabled', true );
        //
        //         else
        //             this.__$location.find('.validate_association_btn').prop('disabled', false );

    }

    onCategorySelectionAddFilterOnProductList(active)
    {
        if(active)
        {
            this.__$location.on('change.onCategorySelectionAddFilterOnProductList', '.product_category_choice', e => {

                /*const category = $(e.currentTarget).val();

                this.__$productsList.find(`tr`).each( (index, item) => {

                    if(category !== "")
                    {
                        if( parseInt( $(item).data('category') ) !== parseInt( $(e.currentTarget).val() ) )
                            $(item).fadeOut();

                        else
                            $(item).fadeIn();
                    }
                    else
                        $(item).fadeIn();

                } );*/

            })
        }
        else
        {
            this.__$location.off('change.onCategorySelectionAddFilterOnProductList', '.product_category_choice');
        }

        return this;
    }

    enable()
    {
        super.enable();

        this.onClickOnChevronUpdateMediaAssociatedProductsList(true)
            .onClickOnValidationButtonUpdateMediaAssociatedInfos(true)
            .onClickOnProductDissociationButton(true)
            .onClickOnSelectAllButtonSelectAllProducts(true)
            .onProductSelectionAddProductInAssociatedList(true)
            .onCategorySelectionAddFilterOnProductList(true)
            .onClickOnProductAssociationShortcutShowModal(true)
            .onClickOnProductAssociationButtonShowModal(true)
            .onClickOnCloseButtonCloseProductAssociationModal(true)
        ;
    }

    disable()
    {
        super.disable();

        this.onClickOnChevronUpdateMediaAssociatedProductsList(false)
            .onClickOnValidationButtonUpdateMediaAssociatedInfos(false)
            .onClickOnProductDissociationButton(false)
            .onClickOnSelectAllButtonSelectAllProducts(false)
            .onProductSelectionAddProductInAssociatedList(false)
            .onCategorySelectionAddFilterOnProductList(false)
            .onClickOnProductAssociationShortcutShowModal(false)
            .onClickOnProductAssociationButtonShowModal(false)
            .onClickOnCloseButtonCloseProductAssociationModal(false)
        ;
    }


}

export default MediaProductAssociationHandlerTool;