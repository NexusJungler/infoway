import {PlaylistEntry} from "./PlaylistEntry";

class ScreenPlaylist{

    constructor() {
        this._screenPosition = null ;
        this._entries = [] ;
    }

    get screenPosition() {
        return this._screenPosition;
    }

    set screenPosition(value) {
        this._screenPosition = value;
    }

    get entries() {
        return this._entries;
    }

    addEntry( entry ) {
        if( ! entry instanceof PlaylistEntry )throw new Error('invalid argument type')
        if( ! this._entries.includes( entry ) ){
            this._entries.push( entry )
            entry.positionInPlaylist = this._entries.length
        }
    }
}

export { ScreenPlaylist }