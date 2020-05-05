import Tool from "../Tool";

class ProductAssociationHandlerTool extends Tool
{

    constructor()
    {
        super();
        this.__name = "ProductAssociationHandlerTool";
    }

    onClickOnValidationButtonAddProductInAssociateList(active)
    {

        if(active)
        {
            $('.associate_product-popup .validation-btn').on('click.onClickOnValidationButtonAddProductInAssociateList', e => {

                $('associate_product-popup .product-choice-list .choice_product:checked').each( (checkbox) => {

                    const productName = $(checkbox).data('product');


                } )

            })
        }
        else
        {
            $('.associate_product-popup .validation-btn').off('click.onClickOnValidationButtonAddProductInAssociateList');
        }

        return this;
    }

    enable() {
        super.enable();
        this.onClickOnValidationButtonAddProductInAssociateList(true)
        ;
    }

    disable() {
        super.disable();
        this.onClickOnValidationButtonAddProductInAssociateList(false)
        ;
    }


}

export default ProductAssociationHandlerTool;