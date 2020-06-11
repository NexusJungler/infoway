import "../css/settings/criterion/create_criterion.scss";
import "../css/settings/criterion/list_criterion.scss";
import "../css/settings/criterion/edit_criterion.scss";

let createcriterion = new createCriterion();

import createCriterion from "./settings/criterion/createCriterion.js";

$(function(){

    let addTagBtn = $('#add_criterion')
    let nameTags = [];


    // checked input modification
    $(".content-criterion .modified-criterion").click(function(){

        $.each($(".row-criterion input[type='checkbox']:checked"), function(){
            var id_criterion = $(this).attr('data-criterion');

            window.location.href= "/criterions/"+ id_criterion +"/edit"
            console.log(id_criterion);
        })
    })

//delete
    $(".content-criteres-bloc").on("click", ".delete-row", function(){
        var button_id = $(this).attr("id");

        $('#criterions_list_criterions_'+button_id+'_selected').parents('li').remove();
    });




    $(".btn-popup-edit-criterion").addClass("btn_hidden");

// btn modification
    $(".row-criterion input[type='checkbox']").each( function () {
        if($(this).prop("checked")){
            console.log($(this).prop("checked"));
            $(".btn-popup-edit-criterion").removeClass("btn_hidden");
        }
        console.log($(this).prop("checked"));
    })


    $(".row-criterion .chkbox-critere").change( function(){

        var nb_input = $(".row-criterion input[type='checkbox']:checked").length;

        if( nb_input === 1 ){

            $(".btn-popup-edit-criterion").prop('disabled', false)
            $(".btn-popup-edit-criterion").removeClass("btn_hidden");
        }else{
            $(".btn-popup-edit-criterion").prop('disabled', true)
            $(".btn-popup-edit-criterion").addClass("btn_hidden");
        }
    })




    addTagBtn.on('click', e =>{

        let list = $('.criterion_list')

        let counter = list.children().length;

        let newWidget = list.attr('data-prototype');
        newWidget = newWidget.replace(/__name__/g, counter);
        newWidget = "<div>" + newWidget + "<div><button type='button' id='"+ counter +"' class='btn delete-row' >x</button></div></div>"

        counter++;
        list.data('widget-counter', counter);

        let newElem = $(list.attr('data-widget-tags')).html(newWidget);

        newElem.appendTo(list);
        $(`#criterions_list_criterions_${counter-1} label[for='criterions_list_criterions_${counter-1}_selected']`).html("Choix n°" + counter );
    })

    $("#add_criterion").on("click", function() {
        console.log("1");
        $(".content-criteres-bloc .criterion_list ").animate({

            scrollTop: $('.content-criteres-bloc .criterion_list ').get(0).scrollHeight
        }, 50);
    });


// $('.checkbox-criterion').change( e => {
//     uniqueChoice();
// })

// $('#criterions_list_multiple').change( e => {
//     uniqueChoice();
// })

// function uniqueChoice(){
//     let selectChoix = $('#criterions_list_multiple').children("option:selected").val();

//     if(selectChoix == 0){
//         console.log("unique");
//         let nb_input = $(".checkbox-criterion:checked").length;
//         if( nb_input === 1 ){
//             $(".btn-create-critions").prop('disabled', false);
//         }else {
//             $(".btn-create-critions").prop('disabled', true);
//             $(".btn-create-critions").addClass("hide-btn");
//         }

//     }else{
//         console.log("multiple");
//     }
// }
// $("#criterions_list_basicCriterionUsed_0").prop("required", false);

// $("#criterions_list_basicCriterionUsed input[type=radio]").on('click', function() {

//     if(this.previous){
//         this.checked = false
//         $(".row-define-criterion").removeClass("active");
//         $("#criterions_list_basicCriterionUsed_1").val($(this).is(':checked'));
//     }

//     this.previous = this.checked;

//     // $("#criterions_list_basicCriterionUsed input[type=radio]").prop("checked", false);
// });



// popup modification
    $('.btn-popup-edit-criterion').click(function () {
        $('.add-popup-edit').addClass('is-open');

        $.each($(".criterion-poster input[type='checkbox']:checked"), (index, input) => {
            let selectedTagName = $(input).parents("td").find(".criterion-name").text();
            if(nameTags.indexOf(selectedTagName) === -1){
                nameTags.push(selectedTagName);
            }
        });

        nameTags.forEach(nameTag =>{
            if($(`.content-modal .selected-tags-list p[text="${nameTag}"]`).length === 0 ){
                $("<p>" + nameTag + "</p>").appendTo($(".content-modal .selected-tags-list"));
            }
        });
        return false;
    });

// popup modification
    $('.btn-popup-delete-criterion').click(function () {
        $('.add-popup-edit').addClass('is-open');

        $.each($(".criterion-poster input[type='checkbox']:checked"), (index, input) => {
            let selectedTagName = $(input).parents("td").find(".criterion-name").text();
            if(nameTags.indexOf(selectedTagName) === -1){
                nameTags.push(selectedTagName);
            }
        });

        nameTags.forEach(nameTag =>{
            if($(`.content-modal .selected-tags-list p[text="${nameTag}"]`).length === 0 ){
                $("<p>" + nameTag + "</p>").appendTo($(".content-modal .selected-tags-list"));
            }
        });
        return false;
    });

    $('.btn-popupclose').click( () => {
        $('.add-popup-edit').removeClass('is-open');
        $(".content-modal .selected-tags-list ").empty();
        nameTags=[];
    });


    createcriterion.enable();

});

