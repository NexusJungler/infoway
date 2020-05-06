// import style css
import "../css/tags.scss";

// jquery 
const $ = require('jquery');
global.$ = global.jQuery = $;

$(".tab-content-criteres .modified-tags").click(function(){

    $.each($(".row-criterion input[type='checkbox']:checked"), function(){
           var id_criterion = $(this).attr('data-criterion');
           
           window.location.href= "/tags/"+ id_criterion +"/edit"
           console.log(id_criterion);
    })
})

// var $collectionHolder;

// // setup an "add a tag" link
// var $addTagButton = $('<button type="button" class="add_tag_link">Créer TAG</button>');
// var $newLinkLi = $('<li></li>').append($addTagButton);

// jQuery(document).ready(function() {
//     // Get the ul that holds the collection of tags
//     $collectionHolder = $('#tag_list');

//     // add the "add a tag" anchor and li to the tags ul
//     $collectionHolder.append($newLinkLi);

//     // count the current form inputs we have (e.g. 2), use that as the new
//     // index when inserting a new item (e.g. 2)
//     $collectionHolder.data('index', $collectionHolder.find('input').length);

//     $addTagButton.on('click', function(e) {
//         // add a new tag form (see next code block)
//         addTagForm($collectionHolder, $newLinkLi);
//     });
// });

// function addTagForm($collectionHolder, $newLinkLi) {
//     // Get the data-prototype explained earlier
//     var prototype = $collectionHolder.data('prototype');

//     // get the new index
//     var index = $collectionHolder.data('index');

//     var newForm = prototype;
//     // You need this only if you didn't set 'label' => false in your tags field in TaskType
//     // Replace '__name__label__' in the prototype's HTML to
//     // instead be a number based on how many items we have
//     // newForm = newForm.replace(/__name__label__/g, index);

//     // Replace '__name__' in the prototype's HTML to
//     // instead be a number based on how many items we have
//     newForm = newForm.replace(/__name__/g, index);

//     // increase the index with one for the next item
//     $collectionHolder.data('index', index + 1);

//     // Display the form in the page in an li, before the "Add a tag" link li
//     var $newFormLi = $('<li></li>').append(newForm);
//     $newLinkLi.before($newFormLi);
// }
