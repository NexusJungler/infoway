
import {Clock} from "../../objects/Clock/Clock";
import {Observable} from "../../Design_Patterns/Observer/Observable";

class ClockManager extends Observable {

    constructor() {

        // call parent constructor
        super();

        this.__title = 'ClockManager';

        this.__$location = $(".clock__all");
        this.__$clockContainer = $(".clock__all .clock-container");
        this.__$dateContainer = $(".clock__all .date-container");
        this.__defaultTimeZone = {
            city : $(".clock__all .clock-container").data("city"),
            timezone : $(".clock__all .clock-container").data("timezone"),
            dateFormat: $(".clock__all .clock-container").data("date_format"),
            clockFormat : parseInt($(".clock__all .clock-container").data("clock_format")),
            language: 'fr'
        };

        this.__timeZoneList = $(".clock__all #timeZoneList-container ul#timeZoneList");

    }



    onClickOnButtonShowTimeZoneList(active) {

        if(active) {
            this.__$location.find("#showTimeZoneList").on("click.onClickOnButtonShowTimeZoneList", e => {

                if($(e.currentTarget).hasClass("arrow-down")) {
                    $(e.currentTarget).removeClass("arrow-down");
                    $(e.currentTarget).addClass("arrow-up");
                    this.__$location.find("#timeZoneList-container").slideDown();
                }

                else {
                    $(e.currentTarget).removeClass("arrow-up");
                    $(e.currentTarget).addClass("arrow-down");
                    this.__$location.find("#timeZoneList-container").slideUp();
                }

            })
        }

        else {
            this.__$location.find("#showTimeZoneList").off("click.onClickOnButtonShowTimeZoneList");
        }

    }

    createNewClock($location, timeZone, isDefaultClock = false) {

        // if is default clock
        // use default timezone
        // else use custom timezone
        const clock = (isDefaultClock) ? new Clock(this.__$clockContainer, this.__defaultTimeZone) : new Clock($location, timeZone);

        clock.createClock()
             .enable();

        // subscribe clock (@see : http://design-patterns.fr/observateur)
        this.subscribe(clock);

    }

    removeAllClock() {
        // remove all clock except default clock
        this.__$clockContainer.find(".clock").not(".default-clock").each( clock => {
            $(clock).remove();
        } );

        this.unsubscribeAllObserver();

    }

    onClickOnCityNameAddNewClock(active) {

        // if manager is enabled
        // add event
        if(active) {
            this.__timeZoneList.on("click.onClickOnCityNameAddNewClock", "li.timezone", e => {

                let $selectedTimeZone = $(e.currentTarget);

                if(this.__$clockContainer.find(`div.clock[data-city='${$selectedTimeZone.text()}']`).length === 0)
                {

                    let selectedTimeZoneInfo = {
                        city: $selectedTimeZone.text(),
                        timezone : $selectedTimeZone.data("timezone"),
                        dateFormat: $selectedTimeZone.data("date_format"),
                        clockFormat : $selectedTimeZone.data("clock_format"),
                        language: $selectedTimeZone.data("language")
                    };

                    this.createNewClock(this.__$clockContainer, selectedTimeZoneInfo);

                }

                $("#showTimeZoneList").removeClass("arrow-up");
                $("#showTimeZoneList").addClass("arrow-down");
                $("#timeZoneList-container").slideUp();

            });
        }

        // else remove event
        else {
            this.__timeZoneList.off("click.onClickOnCityNameAddNewClock", "li.timezone");
        }

    }

    onclickOnRemoveButtonRemoveClock(active) {

        // if manager is enabled
        // add event
        if(active) {
            this.__$clockContainer.on("click.onclickOnRemoveButtonRemoveClock", ".clock .clock-header span.remove-clock", e => {

                const clockToRemove = $(e.currentTarget).data('target');

                this.__$clockContainer.find(`#${clockToRemove}`).remove();

            })
        }

        // else remove event
        else {
            this.__$clockContainer.off("click.onclickOnRemoveButtonRemoveClock", ".clock .clock-header span.remove-clock");
        }

    }

    onMinutesChangeNotifyClocks() {

        const currentDate = new Date( new Date().toLocaleString("en-US", {timeZone: this.__defaultTimeZone.timezone}) );

        const seconds = currentDate.getSeconds();

        // all clocks was notified on each minutes for updating
        if(seconds === 0)
            this.notifyAllRegisteredObserver();

        // call this function (onMinutesChangeNotifyClocks) each seconds (1000ms = 1s)
        // bind this for keep value
        setTimeout(this.onMinutesChangeNotifyClocks.bind(this), 1000);
    }
    

    enable() {

        this.createNewClock(this.__$clockContainer, this.__defaultTimeZone, true);
        this.onMinutesChangeNotifyClocks();
        
        this.onClickOnButtonShowTimeZoneList(true);
        this.onClickOnCityNameAddNewClock(true);
        this.onclickOnRemoveButtonRemoveClock(true);
    }


    disable() {

        this.removeAllClock();

        this.onClickOnButtonShowTimeZoneList(false);
        this.onClickOnCityNameAddNewClock(false);
        this.onclickOnRemoveButtonRemoveClock(false);
    }


}

export {ClockManager}