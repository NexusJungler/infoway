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
        this._addMediaWindow = new AddMediaWindow() ;

        this._$location = $('.main_programming') ;
        this._$programmingForm = this._$location.find('#new_programming_mould_form') ;
        this._$broadCastsContainer = this._$location.find('.displays_forms_container')
        this._$addMediaButton = this._$location.find('#button__add_media') ;

        this._programming = null;
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
            let broadcastObject = new Broadcast()

            let $broadcastStartAtInput = $( $broadcastHTML.find( '.programming__broadcast__start_at__choices' ).get( 0 ) )
            broadcastObject.startAt = moment( $broadcastStartAtInput.val() );

            let $broadcastEndAtInput = $( $broadcastHTML.find( '.programming__broadcast__end_at__choices' ).get( 0 ) )
            broadcastObject.endAt = moment( $broadcastEndAtInput.val() ) ;

            let $broadcastSlots = $broadcastHTML.find('.broadcast_slot')
            $broadcastSlots.each( ( index, broadcastSlot ) => {
                let broadcastSlotObject = this.genereBroadcastSlotFromHTML( $( broadcastSlot ) )
                broadcastObject.addBroadcastSlot( broadcastSlotObject )
            });

            return broadcastObject ;

        }catch( e ){ console.log( e ) }
    }

    genereScreenPlaylistFromHTML( $screenPlaylistHTML, index ){
        try{
            let playlistObject = new ScreenPlaylist() ;
            // broadCastSlotObject.
            playlistObject.screenPosition = index

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
        try{
            let timeSlotObject = new TimeSlot()

            let $broadCastSlotNameInput = $( $timeSlotHTML.find('h3 input.time_slot__name').get( 0 ) )
            timeSlotObject.name = $broadCastSlotNameInput.val()

            let $broadCastSlotStartAt = $( $timeSlotHTML.find('span input.time_slot__start_at').get( 0 ) )
            timeSlotObject.startAt = $broadCastSlotStartAt.val()

            let $broadCastSlotEndAt = $( $timeSlotHTML.find('span input.time_slot__end_at').get( 0 ) )
            timeSlotObject.endAt = $broadCastSlotEndAt.val()

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

    addProgramming( programmingObject ){
        console.log( programmingObject )
        debugger;
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