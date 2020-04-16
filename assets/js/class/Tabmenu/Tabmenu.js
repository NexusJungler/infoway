class Tabmenu  {

constructor() {
  this.down_id = null;
}

  tabmenu() {	

    $('ul.tabs li').click(function(){
      var tab_id = $(this).attr('data-tab');
      var tab_text_id = $(this).attr('data-tab-text');

      $('ul.tabs li').removeClass('current');
      $('.tab-content').removeClass('current');

      $(this).addClass('current');
      $("#"+tab_id).addClass('current');
      $("#"+tab_text_id).addClass('current');  
    })   

    
    $('.btn-display-teach').click(function(){
      var tab_list_enseigne= $(this).attr('data-list-enseigne');

      $('.tbody-content tr').removeClass('current');

      $(this).addClass('current');

      $(".tbody-content ."+tab_list_enseigne).addClass('current');
    })


    /**  media test **/

    /*$('.main-media .product-btn-add').click(function() {

      $('.main-media .upload-file').hide();
      $('.main-media .product-btn-add').hide();
      $('.main-media .upload-title').hide();
      $('.main-media .download-file').show();
      

    });*/

    /** **/
    
  
    $(".menu-list-table .accordion-toggle").click(function() {
      $(this).next().toggleClass("open").slideToggle("fast");
      $(this).toggleClass("active-tab .menu-link").toggleClass("active");
  
      $(".menu-list-table .accordion-content").not($(this).next()).slideUp("fast").removeClass("open");
      $(".menu-list-table .accordion-toggle").not(jQuery(this)).removeClass("active-tab .menu-link").removeClass("active");
    });



    $(".dropdown .btn-dropdown").on('click', function() {
      var down_id = $(this).attr('data-down');

      $("#"+down_id ).slideToggle('fast');

    });

    $('.mutliSelect').on('change', 'input[type="checkbox"]', e => {    
      let clickedInputContainer = $(e.delegateTarget);
      let countCheckbox = clickedInputContainer.find('input[type="checkbox"]:checked').length;

    if( countCheckbox  ){

      clickedInputContainer.find('.multiSel').text(countCheckbox + " Selected");
      // clickedInputContainer.find(".hida").hide();

    }else{
        clickedInputContainer.find('.multiSel').remove();
        // clickedInputContainer.find(".hida").show();
        if( countCheckbox == 0){  
        }
        
    }


      // var title = $(this).closest('.mutliSelect').find('input[type="checkbox"]').val(),
      // title = $(this).val() + ",";
      
      // var tCount =  $("#"+input_id).find('input[type="checkbox"]:checked').length();

      // $(".multiSel").text( tCount + ' selected');

      // if ($(this).is(':checked')){
      //   $(".multiSel").text( tCount + ' selected');

      //   var html = '<span title="' + title + '">' + title + '</span>';
      //   $("#"+input_id).find("p.multiSel").append(html);
      //   $("#"+input_id).find(".hida").hide();

      // } else {

      //   $("#"+input_id).find('span[title="' + title + '"]').remove();
      //     if( ! $("#"+input_id).find(' input[type=checkbox]:checked').length ){
      //       $(".hida").show();
      //     }

      // }
            
    });

    $(".wrapper-content").on('scroll', function() {
        let $val = $(this).scrollLeft();

        if($(this).scrollLeft() + $(this).innerWidth()>=$(this)[0].scrollWidth){
            $(".nav-next").hide();
          } else {
          $(".nav-next").show();
        }

        if($val == 0){
          $(".nav-prev").hide();
        } else {
          $(".nav-prev").show();
        }
      });
    console.log( 'init-scroll: ' + $(".nav-next").scrollLeft() );
    $(".nav-next").on("click", function(){
      $(".wrapper-content").animate( { scrollLeft: '+=460' }, 200);
      
    });
    $(".nav-prev").on("click", function(){
      $(".wrapper-content").animate( { scrollLeft: '-=460' }, 200);
    });

	

  }

}

export {Tabmenu}