"use strict";


class Clock  {
    constructor(clock = "#clock") {
        this.__clock = $(clock);
        this.__hour = null;
        this.__minutes = null;
        this.__secondes = null;
    }

    init() {

        this.getHour();
        this.getDate();

    }

    getHour() {
        let currentTime = new Date();
        this.__hour = currentTime.getHours();
        this.__minutes = currentTime.getMinutes();
        this.__secondes = currentTime.getSeconds();

        if (this.__hour === 0 && this.__minutes === 0 && this.__secondes === 0) {
            this.getDate();
        }


        this.__hour = (this.__hour < 10 ? "0" : "") + this.__hour;
        this.__minutes = (this.__minutes < 10 ? "0" : "") + this.__minutes;
        this.__secondes = (this.__secondes < 10 ? "0" : "") + this.__secondes;

        this.__clock.html(this.__hour + ":" + this.__minutes/* + ":" + this.__secondes*/);
        setTimeout(this.getHour.bind(this),1000)
    }

    getDate() {
        const days =[
            "Dimanche",
            "Lundi",
            "Mardi",
            "Mercredi",
            "Jeudi",
            "Vendredi",
            "Samedi"
        ];

        const monthNames = [
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
        ];

        let currentTime = new Date();
        let day = days[currentTime.getDay()];
        let monthDay = currentTime.getUTCDate();
        let month = monthNames[currentTime.getMonth()];
        let year = currentTime.getFullYear();

        let date = day + " " + monthDay + " " + month + " " + year;
        console.log(date)
        $("p.date").text(date);
    }

}

export {Clock}