class TimeSlot{
    constructor() {
        this._id = null;
        this._name = null;
        this._startAt = null;
        this._endAt = null ;
    }

    get id() {
        return this._id;
    }

    set id( id ) {
        if( typeof  id !== 'number' ) throw new Error('invalid argument type')
        this._id = id ;
    }

    get startAt() {
        return this._startAt;
    }

    set startAt( startAt ) {
        if( ! moment.isMoment( startAt ) || startAt._f  !== 'HHmmss') throw new Error( 'invalid property' )
        this._startAt = startAt
    }

    get endAt() {
        return this._endAt;
    }

    set endAt( endAt ) {
        if( ! moment.isMoment( endAt ) || endAt._f  !== 'HHmmss') throw new Error( 'invalid property' )
        this._endAt =  endAt
    }

    get name() {
        return this._name;
    }

    set name(value) {
        this._name = value;
    }


}

export { TimeSlot }