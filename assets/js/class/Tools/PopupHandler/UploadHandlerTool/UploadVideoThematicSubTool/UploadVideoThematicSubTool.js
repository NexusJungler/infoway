
import UploadHandlerSubTool from "../UploadHandlerSubTool";

class UploadVideoThematicSubTool extends UploadHandlerSubTool {

    constructor()
    {

        super();
        this.__name = this.constructor.name;
        this.__thematicPrototype =  $('.step_2 .file_to_characterisation_list').data('video_thematic_themes_prototype');
        $('.step_2 .file_to_characterisation_list').removeAttr('data-video_thematic_themes_prototype');

    }

    getThemePrototype()
    {
        return this.__thematicPrototype;
    }

    showMediaInfoForEdit(videoThematicInfos)
    {

        return `<td> 
                    <p title="${ videoThematicInfos.fileName }"><i class="fas fa-trash-alt cancel-upload" aria-hidden="true"></i> ${ videoThematicInfos.fileName }</p> 
                </td>
                <td>
                    <progress class="progress_bar" id="progress_${ videoThematicInfos.index }" max="100" value="100"></progress>
                    <i class="fas fa-check" aria-hidden="true"></i>
                </td>
                <td> 
                    ${ super.getMediaPreview(videoThematicInfos) } 
                    <i class="fas fa-expand-alt show_expanded_miniature" data-media_id="${ videoThematicInfos.id }" aria-hidden="true"></i>
                </td>
                <td> 
                    <input type="hidden" class="media_id" name="medias_list[medias][${videoThematicInfos.index}][id]" value="${ videoThematicInfos.id }">
                    <input type="hidden" name="medias_list[medias][${videoThematicInfos.index}][id]" value="${ videoThematicInfos.id }"> 
                    <span class="error hidden"></span> <br>
                    <input type="text" name="medias_list[medias][${videoThematicInfos.index}][name]" class="form_input media_name" placeholder="Nom du media" value="${ videoThematicInfos.fileNameWithoutExtension }" required>
                 </td>
                <td class="tags_affectation_container"> 
                    <button type="button" class="btn tag_association_btn association_btn">Associer tags</button>
                    <div class="associated_tags_container">
                        ${ this.__parent.buildAssociationInputsHtml('tags', videoThematicInfos.index) }
                    </div> 
                </td>    
                <td class="thematic_affectation_container"> 
                    <select name="${ $('.step_2 form').attr('name') }[medias][${ videoThematicInfos.index }][thematic]">
                        ${ this.__thematicPrototype }
                    </select>
                </td>            

`;
        
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

export default UploadVideoThematicSubTool;