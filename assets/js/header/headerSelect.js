class headerSelect{

    enable(){
        this.onLoadSelect();
        this.onHeaderSelect(true);
        this.onHeaderSelectSite(true);
        this.onButtonClickBackselect(true);

    }
    disable(){
        this.onHeaderSelect(false);
        this.onHeaderSelectSite(false);
        this.onButtonClickBackselect(false);
    }

    onLoadSelect(){
        $( window ).on("load", function () {
            $('.logo__enseigne img').show();
            $('p.number__enseigne').show();
            $('.btn_maj').show();
            $('#select_site').removeClass('select-pointer_events');
        })

    }

    onHeaderSelect(active) {
        if(active){
            $('.logo__enseigne img').hide();

            $('#enseigne').change(function(){

                let selectEnseigne = $('#enseigne').val();
                let nbEnseigne = selectEnseigne.length;

                $('p.number__enseigne span').text(nbEnseigne);
                $('.logo__enseigne #customer_logo').attr('src', "/build/images/"+selectEnseigne+".png");

               if(selectEnseigne == null ) {

                   $('.logo__enseigne img').hide();
                   $('p.number__enseigne').hide();
                   $('.btn_maj').hide();
                   $('#select_site').addClass('select-pointer_events');

               } else if(selectEnseigne == "" ) {

                   $('.logo__enseigne img').hide();
                   $('p.number__enseigne').hide();
                   $('.btn_maj').hide();
                   $('#select_site').addClass('select-pointer_events');

               } else{

                   $('.logo__enseigne img').show();


                   $('p.number__enseigne').show();
                   $('.btn_maj').show();
                   $('#select_site').removeClass('select-pointer_events');
               }
            });

        }else{
            $("#enseigne").off('click.onHeaderSelect');
        }
    }

    onHeaderSelectSite(active) {
        if(active){

            $('#select_site').change(function(){

                let selectSite = $('#select_site').val();

                if(selectSite== null ) {

                    $('p.number__site').hide();
                    $('.update__site').hide();
                    $('.btn_maj').show();
                    $('.btn-back').hide();

                } else if(selectSite == "" ) {

                    $('p.number__site').hide();
                    $('.update__site').hide();
                    $('.btn_maj').show();
                    $('.btn-back').hide();

                } else{

                    $('p.number__site').show();
                    $('.update__site').show();
                    $('.btn_maj').hide();
                    $('.btn-back').show();
                }
            });

        }else{
            $("#site").off('click.onHeaderSelect');
        }
    }

    onButtonClickBackselect(active) {
        if(active){
            let selectSite = $('#select_site').val();

            $('.all__enseigne .btn-back').on("click", function(){

                $("#select_site").find("option:selected").prop("selected", false);

                $("#select_site").find(".site_choise").prop("selected", true);

                if( selectSite == "" ){
                    $('p.number__site').hide();
                    $('.update__site').hide();
                    $('.btn-back').hide();
                    $('.btn_maj').show();
                }else{

                }

            });

        }else{
            $("all__enseigne .btn-back").off('click.onButtonClickBackselect');
        }
    }

}

export default headerSelect;