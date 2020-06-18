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
        const numberOfMediasToDisplay= $("#displayed_elements_number_selection").val();

        page = parseInt(page);

        $('.medias_list_container').html("<h1 style='text-align: center; font-weight: bold; width: 100%;'>Chargement en cours...</h1>");

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
        })

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

                if( $(e.currentTarget).hasClass('prev') )
                {
                    if(currentPage.prev('a').length > 0)
                    {
                        currentPage.removeClass('current_page');
                        currentPage.prev('a').addClass('current_page');
                        this.reloadMediatheque( currentPage.prev('a').text() );
                    }
                }
                else if( $(e.currentTarget).hasClass('next') )
                {
                    if(currentPage.next('a').length > 0)
                    {
                        currentPage.removeClass('current_page');
                        currentPage.next('a').addClass('current_page');
                        this.reloadMediatheque( currentPage.next('a').text() );
                    }
                }

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

                this.reloadMediatheque();

            })
        }
        else
        {
            this.__$location.find("#displayed_elements_number_selection").off("change.onDisplayedMediasNumberChange");
        }

        return this;
    }

    rebuildMediasCards(mediasInfos)
    {

        let container = $('.medias_list_container');
        container.empty();

        let cards = '';

        $.each( mediasInfos.medias, (index, mediaInfos) => {

            let card = this.buildMediaCard(mediaInfos.media, {products: mediaInfos.media_products, categories: mediaInfos.media_categories, tags: mediaInfos.media_tags, criterions: mediaInfos.media_criterions}, mediaInfos.media_type, container.data('customer'));
            cards += card;
            //$(card).appendTo( container );

        } )

        container.html(cards);

    }

    buildMediaCard(media, media_associated_infos = { products: [], categories: [], tags: [], criterions: [] }, mediaType, customer)
    {

        const dateIsComingSoon = ( this.getDaysDiffBetweenDates(media.diffusionEnd, new Date()) <= 14);

        let card = `<div class="card" id="media_${ media.id }" data-created_date="${ this.reformateDate(media.createdAt) }" data-media_type="${mediaType}" data-orientation="${media.orientation}"

                                data-media_diff_start="${ this.reformateDate(media.diffusionStart, true, '/') }" data-media_diff_end="${ this.reformateDate(media.diffusionEnd, true, '/') }"

                                data-products="${ (media_associated_infos.products.length > 0) ? media_associated_infos.products.join(', ') : 'none' }" 
                                
                                data-categorys="${ (media_associated_infos.categories.length > 0) ? media_associated_infos.categories.join(', ') : 'none' }"
                                 
                                data-criterions="${ (media_associated_infos.criterions.length > 0) ? media_associated_infos.criterions.join(', ') : 'none' }"
                                 
                                data-tags="${ (media_associated_infos.tags.length > 0) ? media_associated_infos.tags.join(', ') : 'none' }"
                                 
                                 >

                            <div class="card_header">
                                <div class="select_media_input_container">
                                    <input type="checkbox" class="select_media_input">
                                </div>
                                
                                <div class="media_actions_shortcuts_container">
                                    <div class="shortcut shortcut_diff_date_modification ${ (dateIsComingSoon) ? 'date_coming_soon' : '' }">
                                        <i class="far fa-clock"></i>
                                    </div>
                    
                                    <div class="shortcut">
                                        <i class="fas fa-euro-sign"></i>
                                    </div>
                    
                                    <div class="shortcut">
                                        <i class="fas fa-link shortcut_product_association"></i>
                                    </div>
                    
                                    <div class="shortcut">
                                        <i class="far fa-clock"></i>
                                    </div>
                    
                                </div>
                                
                            </div>
                            
                            <div class="card_body">
                               <div class="media_miniature_container">`;
                            
        if(mediaType === 'image')
        {
            card += `<img class="media_miniature" src="/miniatures/${ customer }/images/low/${ media.id }.png"
                                        alt="/miniatures/${ customer }/images/low/${ media.id }.png">`
        }
        else
        {
            card += `<video class="media_miniature">
                        <source src="/miniatures/${ customer }/videos/low/${ media.id }.mp4" type="${ media.mimeType }">
                    </video>`;
        }

        card += `</div>
                 <div class="media_name_container">
                    <span>${ media.name }</span>
                 </div>
                                
                 <div class="media_associated_items_container">

                 <div class="media_criterions_container associated_item">`;

        if(media.products.length > 0)
        {
            media.products.forEach( product => {

                product.criterions.forEach( criterion =>  {

                    card += `<p class="criterion"><span></span>${ criterion.name }</p>`;

                } )

            } )
        }
        else
        {
            card += `<p>0 critères associés</p>`;
        }

        card += `</div> <div class="media_tags_container associated_item">`;

        if(media.tags.length > 0)
        {
            media.tags.forEach( tag => {

                card += `<p class="tag"><span></span>${ tag.name }</p>`;

            } )
        }
        else
        {
            card += `<p>0 tags associés</p>`;
        }

        card += `</div></div></div></div>`;

        return card;

    }

    reformateDate(date, onlyDate = false, dateSeparator = '-', clockSeparator = ':')
    {

        date = new Date(date);
        date.setMonth( date.getMonth() +1 );

        const year = date.getFullYear();
        const month = ( date.getMonth() < 10 ) ? '0' + date.getMonth() : date.getMonth();
        const day= date.getUTCDate();
        const hour = (date.getHours() < 10) ? '0' + date.getHours() : date.getHours();
        const minutes = (date.getMinutes() < 10) ? '0' + date.getMinutes() : date.getMinutes();
        const second = (date.getSeconds() < 10) ? '0' + date.getSeconds() : date.getSeconds();

        if(!onlyDate)
        {
            return year + dateSeparator + month + dateSeparator + day + ' ' + hour + clockSeparator + minutes + clockSeparator + second;
        }
        else
        {
            return year + dateSeparator + month + dateSeparator + day;
        }

    }

    getDaysDiffBetweenDates(date1, date2)
    {
        date1 = ( date1 instanceof Date) ? date1 : new Date(date1);
        date2 = ( date2 instanceof Date) ? date2 : new Date(date2);
        const diffTime = Math.abs(date1 - date2);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }

    rebuildPageList(limit, currentPage)
    {

        this.__$location.find('.pages_container').empty();

        if(limit > 1)
        {

            let url = window.location.href;

            let pageContainerContent = '';

            if(currentPage > 1)
            {
                pageContainerContent += '<i class="fas fa-chevron-left page_navigation_arrow prev"></i>';
                //$('<i class="fas fa-chevron-left page_navigation_arrow left"></i>').appendTo( $(".pages-container") );
            }

            for (let i = 1; i <= limit ; i++)
            {
                pageContainerContent += `<a class='page ${ (i === currentPage) ? 'current_page' : '' }' href="${ url + '/' + i}">${i}</a>`;
                //$(`<a class='page ${ (i === currentPage) ? 'current_page' : '' }' href="${ url + '/' + i}">${i}</a>`).appendTo( $(".pages-container") );
            }

            if(limit > currentPage)
            {
                pageContainerContent += '<i class="fas fa-chevron-right page_navigation_arrow next"></i>';
                //$('<i class="fas fa-chevron-right page_navigation_arrow right"></i>').appendTo( $(".pages-container") );
            }

            this.__$location.find('.pages_container').html( pageContainerContent );

        }

        this.pageNavigationIsActive = true;

    }

    rebuildNumberOfMediasDisplayedList(choices, selected)
    {

        $('#displayed_elements_number_selection').empty();

        choices.forEach( (choice) => {

            $(`<option value="${choice}" ${ (choice === selected) ? 'selected' : '' }>${choice}</option>`).appendTo( $('#displayed_elements_number_selection') );

        } )

    }

    onMediasSortableByDateChange(active)
    {
        if(active)
        {
            $("#medias_sortable_by_date").on("change.onMediasSortableByDateChange", e => {

                const mediasCards = $(".medias_list_container .card");

                mediasCards.sort( this.sortByDate('created_date', $(e.currentTarget).val()) );

                $(".medias_list_container").html(mediasCards)

            })
        }
        else
        {
            $("#medias_sortable_by_date").off("change.onMediasSortableByDateChange")
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
            .onClickOnPageReloadMediatheque(true)
            .onClickOnArrowNavigateToPage(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onDisplayedMediasNumberChange(false)
            .onMediasSortableByDateChange(false)
            .onClickOnPageReloadMediatheque(false)
            .onClickOnArrowNavigateToPage(false)
        ;
    }

}

export default PaginatorHandler;