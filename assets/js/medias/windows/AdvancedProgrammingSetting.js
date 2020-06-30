import {Media} from "../../mediaLibrary/object/Media";
import {TimeSlot} from "../../programming/objects/TimeSlot";
import {Programming} from "../../programming/objects/Programming";
import {Broadcast} from "../../programming/objects/Broadcast";
import { move } from "../../utilities";
import {BroadcastSlot} from "../../programming/objects/BroadcastSlot";
import {ScreenPlaylist} from "../../programming/objects/ScreenPlaylist";
import {PlaylistEntry} from "../../programming/objects/PlaylistEntry";

class AdvancedProgrammingSetting{
    constructor() {
        this._$location = $('#add_media__body__programming__infos')

        this._selectedTimeSlots = [] ;
        this._selectedDays = [] ;
        this._selectedScreens = [] ;
        this._selectedStartDate = null;
        this._selectedEndDate = null ;
        this._selectedMedias = [] ;

        this._$comfirm = this._$location.find('button.comfirm')
        this._$timeSlotsInputs = this._$location.find('.advanced_programming__time_slot__choices')
        this._$daysInputs = this._$location.find('.advanced_programming__days__choices')
        this._$screensInputs = this._$location.find('.advanced_programming__screens__choices')
        this._$mediasInputs = this._$location.find('.advanced_programming__medias__choices')

        this._$startDateInput = $( this._$location.find('.advanced_programming__start_date__choice').get( 0 ) )
        this._$endDateInput = $( this._$location.find('.advanced_programming__end_date__choice').get( 0 ) )

        this._generatedProgramming = null ;

    }


    get generatedProgramming() {
        return this._generatedProgramming;
    }

    set generatedProgramming( generatedProgramming ) {
        this._generatedProgramming = generatedProgramming ;
    }

    get $mediasInputs() {
        return this._$mediasInputs;
    }

    set $mediasInputs( $mediaInputs ) {
        if( ! $mediaInputs instanceof jQuery ) throw new Error('invalid argument for medias Inputs')
        this._$mediasInputs = $mediaInputs;
    }

    active( active, programmingInterface ){
        active ? console.log( 'activated') : console.log('unactivated')
        this.onClickApplyProgramming( active, programmingInterface )
    }

    generateMediaFromHTMl( $mediaHTML ){
        try{
            let mediaObject = new Media();

            let id = $mediaHTML.find('input.medias__choices__id').val()
            let name = $mediaHTML.find('label.medias__choices__name').text()
            let src = $mediaHTML.find('img').prop('src')

            mediaObject.name = name ;
            mediaObject.id = id ;
            mediaObject.src = src ;

            return mediaObject ;

        }catch( e ){ console.log( e ) }
    }
    refreshSelectedMedias(){
        try{
            this._selectedMedias = [] ;
            this._$mediasInputs.toArray()
                .filter( mediaInput => mediaInput.checked )
                .forEach( mediaInput => {
                    let $media = $( mediaInput ) .parents('.medias__choices')
                    let mediaObject = this.generateMediaFromHTMl( $media )
                    console.log( mediaObject )
                    this.addSelectedMedia( mediaObject )
                } )

        }catch( e ){ console.log( e ) }
    }

    addSelectedMedia( media ){
        if( ! media instanceof Media ) throw new Error('invalid argument type ')
        if( ! this._selectedMedias.includes( media ) )this._selectedMedias.push( media )
    }
    refreshSelectedScreens(){
        this._selectedScreens = []

        this._$screensInputs
            .filter( ( index, screenInput ) => screenInput.checked )
            .each( ( index , selectedScreenInput ) => {
                try { this.addSelectedScreen(  parseInt( selectedScreenInput.value ) ) }
                catch( e ){ console.log( e ) }
            } )
    }

    genereTimeSlotFromHTML( $timeSlotHTML ){
        try{
            let timeSlotObject = new TimeSlot() ;

            let $timeSlotInfos = $timeSlotHTML.parents('.add_media__programming__timeslot')
            let id = $timeSlotInfos.data('id');

            if( typeof id === 'number' )timeSlotObject.id = id
            timeSlotObject.startAt = moment( $timeSlotInfos.data('startat') , 'HHmmss' );
            timeSlotObject.endAt = moment( $timeSlotInfos.data('endat') , 'HHmmss' );

            return timeSlotObject

        }catch( e ){ console.log( e ) }
    }
    refreshSelectedTimeSlots(){
        try{
            this._$timeSlotsInputs
                .filter( ( index, timeSlotInput ) => timeSlotInput.checked )
                .each( ( index, timeSlotInputSelected ) => { this.addSelectedTimeSlot( this.genereTimeSlotFromHTML( $( timeSlotInputSelected ) ) ) } )
        }catch( e ){ console.log( e ) }
    }
    refreshSelectedDays(){
        try{
            this._selectedDays = [] ;

            this.$checkedDays = this._$daysInputs
                .filter( ( index, dayInput ) => dayInput.checked )

            this.$checkedDays.each( ( index, dayInput ) => this.addSelectedDay( parseInt( dayInput.value ) ) )

        }catch( e ){ console.log( e ) }
    }

    refreshSelectedStartDate(){
        this.selectedStartDate = moment( this._$startDateInput.val(), 'YYYY-MM-DD')
    }
    refreshSelectedEndDate(){
        this.selectedEndDate = moment( this._$endDateInput.val(), 'YYYY-MM-DD')
    }

    getBroadcastsAffectedByNewProgOrderedByStartAt( broadcasts ){
        try{
            return broadcasts
                .filter( broadcast => broadcast.endAt >= this.selectedStartDate && broadcast.endAt <= this.selectedEndDate )
                .sort( ( a , b ) => a.startAt - b.startAt  )

        }catch( e ){ console.log( e ) }
    }


    genereProgramming( programmingInterface ){

        let programmingObject = programmingInterface.programming

        let affectedBroadcasts = this.getBroadcastsAffectedByNewProgOrderedByStartAt( programmingObject.broadcasts )

        programmingInterface.addNewDisplayHTMLElement()

    }

    hydrateBroadcastWithSelectedInfos( broadcast ){

        this._selectedTimeSlots.forEach( timeSlot => {
            let currentBroadcastSlot = new BroadcastSlot()
            currentBroadcastSlot.timeSlot = timeSlot

            this._selectedScreens.forEach( selectedScreen => {
                let currentPlaylist = new ScreenPlaylist()
                currentPlaylist.screenPosition = selectedScreen

                this._selectedMedias.forEach( ( selectedMedia, index )  => {
                    let playlistEntry = new PlaylistEntry()
                    playlistEntry.media = selectedMedia
                    playlistEntry.positionInPlaylist = index + 1

                    currentPlaylist.addEntry( playlistEntry )
                })

                currentBroadcastSlot.addPlaylist( currentPlaylist )

            })

            broadcast.addBroadcastSlot( currentBroadcastSlot )

        })

        return broadcast
    }
    genereProgrammingFromSettings( ){
        let weekdaysBroadcasting = []

        for( let i = 1 ; i <= 7 ; i++ ){ weekdaysBroadcasting.push( this._selectedDays.includes( i ) ) }

        let sevenDaysAWeekBroadcasting = ! weekdaysBroadcasting.includes( false )

        let daysRemaining = this.selectedEndDate.diff( this.selectedStartDate, 'days')

        let currentDate = moment( this.selectedStartDate );

        let currentIndex = this.selectedStartDate.day() - 1
        let searchBroadcasting = true ;
        let programmingGenerated = new Programming()
        let currentBroadcast = null ;

        if( ! sevenDaysAWeekBroadcasting ) {

            while( daysRemaining >= 0 || ! searchBroadcasting ){

                weekdaysBroadcasting = move( weekdaysBroadcasting, currentIndex )

                let dayDiff = weekdaysBroadcasting.indexOf( searchBroadcasting )

                if( searchBroadcasting ){

                    if( dayDiff > daysRemaining ) break;

                    currentDate.add( dayDiff, 'days' )
                    currentBroadcast = new Broadcast()

                    currentBroadcast.startAt = moment( currentDate )
                    currentBroadcast = this.hydrateBroadcastWithSelectedInfos( currentBroadcast )

                    programmingGenerated.addBroadcast( currentBroadcast )

                }else {
                    if (daysRemaining >= dayDiff) {
                        currentDate.add( dayDiff, 'days' )
                        if (currentBroadcast !== null && currentBroadcast instanceof Broadcast) currentBroadcast.endAt = moment(currentDate).subtract(1, 'days')
                    } else {
                        if (currentBroadcast !== null && currentBroadcast instanceof Broadcast) currentBroadcast.endAt = moment( this.selectedEndDate )
                    }
                }

                searchBroadcasting = ! searchBroadcasting
                daysRemaining = daysRemaining - dayDiff
                currentIndex = dayDiff
            }
        }else {
            let newBroadcast = new Broadcast()

            newBroadcast.startAt = moment( this.selectedStartDate )
            newBroadcast.endAt = moment( this.selectedEndDate )

            newBroadcast = this.hydrateBroadcastWithSelectedInfos( newBroadcast )

            programmingGenerated.addBroadcast( newBroadcast )
        }
        console.log( programmingGenerated )
        debugger ;
        return programmingGenerated

    }

    onClickApplyProgramming( active, programmingInterface ){
        try{
            if( active ){
                this._$comfirm.on('click.onClickApplyProgramming', e => {
                    this.refreshSelectedScreens()
                    this.refreshSelectedMedias()
                    this.refreshSelectedDays()
                    this.refreshSelectedStartDate()
                    this.refreshSelectedEndDate()
                    this.refreshSelectedTimeSlots()

                    this.generatedProgramming = this.genereProgrammingFromSettings()

                    programmingInterface.addProgramming( this.generatedProgramming )
                    programmingInterface.refreshInterface()

                })
            }else{ this._$comfirm.off('click.onClickApplyProgramming') }
        }catch( e ){ console.log( e ) }
    }


    addSelectedScreen( selectedScreen ){
        if( typeof selectedScreen !== 'number' )throw new Error('invalid argument type for selectedScreen')
        if( ! this._selectedScreens.includes( selectedScreen ) ) this._selectedScreens.push( selectedScreen )
    }

    addSelectedDay( selectedDay ){
        if( typeof selectedDay !== 'number' )throw new Error('invalid argument type for selectedDay')
        if( ! this._selectedDays.includes( selectedDay ) ) this._selectedDays.push( selectedDay )
    }
    addSelectedTimeSlot( selectedTimeSlot ){
        if( ! selectedTimeSlot instanceof TimeSlot )throw new Error('invalid argument type for selectedDay')
        if( ! this._selectedTimeSlots.includes( selectedTimeSlot ) ) this._selectedTimeSlots.push( selectedTimeSlot )
    }

    get selectedStartDate() {
        return this._selectedStartDate;
    }

    set selectedStartDate( selectedStartDate ) {
        this._selectedStartDate =  selectedStartDate ;
    }


    get selectedEndDate() {
        return this._selectedEndDate;
    }

    set selectedEndDate( selectedEndDate ) {
        this._selectedEndDate =  selectedEndDate ;
    }
}

export { AdvancedProgrammingSetting }