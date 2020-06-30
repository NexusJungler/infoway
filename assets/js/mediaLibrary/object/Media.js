class Media {

    constructor() {
        this._id = null;
        this._ext = null;
        this._name = null;
        this._src = null;
    }

    get id() {
        return this._id;
    }

    set id( id ) {
        this._id = id;
    }

    get ext() {
        return this._ext;
    }

    set ext( ext ) {
        this._ext =  ext ;
    }

    get name() {
        return this._name;
    }

    set name( name ) {
        this._name = name ;
    }

    get src() {
        return this._src;
    }

    set src( src ) {
        this._src =  src;
    }
}
export { Media }