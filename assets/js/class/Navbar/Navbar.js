
class Navbar {

    navbarleft() {	


        $('.menu-wrap input[type="checkbox"]').change( e =>{
            
            let $checkBox = $(e.currentTarget) ;

            if( $checkBox. is( ':checked' ) ){

                $('.logo__infoway').addClass('logo__infoway-active');

                 $('.menu .nav').addClass('nav-active');

                 $('.home ').addClass('active-main');
                // $('.menu .nav').animate({
                //     width: '12%',
                // },'slow', 'linear', function() {
                // //        $('.menu .nav').addClass('nav-active');
                // });

                $('.menu .nav .nav-sous-menu').show();
                $('.menu .nav .nav-menu > li .nav-sous-menu-mobile').hide();
                
            } else{
                $('.logo__infoway').removeClass('logo__infoway-active');

                 $('.menu .nav').removeClass('nav-active');
                 $('.home ').removeClass('active-main');
            //     $('.menu .nav').animate({
            //         width: '8%',
            //     },'slow', 'linear', function() {
            //           $('.menu .nav').removeClass('nav-active');
            //    });

                $('.menu .nav .nav-sous-menu').hide();
                $('.menu .nav .nav-menu > li .nav-sous-menu-mobile').show();

            }
        })



            
    }
    
}
  
export {Navbar}