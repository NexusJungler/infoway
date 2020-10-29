import Tool from "../Tool";

class PaginatorHandler extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$location = $('.pagination_container');
        this.pageNavigationIsActive = true;

    }

    reloadMediatheque(page = 1)
    {

        const mediasDisplayedType = $(".main-media").data("media_displayed");
        //const numberOfMediasToDisplay= $("#displayed_elements_number_selection").val();

        window.location = `/mediatheque/${mediasDisplayedType}/${page}`;

        /*$('.medias_list_container').html("<h1 style='text-align: center; font-weight: bold; width: 100%;'>Chargement en cours...</h1>");

        this.pageNavigationIsActive = false;

        $.ajax({
            url: `/mediatheque/${mediasDisplayedType}`,
            type: "POST",
            data: { 'mediasDisplayedType': mediasDisplayedType, 'page': page, 'numberOfMediasToDisplay': numberOfMediasToDisplay },
            success: (response) => {

                console.log(response);

                this.rebuildMediasCards(response.mediasToDisplayed);
                this.rebuildPageList(response.numberOfPages, page);
                this.rebuildNumberOfMediasDisplayedList(response.numberOfMediasAllowedToDisplayed, response.userMediasDisplayedChoice);

                this.getToolBox().getTool('FilterMediasTool').getAgainMediaListContainer();
                this.getToolBox().getTool('PopupHandler').getAgainMediaListContainer();

            },
        });*/

    }

    onClickOnPageReloadMediatheque(active)
    {
        if(active)
        {
            this.__$location.on('click.onClickOnPageReloadMedia', '.page', e => {

                e.preventDefault();

                if(this.pageNavigationIsActive)
                    this.reloadMediatheque( $(e.currentTarget).text() );

            })
        }
        else
        {
            this.__$location.off('click.onClickOnPageReloadMedia', '.page');
        }

        return this;
    }

    onClickOnArrowNavigateToPage(active)
    {

        if(active)
        {
            this.__$location.on('click.onClickOnArrowNavigateToPage', '.page_navigation_arrow', e => {

                let currentPage = $('.page.current_page');
                let currentPageText = parseInt( $('.page.current_page').text() );

                if( ( $(e.currentTarget).hasClass('prev') && currentPage.prev('a.page').length > 0) ||
                    ( $(e.currentTarget).hasClass('next') && currentPage.next('a.page').length > 0) )
                {

                    let nextOrPrevPage = ( $(e.currentTarget).hasClass('prev') ) ? currentPage.prev('a.page') : currentPage.next('a.page');

                    currentPage.removeClass('current_page');

                    if(nextOrPrevPage.text() === '...')
                    {
                        nextOrPrevPage.text( ($(e.currentTarget).hasClass('prev')) ? currentPageText  - 1 : currentPageText  + 1 );
                    }

                    nextOrPrevPage.addClass('current_page');
                    this.reloadMediatheque( nextOrPrevPage.text() );

                }



                /*if( $(e.currentTarget).hasClass('prev') )
                {
                    if(currentPage.prev('a.page').length > 0)
                    {


                    }
                }
                else if( $(e.currentTarget).hasClass('next') )
                {
                    if(currentPage.next('a.page').length > 0)
                    {

                        let nextPage = currentPage.next('a.page');

                        currentPage.removeClass('current_page');

                        if(nextPage.text() === '...')
                        {
                            nextPage.text(currentPageText  - 1);
                        }

                        nextPage.addClass('current_page');
                        this.reloadMediatheque( nextPage.text() );
                    }
                }*/

            })
        }
        else
        {
            this.__$location.off('click.onClickOnArrowNavigateToPage', '.page_navigation_arrow');
        }

        return this;
    }

    onDisplayedMediasNumberChange(active)
    {
        if(active)
        {
            this.__$location.find("#displayed_elements_number_selection").on("change.onDisplayedMediasNumberChange", e => {

                $('.medias_list_container').html("<h1 style='text-align: center; font-weight: bold; width: 100%;'>Chargement en cours...</h1>");

                this.pageNavigationIsActive = false;

                $.ajax({
                    url: `/update/mediatheque/medias/number`,
                    type: "POST",
                    data: { 'mediatheque_medias_number': $(e.currentTarget).val() },
                    success: (response) => {

                        console.log(response);

                        this.reloadMediatheque();

                    },
                    error: () => {

                        alert("Impossible de update la variable session!")

                    },
                });

                /*$('.medias_list_container').html("<h1 style='text-align: center; font-weight: bold; width: 100%;'>Chargement en cours...</h1>");

                const mediasDisplayedType = $(".main-media").data("media_displayed");

                this.pageNavigationIsActive = false;

                $.ajax({
                    url: `/mediatheque/${mediasDisplayedType}`,
                    type: "POST",
                    data: { 'mediasDisplayedType': mediasDisplayedType, 'page': 1, 'numberOfMediasToDisplay': $(e.currentTarget).val() },
                    success: (response) => {

                        console.log(response);

                        this.rebuildMediasCards(response.mediasToDisplayed);
                        this.rebuildPageList(response.numberOfPages, page);
                        this.rebuildNumberOfMediasDisplayedList(response.numberOfMediasAllowedToDisplayed, response.userMediasDisplayedChoice);

                        this.getToolBox().getTool('FilterMediasTool').getAgainMediaListContainer();
                        this.getToolBox().getTool('PopupHandler').getAgainMediaListContainer();

                    },
                });*/

            })
        }
        else
        {
            this.__$location.find("#displayed_elements_number_selection").off("change.onDisplayedMediasNumberChange");
        }

        return this;
    }

    onMediasSortableTypeChange(active)
    {
        if(active)
        {
            $("#medias_sortable_by_date").on("change.onMediasSortableTypeChange", e => {

                const mediasCards = $(".medias_list_container_cards .card");

                if( $(e.currentTarget).val() === 'asc' || $(e.currentTarget).val() === 'desc' )
                    mediasCards.sort( this.sortByDate('created_date', $(e.currentTarget).val()) );

                else if( $(e.currentTarget).val() === 'alpha_num_asc' || $(e.currentTarget).val() === 'alpha_num_desc' )
                    mediasCards.sort( this.sortByAlphaNum($(e.currentTarget).val()) );

                else
                    debugger

                $(".medias_list_container_cards").html(mediasCards)

            })
        }
        else
        {
            $("#medias_sortable_by_date").off("change.onMediasSortableTypeChange")
        }

        return this;
    }

    sortByDate(key, order = 'desc')
    {
        return (a, b) => {

            const varA = new Date( $(a).data(key) );
            const varB = new Date( $(b).data(key) );

            if( !(varA instanceof Date) || !(varB instanceof Date) )
                throw new Error(`Can't create an instance of Date with this key ! (key : '${ key }'`);

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

    sortByAlphaNum(order = 'alpha_num_asc')
    {

        return (a, b) => {

            a = $(a).find('.media_name').text();
            b = $(b).find('.media_name').text();

            a = typeof a === 'string' ? a.toLowerCase() : a.toString();
            b = typeof b === 'string' ? b.toLowerCase() : b.toString();

            return (order === "alpha_num_asc") ? a.localeCompare(b) : b.localeCompare(a);

        }
    }

    enable()
    {
        super.enable();
        this.onDisplayedMediasNumberChange(true)
            .onMediasSortableTypeChange(true)
            .onClickOnPageReloadMediatheque(true)
            .onClickOnArrowNavigateToPage(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onDisplayedMediasNumberChange(false)
            .onMediasSortableTypeChange(false)
            .onClickOnPageReloadMediatheque(false)
            .onClickOnArrowNavigateToPage(false)
        ;
    }

}

export default PaginatorHandler;