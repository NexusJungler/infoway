"use strict";


class Modal_message {

    constructor(type = "info") {

        this._type = type;
        this._content = null;
        this._modal = null;

    }

    ucFirst(str) {
        if (!str) return str;

        return str[0].toUpperCase() + str.slice(1);
    }


    create(container = "body", content) {

        if($(container).children("div.popups").length > 0)
            $(`${container} div.modal`).remove();

        const modal = $('<div/>', {

            id: 'modal'

        }).css({'display': 'none'});

        const modalWrapper = $('<div/>', {

            id: 'modal-wrapper'

        }).appendTo(modal);

        $('<button/>', {

            id: 'close-modal',
            text: 'X'

        }).appendTo(modalWrapper).click( () => this.closeModal() );

        if(this._type === "info")
        {
            $('<i/>', {

                class: 'fas fa-info-circle',
                id: 'icon-info'

            }).appendTo(modalWrapper);

            modalWrapper.addClass('modal-info');
        }

        else
        {
            $('<i/>', {

                class: 'fas fa-exclamation-triangle',
                id: 'icon-error'

            }).appendTo(modalWrapper);

            modalWrapper.addClass('modal-error');
        }

        const dataContainer = $('<div/>', {

            id: 'container'

        }).appendTo(modalWrapper);

        dataContainer.html(content);
        this._content = content;

        modal.appendTo(container);

        this._modal = modal;

    }

    openModal() {
        this._modal.css({"display": ""});
    }

    closeModal() {
        this._modal.css("display", "none");
    }


}

export {Modal_message}