import Tool from "../../Tool";
import SubTool from "../../SubTool";

class MediaProductAssociationHandlerTool extends SubTool
{

    constructor()
    {
        super();
        //this.__name = "MediaProductAssociationHandlerTool";
        this.__name = this.constructor.name; // get name by constructor
        this.__$productList = $('.popup_associate_product .product_choice_list');
        this.__$associatedList = $('.popup_associate_product .product_associated_list');
        this.__$mediasCollection = $('.medias_collection');
        this.__$container = $('.popup_associate_product_container');
        this.__$location = $('.popup_associate_product');
        this.__currentMedia = null;
        this.__currentPos = null;
        this.__mediasAssociationInfo = [];
        this.__isUpload = false;
    }

    addItemInList(item)
    {
        if( this.__$associatedList.find(`tr[data-product_id="${ item.productId }"]`).length < 1 )
        {
            let newItem = `<tr data-product_id="${ item.productId }" data-product_criterions="${ item.productCriterions }">
                                            <td class="product_name">${ item.name }</td>
                                            <td><button class="remove_item">X</button></td>
                                        </tr>`;

            $(newItem).appendTo(this.__$associatedList);
        }
    }

    removeItemFromList(item)
    {
        this.__$associatedList.find(`tr[data-product_id="${ item.productId }"]`).remove();
        $(`.edit_media_info .associated_products_container span[data-product='${ item.productId }']`).remove();
    }

    updateMediaAssociatedProducts(mediaInfos)
    {

        let registeredMediaInfosIndex = this.__mediasAssociationInfo.findIndex( mediaInfo =>  mediaInfo.media === mediaInfos.media );

        if( registeredMediaInfosIndex === -1 )
            this.__mediasAssociationInfo.push( mediaInfos );

        else
            this.__mediasAssociationInfo[ registeredMediaInfosIndex ].products = mediaInfos.products;

        if(this.__isUpload)
        {

            let mediaIndex = this.__$mediasCollection.find(`.media_name[value='${ mediaInfos.media }']`).parents('li').data('index');

            $(`#medias_list_medias_${mediaIndex}_products input[type='checkbox']`).each( (index, input) => {

                $(input).attr('checked', ( mediaInfos.products.indexOf( parseInt($(input).val()) ) !== -1 ) );

            } )

        }

    }


    onClickOnProductAssociationShortcutShowModal(active)
    {
        if(active)
        {
            this.__parent.getMediasContainer().on('click.onClickOnProductAssociationShortcutShowModal', '.shortcut_product_association', e => {

                this.__currentMedia = $(e.currentTarget).parents('card').find('.media_name');

                this.__$container.addClass('is_open');

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
            $('.modal .edit_media_info').on("click.onClickOnAssociationButtonShowModal", ".associate-product", e => {

                $('.add-popup').css({ 'z-index': '-30' });
                this.__currentMedia = $(e.currentTarget).data('media');
                this.__currentPos = $(e.currentTarget).parents('tr').attr('id');

                // check if media is already associated with product (in this case, update popup)
                this.getMediaAssociationInfos();

                this.__$location.find('.modal-title-container .media_name').text( this.__currentMedia );
                this.__$location.fadeIn();

            })
        }
        else
        {
            $('.modal').off("click.onClickOnAssociationButtonShowModal", ".associate-product");
        }

        return this;
    }


    getMediaAssociationInfos()
    {

        let registeredMediaInfosIndex = this.__mediasAssociationInfo.findIndex( mediaInfo =>  mediaInfo.media === this.__currentMedia );
        if(registeredMediaInfosIndex !== -1)
        {
            this.__mediasAssociationInfo[ registeredMediaInfosIndex ].products.forEach( (id, index) => {

                const productId = id;
                this.__$productList.find(`tr[data-product_id='${ productId }'] .choice_product`).prop('checked', true);
                const productName = this.__$productList.find(`tr[data-product_id='${ id }'] .product_name`).text();
                const productCriterions = this.__$productList.find(`tr[data-product_id='${ id }']`).data('product_criterions');
                this.addItemInList( { name: productName, productId: productId, productCriterions: productCriterions } );

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

                this.__$container.removeClass('is_open');

                // reset
                this.__$productList.find('.choice_product').prop('checked', false);
                this.__$location.find('.select_all_products').prop('checked', false);
                this.__$associatedList.empty();

                if( $(e.currentTarget).hasClass('cancel') && this.__isUpload )
                {
                    this.updateMediaAssociatedProducts( { media: this.__currentMedia, products: [] } );
                }

            })
        }
        else
        {
            $('.popup_associate_product .close_modal_button').off('click.onClickOnCloseButtonCloseProductAssociationModal');
        }

        return this;
    }

    onClickOnValidationButtonAddProductInAssociateList(active)
    {

        if(active)
        {
            this.__$location.find('.validation-btn').on('click.onClickOnValidationButtonAddProductInAssociateList', e => {

                let productsToMedia = [];
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

                this.updateMediaAssociatedProducts( { media: this.__currentMedia, products: productsToMedia } );

            })
        }
        else
        {
            $('.associate_product_popup .validation_btn').off('click.onClickOnValidationButtonAddProductInAssociateList');
        }

        return this;
    }

    onClickOnRemoveAssociateListItemButton(active)
    {
        if(active)
        {
            this.__$associatedList.on('click.onClickOnRemoveAssociateListItemButton', '.remove_item', e => {

                let button = $(e.currentTarget);

                this.__$productList.find(`.choice_product[data-product="${ button.parents('tr').data('product') }"]`).prop('checked', false)

                button.parents('tr').remove();

            })
        }
        else
        {
            this.__$associatedList.off('click.onClickOnRemoveAssociateListItemButton', '.remove_item');
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
                    this.__$productList.find('.choice_product').each( (index, input) => {
                        $(input).prop('checked', true);
                        const productName = $(input).data('product_name');
                        const productId = $(input).parents('tr').data('product_id');
                        const productCriterions = $(input).parents('tr').data('product_criterions');
                        this.addItemInList( { name: productName, productId: productId, productCriterions: productCriterions } );
                    } );
                }
                else
                {
                    this.__$productList.find('.choice_product').prop('checked', false)
                    this.__$associatedList.empty();
                }

            })
        }
        else
        {
            this.__$location.find('.select_all_products').off('click.onClickOnSelectAllButtonSelectAllProducts');
        }

        return this;
    }

    onClickOnAddProductInListCheckbox(active)
    {
        if(active)
        {
            this.__$productList.on('click.onClickOnAddProductInListCheckbox', '.choice_product', e => {

                const productName = $(e.currentTarget).data('product_name');
                const productId = $(e.currentTarget).parents('tr').data('product_id');
                const productCriterions = $(e.currentTarget).parents('tr').data('product_criterions');

                if( $(e.currentTarget).is(':checked') )
                    this.addItemInList( {name: productName, productId: productId, productCriterions: productCriterions} );

                else
                    this.removeItemFromList( {productId: productId} );

                if(this.__$productList.find('.choice_product:checked').length === 0)
                    this.__$location.find('.select_all_products').prop('checked', false);

            })
        }
        else
        {
            this.__$productList.off('click.onClickOnAddProductInListCheckbox', '.choice_product');
        }

        return this;
    }

    onCategorySelectionAddFilterOnProductList(active)
    {
        if(active)
        {
            this.__$location.on('change.onCategorySelectionAddFilterOnProductList', '.product_category_choice', e => {

                const category = $(e.currentTarget).val();

                this.__$productList.find(`tr`).each( (index, item) => {

                    if(category !== "")
                    {
                        if( parseInt( $(item).data('category') ) !== parseInt( $(e.currentTarget).val() ) )
                            $(item).fadeOut();

                        else
                            $(item).fadeIn();
                    }
                    else
                        $(item).fadeIn();

                } );

            })
        }
        else
        {
            this.__$location.off('change.onCategorySelectionAddFilterOnProductList', '.product_category_choice');
        }

        return this;
    }

    enable() {
        super.enable();
        this.onClickOnValidationButtonAddProductInAssociateList(true)
            .onClickOnRemoveAssociateListItemButton(true)
            .onClickOnSelectAllButtonSelectAllProducts(true)
            .onClickOnAddProductInListCheckbox(true)
            .onCategorySelectionAddFilterOnProductList(true)
            .onClickOnProductAssociationShortcutShowModal(true)
            .onClickOnProductAssociationButtonShowModal(true)
            .onClickOnCloseButtonCloseProductAssociationModal(true)
        ;
    }

    disable() {
        super.disable();
        this.onClickOnValidationButtonAddProductInAssociateList(false)
            .onClickOnRemoveAssociateListItemButton(false)
            .onClickOnSelectAllButtonSelectAllProducts(false)
            .onClickOnAddProductInListCheckbox(false)
            .onCategorySelectionAddFilterOnProductList(false)
            .onClickOnProductAssociationShortcutShowModal(false)
            .onClickOnProductAssociationButtonShowModal(false)
            .onClickOnCloseButtonCloseProductAssociationModal(false)
        ;
    }


}

export default MediaProductAssociationHandlerTool;