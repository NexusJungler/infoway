class Filter{

    constructor() {
        this._filters = {};
    }


    get filters() {
        return this._filters;
    }

    set filters(value) {
        this._filters = value;
    }

    addFilterEntry( name, defaultValue = null ){
        if( typeof name !== 'string') throw new Error('invalid entry name : must be a string')

        if( ! this.isEntryExist( name ) ) this._filters[ name ] = defaultValue
    }

    isEntryExist( entryName ){
        if( typeof entryName !== 'string' )throw new Error('invalid entry name : must be a string')
        return Object.keys( this._filters ).includes( entryName )
    }
    removeFilterEntry( name ){
        if( typeof name !== 'string') throw new Error('invalid entry name : must be a string')

        if( this.isEntryExist( name ) ) delete this._filters[ name ] ;
    }

    add( name , value ){
        if( typeof name !== 'string' || ! this.isEntryExist( name ) ) throw new Error('invalid filter name')

        if(  ! Array.isArray( this._filters[ name ] ) ) this._filters[ name ] = [] ;
        if( ! this._filters[ name ].includes( value ) ) this._filters[ name ].push( value )  ;
    }

    remove( name , value ){
        if( typeof name !== 'string' || ! this.isEntryExist( name ) ) throw new Error('invalid filter name')

        if( ! Array.isArray( this._filters[ name ] ) )throw new Error('Impossible to find entries list for this filter')

        let indexOfValue = this._filters[ name ].indexOf( value )

        if( indexOfValue > -1  ) this._filters[ name ].splice( indexOfValue, 1 )  ;
    }
}

export { Filter }