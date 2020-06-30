import {Media} from "../../mediaLibrary/object/Media";

class PlaylistEntry {

    constructor() {
        this._positionInPlaylist = null;
        this._media = null;
    }

    get positionInPlaylist() {
        return this._positionInPlaylist;
    }

    set positionInPlaylist( positionInPlaylist ) {
        this._positionInPlaylist = positionInPlaylist;
    }

    get media() {
        return this._medias;
    }

    set media( media ){
        if( ! media instanceof Media )throw new Error('invalid argument type')
        this._media = media ;
    }
}

export { PlaylistEntry }