
class Navbar {

    navbarleft() {	


        $('#text').click(function () {
            $('#menu-closed').addClass('menu-test');
          });

        $('.menu-wrap .toggler').change( e =>{
            
            let $checkBox = $(e.currentTarget) ;

            if( $checkBox. is( ':checked' ) ){

                $('.header_logo').addClass('logo__infoway-active');

                 $('.menu .nav').addClass('nav-active');

                 $('.main ').addClass('active-main');

                $('.menu .nav .nav-sous-menu').show();
                $('.menu .nav .nav-menu > li .nav-sous-menu-mobile').hide();
                
            } else{
                $('.header_logo').removeClass('logo__infoway-active');

                 $('.menu .nav').removeClass('nav-active');
                 $('.main ').removeClass('active-main');

                $('.menu .nav .nav-sous-menu').hide();
                $('.menu .nav .nav-menu > li .nav-sous-menu-mobile').show();

            }
        })            
    }
    
}
  
export {Navbar}