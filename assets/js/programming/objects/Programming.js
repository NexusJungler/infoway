import {Broadcast} from "./Broadcast";
import {TimeSlot} from "./TimeSlot";

class Programming{

    constructor() {
        this._broadcasts = [] ;
        this._timeSlots = [] ;
        this._screensQty = 5 ;
    }

    get broadcasts() {
        return this._broadcasts;
    }

    addBroadcast( broadcast ) {
        if(  ! broadcast instanceof Broadcast ) throw new Error( 'invalid argument type' )
        if( ! this._broadcasts.includes( broadcast ) )this._broadcasts.push( broadcast )
    }


    get timeSlots() {
        return this._timeSlots;
    }

    addTimeSlot( timeSlot ) {
        if(  ! timeSlot instanceof TimeSlot ) throw new Error( 'invalid argument type' )
        if( ! this._timeSlots.includes( timeSlot ) )this._timeSlots.push( timeSlot )
    }


    get screensQty() {
        return this._screensQty;
    }

    set screensQty( screensQty) {
        this._screensQty = screensQty;
    }
}

export { Programming }