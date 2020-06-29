import {ScreenPlaylist} from "./ScreenPlaylist";
import {TimeSlot} from "./TimeSlot";

class BroadcastSlot{
    constructor() {
        this._timeSlot = null;
        this._playlists = [] ;
    }

    get playlists() {
        return this._playlists;
    }

    addPlaylist( playlist ) {
        if( ! playlist instanceof ScreenPlaylist )throw new Error('invalid argument type')
        if( ! this._playlists.includes( playlist ) )this._playlists.push( playlist )
    }


    get timeSlot() {
        return this._timeSlot;
    }

    set timeSlot( timeSlot ) {
        if( ! timeSlot instanceof TimeSlot )throw new Error('invalid argument type')
        this._timeSlot =  timeSlot ;
    }
}

export { BroadcastSlot }