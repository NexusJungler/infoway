import Tool from "../Tool";

class DraggableTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__draggableElement = null;
    }

    setDraggableElement(draggableElement)
    {
        this.__draggableElement = draggableElement;

        this.activeEvent();

        return this;
    }

    getDraggableElement()
    {
        return this.__draggableElement;
    }

    activeEvent()
    {

        this.__draggableElement.on('mousedown.activeEvent', e => {



        })

    }

    disableEvent()
    {

    }

    onMouseDownOnDraggableElement(active)
    {

    }

    onDraggableElementMove(active)
    {

    }

    onDraggableElementMoveStop(active)
    {

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

export default DraggableTool;