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

                const mediasDisplayedType = $(".main-media").data("media_displayed");
                const currentPage = $(".pages-container .page.current_page").text();
                const numberOfMediasToDisplay= $(e.currentTarget).val();

                $('.medias-list-container').html("<h1 style='text-align: center; font-weight: bold; width: 100%;'>Chargement en cours...</h1>");

                $.ajax({
                    url: "/get/more/medias/for/mediatheque",
                    type: "POST",
                    data: { 'mediasDisplayedType': mediasDisplayedType, 'page': currentPage, 'numberOfMediasToDisplay': numberOfMediasToDisplay },
                    success: (response) => {
                        // add card
                        console.log(response);

                        this.rebuildMediasCard(response.mediasToDisplayed);
                        this.rebuildPageList(response.numberOfPages);
                        this.rebuildNumberOfMediasDisplayedList(response.numberOfMediasAllowedToDisplayed, response.userMediasDisplayedChoice);

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

    rebuildMediasCard(mediasInfos)
    {

        let container = $('<div class="medias-list-container">');

        $.each( mediasInfos.medias, (index, mediaInfos) => {

            let card = this.buildMediasCard(mediaInfos.media, {products: mediaInfos.media_products, categories: mediaInfos.media_categories}, mediaInfos.media_type, mediasInfos.customer);
            $(card).appendTo( container );

        } )

        $('.medias-list-container').replaceWith(container);

    }

    buildMediasCard(media, media_associated_infos = { products: [], categories: [] }, mediaType, customer)
    {

        let card = `<div class="card" data-created_date="${ this.reformateDate(media.createdAt) }" data-media_type="${mediaType}" 

                                data-products="${ (media_associated_infos.products.length > 0) ? media_associated_infos.products.join(', ') : 0 }" 
                                
                                data-products="${ (media_associated_infos.categories.length > 0) ? media_associated_infos.categories.join(', ') : 0 }" >

                            <div class="card-header">
                                <div class="select-media-input-container">
                                    <input type="checkbox">
                                </div>
                                
                                <div class="media-actions-shorcuts-container">
                                    <div class="shorcut shorcut-diff-date-modification date-coming-soon">
                                        <i class="far fa-clock"></i>
                                    </div>
                    
                                    <div class="shorcut">
                                        <i class="fas fa-euro-sign"></i>
                                    </div>
                    
                                    <div class="shorcut">
                                        <i class="fas fa-link"></i>
                                    </div>
                    
                                    <div class="shorcut">
                                        <i class="far fa-clock"></i>
                                    </div>
                    
                                </div>
                                
                            </div>
                            
                            <div class="card-body">
                               <div class="media-miniature-container">`;
                            
        if(mediaType === 'image')
        {
            card += `<img class="media-miniature" src="/miniatures/${ customer }/images/low/${ media.id }.png"
                                        alt="/miniatures/${ customer }/images/low/${ media.id }.png">`
        }
        else
        {
            card += `<video class="media-miniature" controls>
                        <source src="/miniatures/${ customer }/videos/low/${ media.id }.mp4" type="${ media.mimeType }">
                    </video>`;
        }

        card += `</div>
                 <div class="media-name-container">
                    <span>${ media.name }</span>
                 </div>
                                
                 <div class="media-associated-items-container">

                 <div class="media-criterions-container associated-item">`;

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

        card += `</div> <div class="media-tags-container associated-item">`;

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

    reformateDate(date)
    {

        date = new Date(date);
        date.setMonth( date.getMonth() +1 );

        const year = date.getFullYear();
        const month = ( date.getMonth() < 10 ) ? '0' + date.getMonth() : date.getMonth();
        const day= date.getUTCDate();
        const hour = (date.getHours() < 10) ? '0' + date.getHours() : date.getHours();
        const minutes = (date.getMinutes() < 10) ? '0' + date.getMinutes() : date.getMinutes();
        const second = (date.getSeconds() < 10) ? '0' + date.getSeconds() : date.getSeconds();

        return year + '-' + month + '-' + day + ' ' + hour + ':' + minutes + ':' + second;

    }

    rebuildPageList(limit)
    {

        $(".pages-container").empty();

        let page = window.location.href.match(/\d*$/);
        let url = window.location.href;

        if(page[0] !== '')
        {
            url = url.replace(/\d*$/, '');
            console.log(url);
            console.log(page);
        }

        for (let i = 1; i <= limit ; i++)
        {
            $(`<a class='page ${ (i === 1) ? 'current_page' : '' }' href="${ url + i}">${i}</a>`).appendTo( $(".pages-container") );
        }

    }

    rebuildNumberOfMediasDisplayedList(choices, selected)
    {

        $('#displayed-elements-number-selection').empty();

        choices.forEach( (choice) => {

            $(`<option value="${choice}" ${ (choice === selected) ? 'selected' : '' }>${choice}</option>`).appendTo( $('#displayed-elements-number-selection') );

        } )

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