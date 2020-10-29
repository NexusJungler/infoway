require("../css/popups/popup.scss");
require("../css/media/show_media.scss");

let filtertablesite = new filterTableSite();
let filtremediacard = new filtreMediaCard();

import ToolBox from "./class/Tools/ToolBox";
import filterTableSite from "./site/FiltreTable/FiltreTable";
import filtreMediaCard from "./Filtrer/FiltreMedia";

const toolBox = new ToolBox();
toolBox.activeTool("PaginatorHandler")
       .activeTool("FilterMediasTool")
       .activeTool("PopupHandler")
       .activeTool("AnimateTool")
       .activeTool("MediathequeActionButtonHandler");

toolBox.getTool("PopupHandler").activeSubTool("MediaInfoSheetHandler")
                                        .activeSubTool("MediaWaitingIncrustationHandler")
                                        .activeSubTool("ArchivedMediasHandlerTool")
                                        .activeSubTool("AssociationPopupHandler", ["all"])
                                        .activeSubTool("UploadHandlerTool")
                                        .activeSubTool("MediaExpandedMiniatureDisplayHandler");

toolBox.getTool("FilterMediasTool").activeAllSubTools();

toolBox.getTool("MediathequeActionButtonHandler").activeAllSubTools();

toolBox.getTool("AnimateTool").activeSubTool("ShowMediaFilter");


$(function() {

    filtertablesite.enable();
    filtremediacard.enable();
});


$(".synchro_container").each( (index, synchro) => {
    let synchro_element_nb = $(synchro).find(".synchro_elements").length;

    if(synchro_element_nb > 6 ){
        $(synchro).find(".synchro_elements").css({"width" : "calc(100% / " + synchro_element_nb + ")"  });
    }else {

    }
})



$(".open_tags").on('click' , e => {
    let tags =  $(e.currentTarget).parents(".media_tags_container").find(".nav_tags");

    if (tags.hasClass("open")){
        tags.removeClass("open");
    }else{
        tags.addClass("open");
    }

});

//
// $(".filter_by_criterion .display-content-down .container-input ").each( function () {
//
//
//     let inputnb = $(this).find("input:checkbox:checked").length;
//     console.log(inputnb)
//     if(inputnb == 1 ){
//         $(".criterion_input_filter").attr( 'checked', true );
//     }else {
//         $(".criterion_input_filter").removeAttr('checked');
//     }
// })