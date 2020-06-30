import { AddMediaWindow } from "../medias/windows/AddMediaWindow" ;
import { Broadcast } from "./objects/Broadcast";
import { Programming } from "./objects/Programming";
import { BroadcastSlot } from "./objects/BroadcastSlot";
import { PlaylistEntry } from "./objects/PlaylistEntry";
import {ScreenPlaylist} from "./objects/ScreenPlaylist";
import {Media} from "../mediaLibrary/object/Media";
import {TimeSlot} from "./objects/TimeSlot";

class ProgrammingInterface {

    constructor() {
        this._addMediaWindow = new AddMediaWindow();

        this._$location = $('.main_programming');
        this._$programmingForm = this._$location.find('#new_programming_mould_form');
        this._$broadCastsContainer = this._$location.find('.displays_forms_container')
        this._$addMediaButton = this._$location.find('#button__add_media');

        this._programming = null;
        this._$broadcastsInInterface = {}
    }

    registerBroadcastFromInterface( $broadcastInInterface, broadcast ){
        this._$broadcastsInInterface[ $broadcastInInterface.data('index') ] = {
            broadcast : broadcast,
            $broadcast : $broadcastInInterface
        } ;
    }

    init() {
        this.active( true )
        this.programming = this.genereProgrammingFromHTML()
    }


    get programming() {
        return this._programming;
    }

    set programming( programming ) {
        if( ! programming instanceof Programming )throw new Error( 'Invalid argument for programming' )

        this._programming = programming;
    }

    genereBroadcastFromHTML( $broadcastHTML ){
        try{
            console.log( $broadcastHTML )
            debugger ;
            let broadcastObject = new Broadcast()

            let $broadcastStartAtInput = $( $broadcastHTML.find( '.programming__broadcast__start_at__choices' ).get( 0 ) )
            broadcastObject.startAt = moment( $broadcastStartAtInput.val(), 'YYYY-MM-DD');

            let $broadcastEndAtInput = $( $broadcastHTML.find( '.programming__broadcast__end_at__choices' ).get( 0 ) )
            broadcastObject.endAt = moment( $broadcastEndAtInput.val(), 'YYYY-MM-DD') ;

            let $broadcastSlots = $broadcastHTML.find('.broadcast_slot')
            $broadcastSlots.each( ( index, broadcastSlot ) => {
                let broadcastSlotObject = this.genereBroadcastSlotFromHTML( $( broadcastSlot ) )
                broadcastObject.addBroadcastSlot( broadcastSlotObject )
            });
            this.registerBroadcastFromInterface( $broadcastHTML, broadcastObject )
            console.log( this._$broadcastsInInterface )
            debugger ;
            return broadcastObject ;

        }catch( e ){ console.log( e ) }
    }

    genereScreenPlaylistFromHTML( $screenPlaylistHTML, index ){
        try{
            let playlistObject = new ScreenPlaylist() ;
            console.log( $screenPlaylistHTML )
            debugger ;
            // broadCastSlotObject.
            playlistObject.screenPosition = $screenPlaylistHTML.data('screenpos')

            $screenPlaylistHTML
                .find('.playlist__playlist_entries')
                .each( ( index , entry ) => {
                    let entryObject = this.generePlaylistEntryFromHTML( $( entry ) , index )
                    playlistObject.addEntry( entryObject )
                })
            return playlistObject ;

        }catch( e ){ console.log( e ) }
    }

    generePlaylistEntryFromHTML( $playlistEntryHTML, index ){
        try{
            let playlistEntry = new PlaylistEntry() ;

            playlistEntry.positionInPlaylist = index

            let $mediaHTML = $ ( $playlistEntryHTML.find('.broadcast_slot__playlist__entry__visual').get( 0 ) )

            playlistEntry.media =  this.genereMediaFromHTML( $mediaHTML )

            return playlistEntry
        }catch( e ){ console.log( e ) }
    }

    genereMediaFromHTML ( $mediaHTML ){
        try{
            let mediaObject = new Media() ;

            mediaObject.id = $mediaHTML.find('input').val()
            mediaObject.src = $mediaHTML.find('img').prop('src')
            mediaObject.name = $mediaHTML.find('span').text()

            return mediaObject ;
        }catch( e ){ console.log( e ) }
    }

    genereTimeSlotFromHTML( $timeSlotHTML ){
        console.log(  $timeSlotHTML )
        try{
            let timeSlotObject = new TimeSlot()

            let $broadCastSlotNameInput = $( $timeSlotHTML.find('.time_slot__name').get( 0 ) )
            $broadCastSlotNameInput.is('input')
            timeSlotObject.name = $broadCastSlotNameInput.is('input') ? $broadCastSlotNameInput.val() : $broadCastSlotNameInput.text()

            let $broadCastSlotStartAt = $( $timeSlotHTML.find('.time_slot__start_at').get( 0 ) )
            timeSlotObject.startAt =  $broadCastSlotStartAt.is('input') ? moment( $broadCastSlotStartAt.val(), 'HHmmss' ) : moment( $broadCastSlotStartAt.text(), 'HHmmss')


            let $broadCastSlotEndAt = $( $timeSlotHTML.find('.time_slot__end_at').get( 0 ) )

            timeSlotObject.endAt = $broadCastSlotEndAt.is('input') ? moment( $broadCastSlotEndAt.val(), 'HHmmss' ) : moment( $broadCastSlotEndAt.text(), 'HHmmss' )

            console.log( timeSlotObject )
            debugger;

            return timeSlotObject

        }catch( e ){ console.log( e ) }
    }
    genereBroadcastSlotFromHTML( $broadcastSlotHTML ){
        try{
            let broadCastSlotObject = new BroadcastSlot()

            let $timeSlot =  $broadcastSlotHTML.find('.broadcast_slot__infos__slot');

            broadCastSlotObject.timeSlot = this.genereTimeSlotFromHTML( $timeSlot )

            $broadcastSlotHTML
                .find('.broadcast_slot__playlist')
                .each( ( index , playlist ) => {
                    let playlistObject = this.genereScreenPlaylistFromHTML( $( playlist ), index )
                    broadCastSlotObject.addPlaylist( playlistObject )
                } )

            return broadCastSlotObject

        }catch( e ){ console.log( e ) }
    }

    cloneBroadcast( broadCastToClone , newStartAt, newEndAt ){
        var newBroadcast = _.cloneDeep( broadCastToClone )
        newBroadcast.startAt = newStartAt
        newBroadcast.endAt = newEndAt
        return newBroadcast
    }
    addProgramming( programmingObject ){

        console.log( programmingObject )

        programmingObject.broadcasts.forEach( broadcastToAdd => {
            console.log(broadcastToAdd);
            console.log( broadcastToAdd.startAt.format() )
            console.log( broadcastToAdd.endAt.format() )
            let affectedBroadcasts = this.programming.broadcasts.filter(broadcast =>
                (broadcast.startAt >= broadcastToAdd.startAt && broadcast.startAt <= broadcastToAdd.endAt) ||
                (broadcast.endAt >= broadcastToAdd.startAt && broadcast.endAt <= broadcastToAdd.endAt)
            )
            console.log( affectedBroadcasts )
            if( affectedBroadcasts.length > 0 ){
                let prevProgEndDate = null
                for (let i = 0; i < affectedBroadcasts.length; i++) {
                    let affectedBroadcast = affectedBroadcasts[i];
                    console.log(  affectedBroadcast )
                    debugger;
                    if (i === 0) {

                        prevProgEndDate = moment( broadcastToAdd.startAt.format(), 'YYYY-MM-DD' )
                        console.log( prevProgEndDate.format() )
                        debugger;

                        if (affectedBroadcast.startAt < broadcastToAdd.startAt) {
                            this.programming.addBroadcast(
                                this.cloneBroadcast(
                                    affectedBroadcast,
                                    moment(affectedBroadcast.startAt),
                                    moment(broadcastToAdd.startAt.format(), 'YYYY-MM-DD')
                                )
                            )
                            console.log( affectedBroadcast )
                            debugger ;
                            affectedBroadcast.startAt = moment(broadcastToAdd.startAt.format(), 'YYYY-MM-DD')
                        }
                    }
                    if ( i === affectedBroadcasts.length - 1 ) {

                        if (affectedBroadcast.endAt > broadcastToAdd.endAt) {
                            this.programming.addBroadcast(
                                this.cloneBroadcast(
                                    affectedBroadcast,
                                    moment(broadcastToAdd.endAt.format(), 'YYYY-MM-DD'),
                                    moment(affectedBroadcast.endAt),
                                )
                            )
                            affectedBroadcast.endAt = moment(broadcastToAdd.endAt.format(), 'YYYY-MM-DD')
                        }

                        if( broadcastToAdd.endAt > affectedBroadcast.endAt ){
                            console.log('iciii')
                            debugger;
                            this.programming.addBroadcast(
                                this.cloneBroadcast(
                                    broadcastToAdd,
                                    moment(affectedBroadcast.endAt).add(1, 'days'),
                                    moment(broadcastToAdd.endAt.format(), 'YYYY-MM-DD'),
                                )
                            )
                        }
                    }
                    console.log( prevProgEndDate )
                    console.log( affectedBroadcast.startAt )
                    debugger;
                    if( moment.isMoment( prevProgEndDate ) && prevProgEndDate < affectedBroadcast.startAt ){
                        console.log( prevProgEndDate )
                        console.log( affectedBroadcast.startAt )
                        debugger ;
                        this.programming.addBroadcast(
                            this.cloneBroadcast(
                                broadcastToAdd,
                                moment( prevProgEndDate ),
                                moment( affectedBroadcast.startAt ).subtract(1,'days'),
                            )
                        )
                    }

                    broadcastToAdd.broadcastSlots.forEach( broadcastSlotToAdd => {

                        let affectedBroadcastSlots = affectedBroadcast.broadcastSlots.filter( broadcastSlotFromAffectedBroadcast => {
                            return (
                                broadcastSlotFromAffectedBroadcast.timeSlot.startAt.isSame( broadcastSlotToAdd.timeSlot.startAt ) &&
                                broadcastSlotFromAffectedBroadcast.timeSlot.endAt.isSame( broadcastSlotToAdd.timeSlot.endAt )
                            )
                        })
                        console.log( affectedBroadcastSlots )
                        debugger ;
                        broadcastSlotToAdd.playlists.forEach( playlistToAdd => {

                            affectedBroadcastSlots.forEach( affectedBroadcastSlot => {

                                let affectedPlaylists = affectedBroadcastSlot.playlists.filter( playlistFromAffectedBroadcastSlot => playlistFromAffectedBroadcastSlot.screenPosition === playlistToAdd.screenPosition )
                                affectedPlaylists.forEach(affectedPlaylist => {

                                    playlistToAdd.entries.forEach( entryFromPlaylistToAdd => {
                                        affectedPlaylist.addEntry( entryFromPlaylistToAdd )
                                    })
                                })
                            })
                        })

                    })
                }
            }else{
                this.programming.addBroadcast( _.cloneDeep( broadcastToAdd ) )
            }
        })

        this.programming.broadcasts.forEach(broadcast => {
            console.log( broadcast.startAt.format() )
            console.log( broadcast.endAt.format() )
            debugger;
        })
        console.log( this.programming )
        debugger ;
    }

    refreshInterface(){
        console.log( this.programming )
        this.programming.broadcasts.forEach( broadcast => {

            let broadcastInInterfaceIndexes = Object.keys( this._$broadcastsInInterface )
            let broadcastInInterfaceIndexKey = broadcastInInterfaceIndexes.findIndex( broadcastIndex => this._$broadcastsInInterface[ broadcastIndex ].broadcast === broadcast )
            if( broadcastInInterfaceIndexKey > -1 ){
                console.log( broadcastInInterfaceIndexes )
                console.log( broadcastInInterfaceIndexKey )
                debugger ;
                let broadcastInInterfaceDatas = this._$broadcastsInInterface[ broadcastInInterfaceIndexes[ broadcastInInterfaceIndexKey ] ]
                this.refreshBroadcastInInterface( broadcast, broadcastInInterfaceDatas.$broadcast )
            }
        })
        debugger;
    }
    refreshBroadcastInInterface( broadcast, $location ){
        let $broadcastSlots = $location.find('li.broadcast_slot')
        console.log( $broadcastSlots );
        debugger ;
        broadcast.broadcastSlots.forEach( ( broadcastSlot, index) => {

            console.log( broadcastSlot )
            debugger ;
            let $broadcastSlot = $( $broadcastSlots.get( index ) )  ;
            let $playlists = $broadcastSlot.find('li.broadcast_slot__content__playlists__list__item')
            console.log( $playlists )
            debugger;
            broadcastSlot.playlists.forEach( ( playlist, index )  => {
                console.log( playlist );
                debugger ;
                let $playlist =  $playlists.find(`[data-screenpos=${ playlist.screenPosition }]`)
                let $entries = $playlist.find('.broadcast_slot__playlist__entry')
                playlist.entries.forEach( entry => {
                    console.log(entry)
                    let media = entry.media ;
                    console.log( $entries)
                    debugger;
                    let $entry = $entries.filter(`[data-posinplaylist=${entry.positionInPlaylist}]`)
                    /*if( $entry.length < 1  )*/
                        let $media = $entry.find('.playlist__media')
                    $media.find('.playlist__media__visual').prop( 'src', media.src )
                    $media.find('.input').val( media.id )

                    console.log( $media )
                    console.log( $entry );
                    debugger ;
                } )
                console.log( $playlist )
                debugger ;
                // let $playlist =
            })
        })
        console.log( broadcast, $location )
        broadcast
        debugger ;
    }
    genereProgrammingFromHTML() {
        try {
            let programmingObject = new Programming ;

            this._$programmingForm.find('.mould__timeslots__timeslot').each( ( index, timeSlotChoice ) => {
                let timeSlotObject = this.genereTimeSlotFromHTML( $( timeSlotChoice ) )
                programmingObject.addTimeSlot( timeSlotObject )

            })
            this._$programmingForm.find('.display_form').each( ( index , broadcastForm ) => {
                let broadcastObject = this.genereBroadcastFromHTML( $( broadcastForm ) )
                console.log( this._$broadcastsInInterface );
                debugger;
                programmingObject.addBroadcast( broadcastObject )
            })

            return programmingObject

        }catch( e ) { console.log( e ) }
    }


    active( active ){
        this._addMediaWindow.active( active, this )
        this.onClickOnAddMediaButtonShowAddMediaWindow( active )
    }

    onClickOnAddMediaButtonShowAddMediaWindow( active ){
        if( active ){
            this._$addMediaButton.on('click.onClickOnAddMediaButtonShowAddMediaWindow',e => {
                e.preventDefault()
                this._addMediaWindow.show()
            } )
        }else{ this._$addMediaButton.off('click.onClickOnAddMediaButtonShowAddMediaWindow') }
    }

    generateNewCollectionHTMLElement( $container, prototype ){
        try{

            let index = $container.children('li').length;

            let newForm = prototype;

            newForm = newForm.replace(/__name__/g, index);

            $container.data('index', index + 1);

            return $('<li></li>').append( newForm );

        }catch( e ){ console.log( e  ) }
    }

    generateBroadcastSlowHTMLElement( timeSlot, $broadcastSlotsContainer, prototype ){
        try{

            let $broadcastSlotElement = this.generateNewCollectionHTMLElement( $broadcastSlotsContainer, prototype )
            let $timeSlotDisplayer  = $broadcastSlotElement.find('.broadcast_slot__infos__slot')

            $timeSlotDisplayer.find('h3.broadcast_slot__infos__slot__name').text( timeSlot.name )
            $timeSlotDisplayer.find('span.broadcast_slot__infos__slot__times__start_at').text( timeSlot.startAt.format('HH:mm') )
            $timeSlotDisplayer.find('span.broadcast_slot__infos__slot__times__end_at').text( timeSlot.endAt.format('HH:mm') )

            return $broadcastSlotElement ;

        }catch ( e ){ console.log( e ) }
    }


    addNewDisplayHTMLElement() {
        try{
            let $originalBroadCastsContainer = this._$programmingForm.find( '.displays_forms_container[data-prototype]' )
            let $displayHTML = this.generateNewCollectionHTMLElement( this._$broadCastsContainer, $originalBroadCastsContainer.data('prototype') )

            let $timeSlotHTML = $originalBroadCastsContainer.find('.display_form__infos__select_timeslot').html()
            $displayHTML.find('.display_form__infos__select_timeslot').html( $timeSlotHTML )

            let $broadcastSlotsContainer = $displayHTML.find( '.programming__broadcast__slots' )

            let $broadCastSlotsOriginalContainer = $( '.programming__broadcast__slots[data-prototype]' )

            this._programming.timeSlots.forEach( timeSlot => {
                let $broadcastSlotElement = this.generateBroadcastSlowHTMLElement( timeSlot, $broadcastSlotsContainer, $broadCastSlotsOriginalContainer.data('prototype') )
                $broadcastSlotsContainer.append( $broadcastSlotElement )
            });

            this._$broadCastsContainer.append( $displayHTML )

        }catch( e ){ console.log( e ) }

    }
}

export { ProgrammingInterface }