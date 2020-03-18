

class ClockTool
{

    constructor() {

        this.__$container = $(".clock__all .clock-container");
        this.__$containerParent = this.__$container.parent();

        this.__location = {
            city: this.__$container.data('city'),
            timezone: this.__$container.data('timezone'),
            date_format: this.__$container.data('date_format'),
            clock_format: this.__$container.data('clock_format')
        };

        this.__date = null;
        this.__hours = null;
        this.__minutes = null;

    }

    getCurrentDate() {

        // (1) first : create first Date() object,
        // get current date of timezone (language : en-US) and convert to string
        // will be used by (2)
        //
        // (2) second : create another Date() object based on (1) result
        const currentDate = new Date( new Date().toLocaleString("en-US", {timeZone: this.__location.timezone}) );

        this.__hours = currentDate.getHours();
        this.__minutes = currentDate.getMinutes();
        this.__seconds = currentDate.getSeconds();

        if(this.__location.clock_format === 24)
        {
            this.__hours = (currentDate.getHours() < 10) ? '0' + currentDate.getHours() : currentDate.getHours();
            this.__minutes = (currentDate.getMinutes() < 10) ? '0' + currentDate.getMinutes() : currentDate.getMinutes();
            this.__seconds = (currentDate.getSeconds() < 10) ? '0' + currentDate.getSeconds() : currentDate.getSeconds();
        }

        if(this.__hours === "00" && this.__minutes === "00" && this.__seconds === "00")
            this.updateDate();

        if(this.__seconds === "00")
            this.updateHour();

        this.__date = this.formatDate(currentDate, this.__location.date_format);

        // call this function (onMinutesChangeNotifyClocks) each seconds (1000ms = 1s)
        // bind this for keep value
        setTimeout(this.getCurrentDate.bind(this), 1000);

    }

    formatDate(date, format = 'd-m-Y') {

        if(typeof date !== 'object' || !(date instanceof Date))
            throw  new Error("Invalid 'date' parameter for ClockTool::formatDate()");

        else if(typeof format !== 'string')
            throw  new Error("Invalid 'format' parameter for ClockTool::formatDate()");

        else {

            if(format === "m-d-Y")
                return ( (date.getMonth() + 1 < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1) ) + '/' + ( (date.getUTCDate() < 10) ? '0' + date.getUTCDate() : date.getUTCDate() ) + "/" + date.getFullYear();


            else if(format === "Y-m-d")
                return date.getFullYear() + '/' + ( (date.getMonth() + 1 < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1) ) + '/' + ( (date.getUTCDate() < 10) ? '0' + date.getUTCDate() : date.getUTCDate() );


            else
                return ( (date.getUTCDate() < 10) ? '0' + date.getUTCDate() : date.getUTCDate() ) + "/" + ( (date.getMonth() + 1 < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1) ) + "/" + date.getFullYear();

        }

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
            class: `clock`
        }).data('city', this.__location.city)
          .data('timezone', this.__location.timezone);

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
            text: `(${this.__location.city})`
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

        $(container).appendTo(this.__$container);

        return this;
    }

    removeClock() {
        this.__$container.empty();
    }

    updateHour() {

        this.__$container.find(`.clock .hour`).text(`${this.__hours} : ${this.__minutes}`);

    }

    updateDate() {

        this.__$container.find(`.clock .date`).text(this.__date);

    }

    onClickOnArrowButtonShowCityList(active) {

        if(active) {

            this.__$containerParent.find("i#showTimeZoneList").on("click.onClickOnArrowButtonShowCityList", e => {

                if($(e.currentTarget).hasClass("arrow-down")) {
                    $(e.currentTarget).removeClass("arrow-down");
                    $(e.currentTarget).addClass("arrow-up");
                    this.__$containerParent.find("div#timeZoneList-container").slideDown();
                }

                else {
                    $(e.currentTarget).removeClass("arrow-up");
                    $(e.currentTarget).addClass("arrow-down");
                    this.__$containerParent.find("div#timeZoneList-container").slideUp();
                }

            });

        }

        else {
            this.__$containerParent.find("i#showTimeZoneList").off("click.onClickOnArrowButtonShowCityList");
        }

    }

    onClickOnCityNameLoadCityClock(active) {

        if(active) {

            this.__$containerParent.find("#timeZoneList-container #timeZoneList li").on("click.onClickOnCityNameLoadCityClock", e => {

               this.__location = {
                   city: $(e.currentTarget).data('city'),
                   timezone: $(e.currentTarget).data('timezone'),
                   date_format: $(e.currentTarget).data('date_format'),
                   clock_format: $(e.currentTarget).data('clock_format')
               };

                this.removeClock();
                this.getCurrentDate();
                this.createClock();

            })

        }

        else {
            this.__$containerParent.find("#timeZoneList-container #timeZoneList li").off("click.onClickOnCityNameLoadCityClock");
        }

    }

    enable() {


        this.getCurrentDate();
        this.createClock();

        this.onClickOnArrowButtonShowCityList(true);
        this.onClickOnCityNameLoadCityClock(true);

    }

    disable() {

        this.onClickOnArrowButtonShowCityList(false);
        this.onClickOnCityNameLoadCityClock(false);

    }

}

export default ClockTool