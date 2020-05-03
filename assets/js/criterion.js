
// checked input modification

$(".tab-content-criteres .modified-criterion").click(function(){

    $.each($(".row-criterion input[type='checkbox']:checked"), function(){
        var id_criterion = $(this).attr('data-criterion');
        
        console.log(id_criterion);
    })
})

let i = 2;
$(".form-content .btn-add .btn-add-critiere").click(function(){
       i++;
       var html = "";
       html += "<div class='row-criteres' id='row"+i+"'> <div class='selected'> ";
       html += "<label for='criterion_list_criterions_"+i+"_selected'> choix n°"+i+" </label><input type='checkbox' id='criterion_list_criterions_"+i+"_selected' name='criterion_list[criterions]['+i+'][selected]' class='checkbox-custome' value="+i+"></div> ";
       html += "<div><label for='criterion_list_criterions_"+i+"_name'> </label><input type='text' id='criterion_list_criterions_"+i+"_name' name='criterion_list[criterions]["+i+"][name]' class='input-custome'></div>";
       html += "<div><label for='criterion_list_criterions_"+i+"_description'>Description</label><input type='text' id='criterion_list_criterions_"+i+"_description' name='criterion_list[criterions]["+i+"][description]' class='input-custome input-custome-desc'></div>";
       html += "<div><button type='button' id='"+i+"' class='delete-row btn'>X</button></div></div>";
       
       $('.content-criteres-bloc').append(html);
       
});

$(".content-criteres .delete-row").click(function(){
       var button_id = $(this).attr("id");
       console.log(button_id);
       $('#row'+button_id+'').remove();   
});


$(".tab-content-criteres .modified-criterion").click(function(){

       $.each($(".row-criterion input[type='checkbox']:checked"), function(){
         var id_criterion = $(this).attr('data-criterion');
         
         console.log(id_criterion);
       })
     })
     

// $(".delete-row").click(function(){
//        var count_input = $(".content-criteres-bloc").find('input[type="checkbox"]').length;
//        var checked_count =  $('[type="checkbox"]:checked').length;

//        if(count_input != checked_count){
//               console.log("2");
//               $(".content-criteres-bloc").find('input[type="checkbox"]').each(function(){
//                      if($(this).is(":checked"))
//                 {
//                     $(this).parents(".content-criteres-bloc row-criteres").remove();
//                 }
//               });
//        }else{
//               return false
//        }
// })
