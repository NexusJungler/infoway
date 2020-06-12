import Tool from "../../Tool";
import SubTool from "../../SubTool";

class ShowMediaFilter extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $(".filter_by_characteristics_container");
    }


    ClickShowFiltrer(active)
    {

        if(active)
        {
            this.__$container.find(".btn_show_filter").on("click.ClickShowFiltrer", e => {



                let width = "30%"

                if($(e.currentTarget).hasClass("btn_show_filter_open")){
                    console.log("goo");
                    this.__$container.find('.filter_container').addClass("filter_active");
                    this.__$container.find('.btn').removeClass("btn_show_filter_open");

                    width = "70%" ;

                } else {
                    console.log("goo5544");
                    this.__$container.find('.filter_container').removeClass("filter_active");
                    this.__$container.find('.btn').addClass("btn_show_filter_open");

                    width = "30%" ;
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



    enable()
    {
        super.enable();
        this.ClickShowFiltrer(true)
    }

    disable()
    {
        super.disable();
        this.ClickShowFiltrer(false)
    }

}

export default ShowMediaFilter;