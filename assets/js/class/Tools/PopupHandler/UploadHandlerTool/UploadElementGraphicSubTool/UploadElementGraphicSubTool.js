import ParentTool from "../../../ParentTool";
import SubTool from "../../../SubTool";
import UploadHandlerSubTool from "../UploadHandlerSubTool";

class UploadElementGraphicSubTool extends UploadHandlerSubTool {

    constructor()
    {
        super();
        this.__name = this.constructor.name;
    }

    showMediaInfoForEdit(elementGraphicInfos)
    {

        return `<td> 
                    <p title="${ elementGraphicInfos.fileName }"><i class="fas fa-trash-alt cancel-upload" aria-hidden="true"></i> ${ elementGraphicInfos.fileName }</p> 
                </td>
                <td>
                    <progress class="progress_bar" id="progress_${ elementGraphicInfos.index }" max="100" value="100"></progress>
                    <i class="fas fa-check" aria-hidden="true"></i>
                </td>
                <td> 
                    ${ super.getMediaPreview(elementGraphicInfos) } 
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
                        ${ this.__parent.buildAssociationInputsHtml('products', elementGraphicInfos.index) }
                    </div> 
                </td>`;
    }

    enable()
    {
        super.enable();
    }

    disable()
    {
        super.disable();
    }

}

export default UploadElementGraphicSubTool;