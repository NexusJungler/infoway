import Tool from "../Tool";

class MediaCriterionAssociationHandler extends Tool{

    constructor() {
        super();
        this.__name = this.constructor.name;
        this.__$mediasCollection = $('.medias_collection');
    }

    enable() {
        super.enable();
    }

    disable() {
        super.disable();
    }

}

export default MediaCriterionAssociationHandler;