require("../css/back-office_create-customer.css");
import {FormDataValidator} from "./class/Validator/FormDataValidator";

const $ = require('jquery');
global.$ = global.jQuery = $;

const phoneNumberValidator = new FormDataValidator();

$("form").submit( (e) => {

    e.preventDefault();

    if( phoneNumberValidator.checkPhoneNumber() )
        $(e.target).unbind("submit").submit();

} );

