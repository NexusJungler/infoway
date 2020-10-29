import Tool from "../Tool";
import ParentTool from "../ParentTool";
import MediaDeletingButtonHandler from "./MediaDeletingButtonHandler/MediaDeletingButtonHandler";
import MediaModificationButtonHandler from "./MediaModificationButtonHandler/MediaModificationButtonHandler";
import MediaDuplicatingButtonHandler from "./MediaDuplicatingButtonHandler/MediaDuplicatingButtonHandler";

class MediathequeActionButtonHandler extends ParentTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__subTools = [
            new MediaDeletingButtonHandler(),
            new MediaModificationButtonHandler(),
            new MediaDuplicatingButtonHandler(),
        ]
    }

    onMediaSelectionAndDeselectionChangeMediaActionsButtonsState(active)
    {
        if(active)
        {
            $(".medias_list_container").on('change.onMediaSelectionAndDeselectionChangeDeleteButtonState', ".select_media_input", e => {

                if($(".medias_list_container").find(".select_media_input:checked").length === 0)
                    $('.media_action_btn').attr('disabled', true);

                else
                {

                    $('.media_action_btn').removeAttr('disabled');

                    if($(".medias_list_container").find(".select_media_input:checked").length > 1)
                        $('.modify_media_btn').attr('disabled', true);
                }

            })
        }
        else
        {
            $(".medias_list_container").off('change.onMediaSelectionAndDeselectionChangeDeleteButtonState', ".select_media_input");
        }

        return this;
    }

    enable()
    {
        super.enable();
        this.onMediaSelectionAndDeselectionChangeMediaActionsButtonsState(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onMediaSelectionAndDeselectionChangeMediaActionsButtonsState(false)
        ;
    }

}

export default MediathequeActionButtonHandler;