class filterTableSite{

    enable(){
        this.onChangeFilter(true);
        this.chechboxFilterSelect(true);
        this.ClickOpenMenubord(true);
        this.ClickNextMenubord(true);
    }
    disable(){
        this.onChangeFilter(false);
        this.chechboxFilterSelect(false);
        this.ClickOpenMenubord(false);
        this.ClickNextMenubord(false);
    }

    ClickOpenMenubord(active){
        if(active){

            $(".accordion-toggle2").click(function() {
                $(this).next().toggleClass("open").slideToggle("fast");
                $(this).toggleClass("active-tab .menu-link").toggleClass("active");

                $(".fa-caret-right").toggleClass("tourne-icone");

                $(" .accordion-content").not($(this).next()).slideUp("fast").removeClass("open");
                $(".accordion-toggle2").not(jQuery(this)).removeClass("active-tab .menu-link").removeClass("active");
            });

        }
    }

    ClickNextMenubord(active){
        if(active){

            $(".accordion-nav2").click(function (){
                let open = $(this).data("open-next");
                let nav = $("#nav"+ open)

                nav.next().toggleClass("open").slideToggle("fast");
                nav.toggleClass("active-tab .menu-link").toggleClass("active");

                $(".accordion-content_"+ (open-1)).not($(this).next()).slideUp("fast").removeClass("open");
                $("#nav"+ (open-1)).not($(this)).removeClass("active-tab .menu-link").removeClass("active");

            })

            // $(".info-gestion-site li ").click(function (){
            //
            //     let index =  $(this).index()+1;
            //     let index_li = $("#nav"+ index);
            //
            //     console.log(index);
            //
            //     $(".accordion-content").not($(this).next()).slideUp("fast").removeClass("open");
            //     $(".accordion-toggle2").not($(this)).removeClass("active-tab .menu-link active");
            //
            //     index_li.next().toggleClass("open").slideToggle("fast");
            //     index_li.toggleClass("active-tab .menu-link").toggleClass("active");
            //
            // })

        }
    }



    onChangeFilter(active) {
        if(active){

            $('.site-container .filter_city').change(function(){

                $('.site-container table tbody tr').hide();

                let cityValue = $('.filter_city').val();

                $('.site-container table.table-custome tr').each(function() {

                    if( cityValue == 0 || cityValue == $(this).find('td.city').data( 'format' ) ){
                        if(  cityValue == 0 ){ $('.site-container table tbody tr').show(); }
                        $(this).show();
                    }
                });
            });

        }else{

        }
    }

    chechboxFilterSelect(active){
        if(active){

            $(".site-btn-filter").on("click", function () {

                let checkedSelect = $(".filters_select_site input:checkbox:checked").length;

                if ( checkedSelect >= 1){

                    $(".table-custome .tbody-content tr ").each((index, element) => {

                        $(element).hide();

                        let menuboardValues = $(".filter_by_menuboard input:checkbox:checked").map( (index, input) => $(input).val() );
                        let criterionValues =  $(".filter_by_criterion input:checkbox:checked").map( (index, input) => $(input).val() );
                        let tagsValues = $(".filter_by_tags input:checkbox:checked").map( (index, input) => $(input).val() );
                        let counter = 0;


                        $('.filters_select_site div.display-content-poste').map( (index, element) => {
                            if( $(element).find('input:checkbox:checked').length > 0 )
                                counter++;
                        });

                        let i = 0;

                        if(menuboardValues.length > 0){
                            menuboardValues.each( (index,value) => {
                                if($(element).text().indexOf(value) >= 0)
                                    i++;
                            });
                        }

                        if(criterionValues.length > 0){
                            criterionValues.each( (index,value) => {
                                if($(element).text().indexOf(value) >= 0)
                                    i++;
                            });
                        }

                        if(tagsValues.length > 0){
                            tagsValues.each( (index,value) => {
                                if($(element).text().indexOf(value) >= 0)
                                    i++;
                            });
                        }

                        if(i === counter)
                            $(element).show();
                        0
                    });

                }else{

                    $(".table-custome .tbody-content tr").show();

                }

            })


            // $(".filter_by_categories input:checkbox").on("change", function () {
            //
            //     let categorie = $(".filter_by_categories input:checkbox:checked").map(function () {
            //         return $(this).val()
            //     });
            //
            //     // $(".table-custome tr").show();
            //      $(".city").filter(function () {
            //
            //         let city = $(this).text(),
            //             index = $.inArray(city, categorie);
            //          $(".table-custome tr").hide()
            //         return index >= 0
            //
            //     }).parent().show();
            //
            // })
                // .first().change()
        }else{
            $(".site-btn-filter").off('click.chechboxFilterSelect');
        }
    }

}

export default filterTableSite;