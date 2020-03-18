import Action from "../Action/Action";
import Subject from "../Subject/Subject";
import Feature from "../Feature/Feature";


class Permission {

    constructor()
    {
        this.__id = null;
        this.__action = null;
        this.__subject = null;
        this.__feature = null;
        this.__users = null;
        this.__state = null;
    }

    getUserIndex(user) {
        return this.__users.findIndex( registeredUser => registeredUser.getId() === user.getId() );
    }

    userIsRegistered(user) {
        return this.getUserIndex(user) !== -1;
    }

    getId() {
        return this.__id;
    }

    setId(id) {

        if(typeof id !== 'number')
            throw new Error("Internal error : invalid typeof Permission::setId() argument ! Argument must be 'int' ");

        this.__id = id;

        return this;
    }

    getAction() {
        return this.__action;
    }

    setAction(action) {

        if(typeof action !== 'object' || !(action instanceof Action))
            throw new Error("Internal error : invalid typeof Permission::setAction() argument ! Argument must be instance of Action.js ");

        this.__action = action;

        return this;
    }

    getSubject() {
        return this.__subject;
    }

    setSubject(subject) {

        if(typeof subject !== 'object' || !(subject instanceof Subject))
            throw new Error("Internal error : invalid typeof Permission::setSubject() argument ! Argument must be instance of Subject.js ");

        this.__subject = subject;

        return this;
    }

    getFeature() {
        return this.__feature;
    }

    setFeature(feature) {

        if(typeof feature !== 'object' || !(feature instanceof Feature))
            throw new Error("Internal error : invalid typeof Permission::setFeature() argument ! Argument must be instance of Feature.js ");

        this.__feature = feature;

        return this;
    }

    getUsers() {
        return this.__users;
    }

    addUser(user) {

        if(!this.userIsRegistered(user)) {
            this.__users.push(user);
        }
        else {
            throw new Error(`Internal error : Permission with id '${ this.__id }' is already associate with an User which have id '${ user.getId() }' !`);
        }

    }

    removeUser(user) {

        if(this.userIsRegistered(user)) {
            this.__users.splice([this.getUserIndex(user)], 1);
        }
        else {
            throw new Error(`Internal error : Permission with id '${ this.__id }' is not associate with an User which have id '${ user.getId() }' !!`);
        }

    }

    getState() {
        return this.__state;
    }

    setState(state) {

        if(typeof state !== 'boolean')
            throw new Error("Internal error : invalid typeof Permission::setState() argument ! Argument must be 'boolean' ");

        this.__state = state;

        return this;
    }

}

export default Permission;