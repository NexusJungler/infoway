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