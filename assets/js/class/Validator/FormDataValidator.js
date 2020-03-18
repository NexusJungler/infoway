"use strict";

class FormDataValidator
{

    constructor() {

        this._phoneNumber= null;
        this._countryDataFormat = [];

        this.init();

        this._country = $("#country select option:selected").text();

        this.setInputValuePattern();

        $("#country select").on('change', this.handleCountrySelect.bind(this));

    }

    init() {

        $.each($("#country select option"), (index, option) => {

            let obj = { };

            if($(option).text() === "France")
            {
                obj = {
                    htmlPostalCodeRegex: "^([0-9]{5})$",
                    htmlPhoneNumberRegex: "^(?:(?:\\+)33|0)\\s*[1-9](?:[\\s.-]*\\d{2}){4}$",
                    JSPostalCodeRegex: /^([0-9]{5})$/g,
                    JSPhoneNumberRegex: /^(?:(?:\+)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/g
                };
            }
            else
            {
                obj = {
                    htmlPostalCodeRegex: null,
                    htmlPhoneNumberRegex: null,
                    JSPostalCodeRegex: null,
                    JSPhoneNumberRegex: null
                };
            }

            this._countryDataFormat[$(option).text()] = obj;

        });

    }

    handleCountrySelect() {
        this._country = $("#country select option:selected").text();

        this.setInputValuePattern();
    }


    setInputValuePattern() {

        let pattern = this.getCountryPhoneNumberRegex(this._country, true);
        if(pattern === null)
        {
            $("#phone-number input").removeAttr("pattern");
            $("#phone-number input").removeAttr("title");
        }
        else
        {
            $("#phone-number input").prop("pattern", pattern);
            $("#phone-number input").prop("title", "Merci de saisir un numéro valide en " + this._country);
        }


        pattern = this.getCountryPostalCodeRegex(this._country, true);
        if(pattern === null)
        {
            $("#postal-code input").removeAttr("pattern");
            $("#postal-code input").removeAttr("title");
        }
        else
        {
            $("#postal-code input").prop("pattern", pattern);
            $("#postal-code input").prop("title", "Merci de saisir un code postal valide en " + this._country);
        }

    }


    checkPhoneNumber() {

        this._phoneNumber = $("#phone-number input").val();

        if(this._country === "France")
            return ( this._phoneNumber.match(this.getCountryPhoneNumberRegex(this._country)) !== null );

        return true;

    }

    getCountryPhoneNumberRegex(country, htmlPattern = false) {
        
        if(htmlPattern)
            return this._countryDataFormat[country].htmlPhoneNumberRegex;
        
        else
            return this._countryDataFormat[country].JSPhoneNumberRegex;
        
    }

    getCountryPostalCodeRegex(country, htmlPattern = false) {

        if(htmlPattern)
            return this._countryDataFormat[country].htmlPostalCodeRegex;

        else
            return this._countryDataFormat[country].JSPostalCodeRegex;

    }

}

export {FormDataValidator}