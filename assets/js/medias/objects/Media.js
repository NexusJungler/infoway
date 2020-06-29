class Media{

    constructor() {
        this._id = null;
        this._name = null;
        this._src = null;
        this._ext = null;
    }

    get id() {
        return this._id;
    }

    set id(value) {
        this._id = value;
    }

    get name() {
        return this._name;
    }

    set name(value) {
        this._name = value;
    }

    get src() {
        return this._src;
    }

    set src(value) {
        this._src = value;
    }

    get ext() {
        return this._ext;
    }

    set ext(value) {
        this._ext = value;
    }
}

export { Media }