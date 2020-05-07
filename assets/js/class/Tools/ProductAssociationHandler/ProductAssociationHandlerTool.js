import Tool from "../Tool";

class ProductAssociationHandlerTool extends Tool
{

    constructor()
    {
        super();
        this.__name = "ProductAssociationHandlerTool";
        this.__$productList = $('.associate_product-popup .product-choice-list');
        this.__$associatedList = $('.associate_product-popup .product-associated-list');
        this.__$mediasCollection = $('.medias_collection');
        this.__$location = $('.associate_product-popup');
    }

    addItemInList(item)
    {
        if( this.__$associatedList.find(`tr[data-product="${ item }"]`).length < 1 )
        {
            let newItem = `<tr data-product="${ item }">
                                            <td>${ item }</td>
                                            <td><button class="remove_item">X</button></td>
                                        </tr>`;

            $(newItem).appendTo(this.__$associatedList);
        }
    }

    removeItemFromList(item)
    {
        this.__$associatedList.find(`tr[data-product="${ item }"]`).remove();
    }

    onClickOnProductAssociationButtonShowModal(active)
    {

        if(active)
        {
            $('.modal').on("click.onClickOnAssociationButtonShowModal", ".associate-product", e => {

                // reset
                this.__$productList.find('.choice_product').prop('checked', false);
                this.__$location.find('.select_all_products').prop('checked', false);
                this.__$associatedList.empty();

                // @TODO: check if media is already associated with product (in this case, update popup)

                $('.add-popup').css({ 'z-index': '-30' });
                $(".associate_product-popup .modal-title-container span.media_name").text( $(e.currentTarget).data('media') );
                $(".associate_product-popup").fadeIn();

            })
        }
        else
        {
            $('.modal').off("click.onClickOnAssociationButtonShowModal", ".associate-product");
        }

        return this;
    }

    onClickOnProductAssociationModalCloseButton(active)
    {

        if(active)
        {
            $('.associate_product-popup .close').on('click.', e => {

                $('.add-popup').css({ 'z-index': '' });
                $(".associate_product-popup").fadeOut();

            })
        }
        else
        {
            $('.associate_product-popup .close').off('click.');
        }

        return this;
    }

    onClickOnValidationButtonAddProductInAssociateList(active)
    {

        if(active)
        {
            this.__$location.find('.validation-btn').on('click.onClickOnValidationButtonAddProductInAssociateList', e => {

                let currentMediaName = this.__$location.find('.modal-title-container span.media_name').text();
                let mediaIndex = this.__$mediasCollection.find(`.media_name[value='${ currentMediaName }']`).data('index');

                // update media hidden datas
                $(`#medias_list_medias_${mediaIndex}_products input[type='checkbox']`).prop('checked', false)

                this.__$associatedList.find('tr').each( (index, item) => {

                    $(`#medias_list_medias_${mediaIndex}_products label`).each( (index2, label) => {

                        if( $(label).text() === $(item).data('product') )
                            $(label).prev("input[type='checkbox']").prop('checked', true);

                    } )

                } )

            })
        }
        else
        {
            $('.associate_product-popup .validation-btn').off('click.onClickOnValidationButtonAddProductInAssociateList');
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
                        this.addItemInList( $(input).data('product') );

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

                const product = $(e.currentTarget).data('product');

                if( $(e.currentTarget).is(':checked') )
                {
                    this.addItemInList( product );
                }
                else
                {
                    this.removeItemFromList( product );
                }

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
            this.__$location.on('change.onCategorySelectionAddFilterOnProductList', '.product-category-choice', e => {

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
            this.__$location.off('change.onCategorySelectionAddFilterOnProductList', '.product-category-choice');
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
            .onClickOnProductAssociationButtonShowModal(true)
            .onClickOnProductAssociationModalCloseButton(true)
        ;
    }

    disable() {
        super.disable();
        this.onClickOnValidationButtonAddProductInAssociateList(false)
            .onClickOnRemoveAssociateListItemButton(false)
            .onClickOnSelectAllButtonSelectAllProducts(false)
            .onClickOnAddProductInListCheckbox(false)
            .onCategorySelectionAddFilterOnProductList(false)
            .onClickOnProductAssociationButtonShowModal(false)
            .onClickOnProductAssociationModalCloseButton(false)
        ;
    }


}

export default ProductAssociationHandlerTool;