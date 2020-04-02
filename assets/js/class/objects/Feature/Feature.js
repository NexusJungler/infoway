
class Feature {

    constructor() {
        this.__id = null;
        this.__name = null;
        this.__branch = null;
    }

    getId() {
        return this.__id;
    }

    setId(id) {

        if(typeof id !== 'number')
            throw new Error("Internal error : invalid typeof Feature::setId() argument ! Argument must be 'int' ");

        this.__id = id;

        return this;
    }

    getName() {
        return this.__name;
    }

    setName(name) {

        if(typeof name !== 'string')
            throw new Error("Internal error : invalid typeof Feature::setName() argument ! Argument must be 'string' ");

        this.__name = name;

        return this;
    }

    getBranch() {
        return this.__branch;
    }

    setBranch(branch) {

        if(typeof branch !== 'string')
            throw new Error("Internal error : invalid typeof Feature::setBranch() argument ! Argument must be 'string' ");

        this.__branch = branch;

        return this;
    }

}

export default Feature;