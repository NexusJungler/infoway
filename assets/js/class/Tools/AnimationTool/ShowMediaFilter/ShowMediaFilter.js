import Tool from "../../Tool";
import SubTool from "../../SubTool";

class ShowMediaFilter extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $(".left_filter_container .filters_by_associated_data_container");
        this.__$openFilter = $(".filters_by_associated_data_container");
    }


    ClickShowFiltrer(active) {

        if(active)
        {
            this.__$container.find(".btn_show_filter").on("click.ClickShowFiltrer", e => {

                let width = "35%";

                if($(e.currentTarget).hasClass("btn_show_filter_open")){
                    this.__$container.find('.filter_container').addClass("filter_active");
                    this.__$container.find('.btn').removeClass("btn_show_filter_open");
                    $(e.currentTarget).find('i').removeClass('fa-plus').addClass('fa-minus');
                    width = "70%" ;

                } else {
                    this.__$container.find('.filter_container').removeClass("filter_active");
                    this.__$container.find('.btn').addClass("btn_show_filter_open");
                    $(e.currentTarget).find('i').removeClass('fa-minus').addClass('fa-plus');
                    width = "35%" ;
                }

                this.__$container.animate({
                    width: width
                }, {
                    queue: false,
                    duration: 1000
                })

            })
        }
        else
        {
            this.__$container.find(".btn_show_filter").off("click.ClickShowFiltrer")
        }

        return this;
    }

    ClickOpenFilterAssocier(active) {

        if(active)
        {
            this.__$openFilter.find(".open_filter ").on("click.ClickOpenFilterAssocier", e => {

                let width = "0%";

                if($(e.currentTarget).hasClass("open_filter_show")){
                    console.log('text');
                    this.__$openFilter.find('.filter_container_association_open').addClass("filter_active");
                    this.__$openFilter.find(".open_filter").removeClass("open_filter_show");
                    $(e.currentTarget).find('i').removeClass('fa-plus').addClass('fa-minus');
                    this.__$openFilter.find('.filter_container_association_open').css({"display":""})
                    // this.__$openFilter.find('.filter').show();
                    width = "100%" ;

                } else {
                    console.log("fini");
                    this.__$openFilter.find('.filter_container_association_open').removeClass("filter_active");
                    this.__$openFilter.find(".open_filter").addClass("open_filter_show");
                    $(e.currentTarget).find('i').removeClass('fa-minus').addClass('fa-plus');
                    // this.__$openFilter.find('.filter').hide();
                    width = "0%" ;
                }


                $('.filter_container_association_open').animate({
                    width: width
                }, {
                    queue: false,
                    duration: 1000
                });

            })
        }
        else
        {
            this.__$openFilter.find(".open_filter ").off("click.ClickOpenFilterAssocier")
        }

        return this;
    }



    ClickOpenMenubord(active){
        if(active){

            $(".menu-list-table .accordion-toggle").click(function() {
                $(this).next().toggleClass("open").slideToggle("fast");
                $(this).toggleClass("active-tab .menu-link").toggleClass("active");

                $(".menu-list-table .accordion-content").not($(this).next()).slideUp("fast").removeClass("open");
                $(".menu-list-table .accordion-toggle").not(jQuery(this)).removeClass("active-tab .menu-link").removeClass("active");
            });


        }
    }



    enable()
    {
        super.enable();
        this.ClickShowFiltrer(true)
        this.ClickOpenFilterAssocier(true)
    }

    disable()
    {
        super.disable();
        this.ClickShowFiltrer(false)
        this.ClickOpenFilterAssocier(false)
    }

}

export default ShowMediaFilter;