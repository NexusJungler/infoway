// import style css
import "../css/settings/tags/create_tags.scss";
import "../css/settings/tags/list_tags.scss";
import "../css/settings/tags/edit_tags.scss";

// import listTags from "./tags/list_tags";
//
// let listtags = new listTags();

// jquery 
const $ = require('jquery');
global.$ = global.jQuery = $;

require('./tags/edit_tags.js');
require('./tags/list_tags.js');

/** page create tags**/


$(function(){


//Search Filterable table
    $("#site-search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".tbody-serach tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });


    let addTagBtn = $('#add_tag')

    addTagBtn.on('click', e =>{

        let list = $('.tags_list')

        let counter = list.children().length;

        let newWidget = list.attr('data-prototype');

        newWidget = newWidget.replace(/__name__/g, counter);
        counter++;
        list.data('widget-counter', counter);

        let newElem = $(list.attr('data-widget-tags')).html(newWidget);

        let $tagNameInput = $('#tag_list_tagToCreate_name') ;
        newElem.find('input.tags-name').val( $tagNameInput.val() );

        let $tagDescriptionInput  = $('#tag_list_tagToCreate_description')
        newElem.find('input.tags-desc').val( $tagDescriptionInput.val() );


        $('<span class="color_input">').insertBefore(newElem.find('.tags-name'));

        newElem.appendTo(list);

    })

    // $addTagBtn.on("click", e =>{

    //     if ($("#tag_list_tagToCreate_name") === "TEXT") {
    //         $("#tag_list_tagToCreate_name").value('');
    //     }

    // })

    /**  **/
    $("#add_tag").on("click", function() {
        $(".content-tags .tags_list").animate({
            scrollTop: $('.content-tags .tags_list').get(0).scrollHeight
        }, 50);
    });

    /** Select all input **/

    $("#selectAll").click(function() {
        $(".table-site .tbody input[type=checkbox]").prop("checked", $(this).prop("checked"));
    });


    $(".table-site .tbody  input[type=checkbox]").click(function() {
        if (!$(this).prop("checked")) {
            $("#selectAll").prop("checked", false);
        }
    });

    /** hidden and poster board **/

    $(".accordion-toggle").click(function() {
        $(this).next().toggleClass("open").slideToggle("fast");
        $(this).toggleClass("active-tab .menu-link").toggleClass("active");

        $(".fa-caret-right").toggleClass("tourne-icone");

        $(" .accordion-content").not($(this).next()).slideUp("fast").removeClass("open");
        $(".accordion-toggle").not(jQuery(this)).removeClass("active-tab .menu-link").removeClass("active");
    });



    // listtags.enable();

});
