import { BroadcastSlot } from "./BroadcastSlot";

class Broadcast{

    constructor() {
        this._startAt = null;
        this._endAt = null;
        this._broadcastSlots = [] ;
    }


    get startAt() {
        return this._startAt;
    }

    set startAt( startAt ) {
        this._startAt =  startAt ;
    }

    get endAt() {
        return this._endAt;
    }

    set endAt( endAt ) {
        this._endAt =  endAt  ;
    }


    get broadcastSlots() {
        return this._broadcastSlots;
    }

    addBroadcastSlot( broadcastSlot ) {
        if( ! broadcastSlot instanceof BroadcastSlot ) throw new Error( 'invalid argument type' )
        if( ! this._broadcastSlots.includes( broadcastSlot ) ) this._broadcastSlots.push( broadcastSlot )
    }
}
export { Broadcast }