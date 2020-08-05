import Tool from "../Tool";

class DraggableTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__draggableElement = null;
        this.__currentDraggableElement = null;
    }

    setDraggableElement(draggableElement)
    {

        //console.log(draggableElement); debugger

        this.__draggableElement = draggableElement;

        if( (typeof this.__draggableElement.length !== 'undefined') && (this.__draggableElement.length > 1) )
        {
            this.__draggableElement.each( (index, element) => {

                $(element).addClass('draggable');


            } )
        }
        else
        {
            this.__draggableElement.addClass('draggable');
        }

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

            if((typeof this.__draggableElement.length !== 'undefined') && (this.__draggableElement.length > 1))
            {

                this.__draggableElement.each( (index, element) => {

                    $(element).on('mousedown.onMouseDownOnDraggableElement', e => {

                        e.preventDefault();

                        this.__currentDraggableElement = $(element);

                        let parentHeight = this.__currentDraggableElement.parent().innerHeight;
                        let top = parseInt(this.__currentDraggableElement.css('top'));
                        let original_ypos = this.__currentDraggableElement.position().top; //original ypos
                        let drag_min_ypos = 0 - original_ypos;
                        let drag_max_ypos = parentHeight - original_ypos - this.__currentDraggableElement.outerHeight();
                        let drag_start_ypos = e.clientY;
                        let my_ypos = original_ypos;

                        this.__draggableElement.each( (index, element) => {
                            $(element).attr('data-order', (index + 1));
                        });

                        var prev_button = this.__currentDraggableElement.prev('.button');
                        var next_button = this.__currentDraggableElement.next('.button');
                        var prev_button_ypos = prev_button.length > 0 ? prev_button.position().top : '';
                        var next_button_ypos = next_button.length > 0 ? next_button.position().top : '';

                        /*this.__currentDraggableElement.parent().find('.ondrag').removeClass('ondrag');

                        this.__currentDraggableElement.addClass('ondrag');

                        $(element).on('mousemove.onDraggableElementMove', this.onDraggableElementMove.bind(this));
                        $(element).on('mouseup.onDraggableElementMoveStop', this.onDraggableElementMoveStop.bind(this));*/

                    });

                    $(element).parent().on('click.onClickOnParentDisableDraggableElement', e => {

                        $(e.currentTarget).find('.ondrag').removeClass('ondrag');

                    })

                } )

            }
            else
            {

            }

        }
        else
        {
            if(Array.isArray(this.__draggableElement))
            {
                this.__draggableElement.each( (index, element) => {

                    $(element).off('mousedown.onMouseDownOnDraggableElement');

                } )
            }

            else
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
        this.__currentDraggableElement.off('mousemove.onDraggableElementMove');
        this.__currentDraggableElement.off('mouseup.onDraggableElementMoveStop');
    }

    removeEventListener()
    {

        this.__draggableElement.each( (index, draggableElement) => {

            //console.log(draggableElement); debugger
            $(draggableElement).removeClass('draggable');

            $(draggableElement).parent().off('click.onClickOnParentDisableDraggableElement');

            $(draggableElement).off('mousedown.onMouseDownOnDraggableElement');
            $(draggableElement).off('mousemove.onDraggableElementMove');
            $(draggableElement).off('mouseup.onDraggableElementMoveStop');

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