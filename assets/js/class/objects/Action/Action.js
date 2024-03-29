
class Action {

    constructor() {
        this.__id = null;
        this.__name = null;
    }

    getId() {
        return this.__id;
    }

    setId(id) {

        if(typeof id !== 'number')
            throw new Error("Internal error : invalid typeof Action::setId() argument ! Argument must be 'int' ");

        this.__id = id;

        return this;
    }

    getName() {
        return this.__name;
    }

    setName(name) {

        if(typeof name !== 'string')
            throw new Error("Internal error : invalid typeof Action::setName() argument ! Argument must be 'string' ");

        this.__name = name;

        return this;
    }

}

export default Action;