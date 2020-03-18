
import {Observer} from "./Observer";


class Observable
{

    constructor() {
        this.__observers = [];

        this.__title = '__Observable__';
    }

    subscribe(observer) {

        if(typeof observer !== 'object' || !(observer instanceof Observer))
            throw new Error("Internal error ! Impossible to register observable because observer is not object or is not instance of Observer");

        this.__observers.push({ id: observer.identificator, observer: observer });

        console.log(`Info : new Observer (identificator: ${observer.identificator}) was recently registered in this Observable : '${this.__title}' !`);
    }

    unsubscribeAllObserver() {
        this.__observers = [];

        console.log(`Info : all Observers was removed from this Observable : '${this.__title}' !`);
    }

    unsubscribe(observer) {

        let observerRemoved = false;
        this.__observers.forEach( (registeredObserver,index) => {

            if(registeredObserver.id === observer.identificator) {
                this.__observers.splice(index, 1);
                observerRemoved = true;
            }

        } );

        if(observerRemoved)
            console.log(`Info : an Observer (identificator: ${observer.identificator}) was recently removed from this Observable : ${this.__title} !`);

        else
            console.log(`Info : Try to removed Observer (identificator: ${observer.identificator}) from '${this.__title}' Observable but it not yet registered !`);

    }

    notifyAllRegisteredObserver() {

        this.__observers.forEach( (registeredObserver,index) => {

            registeredObserver.observer.notify();

        } );

        console.log(`Info : All Observer was notified by this Observable : ${this.__title} !`);

    }

    notifyOnObserver(observer) {

        this.__observers.forEach( (registeredObserver,index) => {

            if(registeredObserver.id === observer.identificator) {
                registeredObserver.observer.notify();
                console.log(`Info : Observer (identificator: ${observer.identificator}) was notified by this Observable : ${this.__title} !`);
            }

        } );

    }

}

export {Observable}