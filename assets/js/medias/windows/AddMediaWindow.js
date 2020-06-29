import { Filter } from "./Filter";
import { AdvancedProgrammingSetting } from "./AdvancedProgrammingSetting";

class AddMediaWindow{

    constructor() {
        this._$location = $('#add_media')
        this._$closeIcon = this._$location.find('button#add_media__head__actions__close')
        this._$advancedProgrammingButton = this._$location.find('#add_media__body__programming__infos__show')
        this._$advancedProgrammingOptions = this._$location.find('#add_media__body__programming__infos__body')
        this._$unwrapFilterOptionsListsButtons = this._$location.find('.add_media__body__sort__filter__unwrap')
        this.availablesFilters = [
                { name: 'criterions', multiple: true },
                { name: 'tags', multiple: true }
            ]
        this.$medias = this._$location.find('article.medias__choices')
        this._$filters = this.find$Filters( this.availablesFilters.map( filter => filter.name ) )
        this._filter = this.generateFilter( this.availablesFilters )

        this._advancedProgrammingSetting = new AdvancedProgrammingSetting()
        this._advancedProgrammingSetting.$mediasInputs = this.$medias.find('.medias__choices__id') ;
    }

    active( active, programmingInterface ){
        this.onClickOnCloseIconCloseWindow( active )
        this.onClickOnAdvancedProgrammingButtonToggleAdvancedProgrammingOptions( active, programmingInterface )
        this.onClickOnButtonUnWrapFilterOptionsList( active )
        this.onChangeFilterOptionsRefreshFilter( active )
        this.onClickApplyFilter( active )
    }

    generateFilter( filters ){
        let generatedFilter = new Filter()

        if( ! Array.isArray( filters ) ) throw new Error('invalid argument type')

        filters.filter( filter =>  typeof filter === 'object' && typeof filter.name === 'string');

        filters.forEach( filter => filter.multiple ? generatedFilter.addFilterEntry( filter.name, [] ) : generatedFilter.addFilterEntry( filter.name, null ) )

        return generatedFilter
    }

    find$Filters( filters ){
        if( ! Array.isArray( filters ) ) throw new Error('invalid argument type')
        filters.filter( filter =>  typeof filter === 'string' )

        return $( Object.values( this._$location.find('[data-filter]') ).filter( $ => $ instanceof HTMLElement && filters.includes( $.dataset.filter ) ) )
    }

    onChangeFilterOptionsRefreshFilter( active ) {
        if( active ){
            this._$filters.find('input').on('change.onChangeFilterOptionsRefreshFilter', e => {
                let $filterOptionInput = $( e.currentTarget )
                let filterName = $filterOptionInput.parents( '[data-filter]' ).data( 'filter' );
                let filterValue = parseInt( $filterOptionInput.val() );

                 $filterOptionInput.is(':checked') ? this._filter.add( filterName, filterValue ) : this._filter.remove( filterName, filterValue )

            })
        }else{ this._$filters.find('input').off('change.onChangeFilterOptionsRefreshFilter') }
    }

    onClickApplyFilter( active ) {
        if( active ) {
            let availablesFiltersNames = this.availablesFilters.map( availableFilter => availableFilter.name )
             this._$filters.siblings('button.comfirm').on('click.onClickApplyFilter', e => {

                 this.$medias.addClass('none');

                 console.log( this.$medias )
                 this.$medias.each( ( index , media ) => {

                     let $media = $( media )
                     availablesFiltersNames.forEach( ( availableFilterName , index ) => {

                         if( typeof $media.data( availableFilterName ) !== 'undefined' ){

                             let filtersData = $media.data( availableFilterName )

                             if( this.availablesFilters[ index ].multiple && Array.isArray( this._filter.filters[ availableFilterName ] ) && Array.isArray( filtersData ) ){

                                 this._filter.filters[ availableFilterName ].forEach( idToDisplay => {
                                     if(filtersData.includes( idToDisplay ) ) return this.show$Media( $media )
                                 })
                             }

                         }
                     })
                 })
            })
        }
    }

    show$Media( $mediaToDisplay ){
        if( $mediaToDisplay.hasClass('none') ) $mediaToDisplay.removeClass('none')
    }

    hide$Media( $mediaToHide  ){
        if( ! $mediaToHide.hasClass('none') ) $mediaToHide.addClass('none')
    }


    show(){
        this._$location.removeClass('none')
    }

    hide(){
        this._$location.addClass('none')
    }

    onClickOnButtonUnWrapFilterOptionsList( active ){
        if( active ){

            this._$unwrapFilterOptionsListsButtons.on('click.onClickOnButtonUnWrapFilterOptionsList', e => {
                $( e.currentTarget ).siblings('ul').toggleClass('flatten-s')
            })
        }else{ this._$unwrapFilterOptionsListsButtons.off('click.onClickOnButtonUnWrapFilterOptionsList') }
    }

    onClickOnCloseIconCloseWindow( active ){
        if( active ){
            this._$closeIcon.on('click.onClickOnCloseIconCloseWindow', () => {
                this.hide()
            })
        }else{ this._$closeIcon.off('click.onClickOnCloseIconCloseWindow') }
    }

    onClickOnAdvancedProgrammingButtonToggleAdvancedProgrammingOptions( active, programmingInterface ){
        if( active ){
            this._$advancedProgrammingButton.on('click.onClickOnAdvancedProgrammingButtonToggleAdvancedProgrammingOptions', () => {
                this._$advancedProgrammingOptions.toggleClass('flatten-mh')
                this._advancedProgrammingSetting.active( ! this._$advancedProgrammingOptions.hasClass( 'flatten-mh' ), programmingInterface );
            })
        }else{ this._$advancedProgrammingButton.off('click.onClickOnAdvancedProgrammingButtonToggleAdvancedProgrammingOptions') }
    }
}
export { AddMediaWindow }