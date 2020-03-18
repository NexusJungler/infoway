
import {Observer} from "../../Design_Patterns/Observer/Observer";

class Clock extends Observer
{

    constructor(location, timezone) {
        super();

        this.__$location = $(location);
        this.__timezone = timezone;

        this.__identificator = this.getLastClockIdentificator();

        this.__date = null;
        this.__hours = null;
        this.__minutes = null;
        this.__seconds = null;

        this.__day = null;
        this.__monthDay = null;
        this.__month = null;
        this.__year = null;

        this.__dateFormat = timezone.dateFormat;
        this.__clockFormat = timezone.clockFormat;
        this.__language = timezone.language;

        this.getCurrentDate();

    }

    get identificator() {
        return this.__identificator;
    }

    set identificator(identificator) {

        if(typeof identificator !== 'number')
            throw new Error("Internal error ! Attempt to initialize Clock::identificator but is not number");

        this.__identificator = identificator;

        return this;
    }

    get timezone() {
        return this.__timezone;
    }

    set timezone(timezone) {
        this.__timezone = timezone;

        return this;
    }

    get location() {
        return this.__$location;
    }

    set location(location) {
        this.__$location = location;

        return this;
    }

    // when clock is notified
    onNotification() {
        this.updateHour();
    }

    // count how many clock was displayed
    getLastClockIdentificator() {
        let i = 0;

        this.__$location.find(".clock").each( () => {

            i++;

        } );

        return `clock-${i}`;
    }

    getCurrentDate() {
        // (1) first : create first Date() object,
        // get current date of timezone (language : en-US) and convert to string
        // will be used by (2)
        //
        // (2) second : create another Date() object based on (1) result
        const currentDate = new Date( new Date().toLocaleString("en-US", {timeZone: this.__timezone.timezone}) );

        this.__hours = currentDate.getHours();
        this.__minutes = currentDate.getMinutes();
        this.__seconds = currentDate.getSeconds();

        /*this.__day = this.translateDay(currentDate.getDay(), this.__language);
        this.__monthDay = currentDate.getUTCDate();
        this.__month = this.translateMonth(currentDate.getMonth(), this.__language);
        this.__year = currentDate.getFullYear();*/

        if(this.__clockFormat === 24)
        {
            this.__hours = (currentDate.getHours() < 10) ? '0' + currentDate.getHours() : currentDate.getHours();
            this.__minutes = (currentDate.getMinutes() < 10) ? '0' + currentDate.getMinutes() : currentDate.getMinutes();
            this.__seconds = (currentDate.getSeconds() < 10) ? '0' + currentDate.getSeconds() : currentDate.getSeconds();
        }

        this.__date = this.formatDate(currentDate, this.__dateFormat);
    }

    formatDate(date, format = 'd-m-Y') {

        if(format === "m-d-Y")
            return ( (date.getMonth() + 1 < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1) ) + '/' + date.getUTCDate() + "/" + date.getFullYear();


        else if(format === "Y-m-d")
            return date.getFullYear() + '/' + ( (date.getMonth() + 1 < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1) ) + '/' + date.getUTCDate();


        else
            return date.getUTCDate() + "/" + ( (date.getMonth() + 1 < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1) ) + "/" + date.getFullYear();


    }

    translateDay(dayNumber, language = 'fr') {

        const days = {
            'fr': [
                "Dimanche",
                "Lundi",
                "Mardi",
                "Mercredi",
                "Jeudi",
                "Vendredi",
                "Samedi"
            ]
        };

        return days[language][dayNumber];

    }

    translateMonth(monthNumber, language = 'fr') {

        const monthNames = {
            'fr': [
                "Janvier",
                "Fevrier",
                "Mars",
                "Avril",
                "Mai",
                "Juin",
                "Juillet",
                "Août",
                "Septembre",
                "Octobre",
                "Novembre",
                "Decembre"
            ]
        };

        return monthNames[language][monthNumber];

    }

    createClock() {

        const container = $('<div/>', {
            id: this.__identificator,
            class: `clock ${ (this.__identificator === 'clock-0') ? 'default-clock' : 'clock-' + this.__timezone.city }`
        }).data('city', this.__timezone.city)
          .data('timezone', this.__timezone.timezone);

        // if not the default clock
        // add header and close button
        if(this.__identificator !== 'clock-0')
        {
            const header = $('<div/>', {
               class: 'clock-header'
            });

            $('<span/>', {
                class: 'remove-clock',
                text: 'X',
                title: 'Supprimer'
            }).data('target', this.__identificator)
              .appendTo(header);

            header.appendTo(container)
        }

        const timeContainer = $('<div/>', {
            class: `time-container container`
        });

        const time = $('<p/>', {
            class: 'time',
        }).appendTo(timeContainer);

        $('<span/>', {
            class: 'hour',
            text: `${this.__hours} : ${this.__minutes}`
        }).appendTo(time);

        $('<span/>', {
            class: 'location',
            text: `(${this.__timezone.city})`
        }).appendTo(time);

        const dateContainer = $('<div/>', {
            class: `date-container container`
        });

        $('<p/>', {
            class: 'date',
            text: this.__date
        }).appendTo(dateContainer);

        timeContainer.appendTo(container);
        dateContainer.appendTo(container);

        $(container).appendTo(this.__$location);

        return this;
    }

    updateHour()
    {
        this.getCurrentDate();

        if(this.__hours === 0 && this.__minutes === 0 && this.__seconds === 0)
            this.updateDate();

        this.__$location.find(`#${this.__identificator} .hour`).text(`${this.__hours} : ${this.__minutes}`);

        //setTimeout(this.updateHour.bind(this), 1000);

    }

    updateDate()
    {
        this.getCurrentDate();

        this.__$location.find(`#${this.__identificator} .date`).text(this.__date);
    }

    enable()
    {
        //this.updateHour();
    }

    disable()
    {

    }

}

export {Clock}