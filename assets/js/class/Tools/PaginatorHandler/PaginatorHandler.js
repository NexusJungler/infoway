import Tool from "../Tool";

class PaginatorHandler extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$location = $('.pagination-container');
    }

    onDisplayedMediasNumberChange(active)
    {
        if(active)
        {
            this.__$location.find("#displayed-elements-number-selection").on("change.onDisplayedMediasNumberChange", e => {

                $.ajax({
                    url: "/update/mediatheque/medias/displayed/number",
                    type: "POST",
                    data: { 'number': $(e.currentTarget).val() },
                    success: () => {
                        location.reload();
                    },
                })

            })
        }
        else
        {
            this.__$location.find("#displayed-elements-number-selection").off("change.onDisplayedMediasNumberChange");
        }

        return this;
    }

    onMediasSortableByDateChange(active)
    {
        if(active)
        {
            $("#medias-sortable-by-date").on("change.onMediasSortableByDateChange", e => {

                const mediasCards = $(".medias-list-container .card");

                mediasCards.sort( this.sortByDate('created_date', $(e.currentTarget).val()) );

                $(".medias-list-container").html(mediasCards)

            })
        }
        else
        {
            $("#medias-sortable-by-date").off("change.onMediasSortableByDateChange")
        }

        return this;
    }

    sortByDate(key, order = 'desc')
    {
        return function innerSort(a, b) {

            const varA = new Date( $(a).data(key) );
            const varB = new Date( $(b).data(key) );

            let comparison = 0;
            if (varA > varB) {
                comparison = 1;
            } else if (varA < varB) {
                comparison = -1;
            }
            return (
                (order === 'desc') ? (comparison * -1) : comparison
            );
        };
    }

    enable()
    {
        super.enable();
        this.onDisplayedMediasNumberChange(true)
            .onMediasSortableByDateChange(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onDisplayedMediasNumberChange(false)
            .onMediasSortableByDateChange(false)
        ;
    }

}

export default PaginatorHandler;