import ParentTool from "../../../ParentTool";
import SubTool from "../../../SubTool";
import UploadHandlerSubTool from "../UploadHandlerSubTool";

class UploadMediaDiffSubTool extends UploadHandlerSubTool {

    constructor()
    {
        super();
        this.__name = this.constructor.name;
    }

    showMediaInfoForEdit(mediaInfos)
    {

        let now = new Date();
        let month = (now.getMonth() + 1);
        month = (month < 10) ? '0' + month : month;
        let day = (now.getDate() < 10 ) ? '0' + now.getDate() : now.getDate();
        let year = now.getFullYear();

        return `<td> <p title="${ mediaInfos.fileName }"><i class="fas fa-trash-alt cancel-upload" aria-hidden="true"></i> ${ mediaInfos.fileName }</p> </td>
                <td>
                    <progress class="progress_bar" id="progress_${ mediaInfos.index }" max="100" value="100"></progress>
                    <i class="fas fa-check" aria-hidden="true"></i>
                </td>
                <td> 
                    ${ super.getMediaPreview(mediaInfos) } 
                    <i class="fas fa-expand-alt show_expanded_miniature" data-media_id="${ mediaInfos.id }" aria-hidden="true"></i>
                </td>
                <td>
                    <span>${mediaInfos.extension}</span> <br> <span>${mediaInfos.width} * ${mediaInfos.height} px</span> <br> <span>${ (mediaInfos.fileType === 'image') ? mediaInfos.dpi + ' dpi' :  mediaInfos.codec}</span>
                </td>
                <td class="media_name_container"> 
                    <input type="hidden" class="media_id" name="${ $('.step_2 form').attr('name') }[medias][${mediaInfos.index}][id]" value="${ mediaInfos.id }"> 
                    <span class="error hidden"></span> <br>
                    <input type="text" name="${ $('.step_2 form').attr('name') }[medias][${mediaInfos.index}][name]" class="form_input media_name" placeholder="Nom du media" value="${mediaInfos.fileNameWithoutExtension}" required> </td>
                <td class="media_diff_date_container"> 
                    <div class="diff_start_container">
                        <span class="error hidden"></span> <br> 
                        <label for="media_${mediaInfos.index}_diff_start">Du</label>
                        <input type="date" name="${ $('.step_2 form').attr('name') }[medias][${mediaInfos.index}][diffusionStart]" id="media_${mediaInfos.index}_diff_start" class="diffusion_dates start form_input" value="${year}-${month}-${day}">
                   </div>

                   <div class="diff_end_container">
                        <span class="error hidden"></span> <br> 
                        <label for="media_${mediaInfos.index}_diff_end">Au</label>
                        <input type="date" name="${ $('.step_2 form').attr('name') }[medias][${mediaInfos.index}][diffusionEnd]" id="media_${mediaInfos.index}_diff_end" class="diffusion_dates end form_input" min="${year}-${month}-${day}" value="${year + 10}-${month}-${day}">
                   </div>
                </td>
                <td class="associated_criterions_container">
                
                </td>
                <td class="tags_affectation_container"> 
                    <button type="button" class="btn tag_association_btn association_btn">Associer tags</button>
                    <div class="associated_tags_container">
                        ${ this.__parent.buildAssociationInputsHtml('tags', mediaInfos.index) }
                    </div> 
                </td>
                <td class="products_affectation_container"> 
                    <button type="button" class="btn product_association_btn association_btn">Associer produits</button>
                    <div class="associated_products_container">
                        ${ this.__parent.buildAssociationInputsHtml('products', mediaInfos.index) }
                    </div> 
                </td>
                <td> 
                    <label class=""><input type="radio" name="${ $('.step_2 form').attr('name') }[medias][${mediaInfos.index}][containIncrustations]" class="form_input media_contain_incruste" value="1">Oui</label> 
                    <label class=""><input type="radio" name="${ $('.step_2 form').attr('name') }[medias][${mediaInfos.index}][containIncrustations]" class="form_input media_contain_incruste" value="0" checked>Non</label>
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

export default UploadMediaDiffSubTool;