import Tool from "../Tool";

class CustomerCreatorHandler extends Tool
{

    constructor()
    {
        super();
        this.__name = "CustomerCreatorHandler";
        this.__$form = $("#createCustomer form");
        this.__contactFields = [
            {
                name: "lastname",
                type: "text",
                placeholder: "Nom"
            },
            {
                name: "firstname",
                type: "text",
                placeholder: "Prenom"
            },
            {
                name: "email",
                type: "email",
                placeholder: "Email"
            },
            {
                name: "phonenumber",
                type: "tel",
                placeholder: "Tel"
            },
            {
                name: "status",
                type: "text",
                placeholder: "Status"
            },
        ];
    }

    onClickOnAddContactButtonAddInputs(active)
    {

        if(active)
        {
            this.__$form.find("button#add_contact").on("click.onClickOnAddContactButtonAddInputs", e => {

                let contactsContainer = this.__$form.find("#contacts_container");
                const contactFieldsNumber = contactsContainer.find(".contact").length;

                let newContactContainer = $("<div/>", {
                    class: "contact",
                    id: `contact_${contactFieldsNumber}`
                });

                this.__contactFields.forEach( field => {

                    $("<input>", {
                        type: field.type,
                        placeholder: field.placeholder,
                        name: `contacts[${contactFieldsNumber}][${field.name}]`,
                        required: true
                    }).appendTo(newContactContainer);

                } )

                $("<button>", {
                    text: "Supprimer",
                    type: "button",
                    class: "delete_contact",
                }).data('target', `contact_${contactFieldsNumber}`)
                    .appendTo(newContactContainer);

                newContactContainer.appendTo(contactsContainer);

            });
        }
        else
        {
            this.__$form.find("button#add_contact").off("click.onClickOnAddContactButtonAddInputs");
        }

        return this;

    }

    onClickOnDeleteContactRemoveInputs(active)
    {

        if(active)
        {
            this.__$form.on("click.onClickOnDeleteContactRemoveInputs", ".delete_contact", e => {

                this.__$form.find(`#contacts_container #${$(e.currentTarget).data('target')}`).remove();

            });
        }
        else
        {
            this.__$form.off("click.onClickOnDeleteContactRemoveInputs", ".delete_contact");
        }

        return this;

    }

    enable()
    {
        super.enable();
        this.onClickOnAddContactButtonAddInputs(true)
            .onClickOnDeleteContactRemoveInputs(true);
    }

    disable()
    {
        super.disable();
        this.onClickOnAddContactButtonAddInputs(false)
            .onClickOnDeleteContactRemoveInputs(false);
    }

}

export default CustomerCreatorHandler;