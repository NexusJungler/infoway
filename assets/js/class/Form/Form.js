class Form  {

    FormValidate() {	
        
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

        $('.form-content-table input[type="checkbox"]').change( e =>{

            let $checkEnseigne = $(e.currentTarget) ;

            if( $checkEnseigne.is( ':checked' ) ){
                console.log($checkEnseigne.parent());
                console.log($(".form-content-table .table").find(`tr.${ $checkEnseigne.attr('class')} `)); 
                $(".form-content-table .table").find(`tr.${ $checkEnseigne.attr('class')} .select`).addClass('select-active');
            }
            else{
                $(".form-content-table .table").find(`tr.${ $checkEnseigne.attr('class')} .select`).removeClass('select-active');
            }
        });

        $('#create-user').validate({
           
            rules:{
                Perimeters:{ required: true },
                selectenseigne:{ required: true }
            },
            messages:{
                Perimeters:{
                  required:"Please select a Color<br/>"
                },
                selectenseigne:{
                    required:"Please select a Color<br/>"
                }
            },
            errorPlacement: function(error, element) {
                if ( element.is(":radio") ) {
                      error.appendTo( element('.message-error') );
                }else { 
                    // This is the default behavior 
                    error.insertAfter( element );
                }
            }
        });

    }

  
}
    
export {Form}