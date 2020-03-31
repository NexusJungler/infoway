class Form  {

    constructor (){
        
    }
    
    ValidateEmail() {	
        
        $("#email").blur(function(){

            $(".form-error").hide();
            var hasError = false;
            let emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/; 
            let emailaddress = $("#email").val();

            if(emailaddress == '') {
                $("#email").after('<span class="form-error">Veuillez saisir votre adresse e-mail.</span>');
                $(".col-email").addClass("error-active");
                hasError = true;
            }
            else if(!emailReg.test(emailaddress)) {
                $("#email").after('<span class="form-error">Entrez une adresse e-mail valide.</span>');
                $(".col-email").addClass("error-active");
                hasError = true;
            }
            if(hasError == true) { 
                // $(".row-email").removeClass("error-active");
                return false; 
            }
   
        });
    }


    
}
    
export {Form}