$(".tab-content.tab-create #create-entity-choice input[type='radio']").on("change", e => {

    $(".tab-content-title .title span").text($(e.currentTarget).val());
    $("form[name='create_company_piece'] input[name='type']").val($(e.currentTarget).data('id'));

})

