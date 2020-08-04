import Tool from "../Tool";

class DraggableTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__draggableElement = null;

        this.__pos1 = this.__pos2 = this.__pos3 = this.__pos4 = 0;
    }

    setDraggableElement(draggableElement)
    {

        //console.log(draggableElement); debugger

        this.__draggableElement = draggableElement;

        return this;
    }

    getDraggableElement()
    {
        return this.__draggableElement;
    }

    handleDraggableEvent()
    {

        if(this.isActive())
        {

            this.__draggableElement.each( (index, element) => {

                $(element).on('mousedown', e => {

                    e.preventDefault();

                    this.__currentDraggableElement = $(element);

                    this.__currentDraggableElement.parent().find('.ondrag').removeClass('ondrag');

                    this.__currentDraggableElement.addClass('ondrag');

                    $(element).on('mousemove', this.onDraggableElementMove.bind(this));
                    $(element).on('mouseup', this.onDraggableElementMoveStop.bind(this));

                });

                $(element).parent().on('click.', e => {

                    $(e.currentTarget).find('.ondrag').removeClass('ondrag');

                })

            } )
        }
        else
        {
            this.__draggableElement.off('mousedown.onMouseDownOnDraggableElement');
        }

        return this;
    }

    onDraggableElementMove(e)
    {

        this.__currentDraggableElement.offset({

            //top: e.pageY - this.__currentDraggableElement.outerHeight() / 2,
            left: e.pageX - this.__currentDraggableElement.outerHeight() /2

        })

    }

    onDraggableElementMoveStop(e)
    {
        this.__currentDraggableElement.off('mousemove');
        this.__currentDraggableElement.off('mouseup');
    }

    removeEventListener()
    {

        this.__draggableElement.each( (index, draggableElement) => {

            //console.log(draggableElement); debugger
            $(draggableElement).off('mousedown');
            $(draggableElement).off('mousemove');
            $(draggableElement).off('mouseup');

        } )

    }

    enable()
    {
        super.enable();

        return this;
    }

    disable()
    {
        super.disable();

        this.removeEventListener()

        return this;
    }

}

export default DraggableTool;