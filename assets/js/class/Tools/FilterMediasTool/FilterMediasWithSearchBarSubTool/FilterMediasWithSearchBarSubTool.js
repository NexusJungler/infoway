import SubTool from "../../SubTool";

class FilterMediasWithSearchBarSubTool extends SubTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__$container = $(".filter_with_search_container");
        this.__$target = null;
    }

    setTarget($target)
    {
        this.__$target = $target;
    }

    onTypingApplyFilter(active)
    {

        if(active)
        {
            this.__$container.find('input.filter_with_search').on('input.onTypingApplyFilter', e => {

                const filter = $(e.currentTarget).val();
                console.log(filter);

                if($(e.currentTarget).parents().hasClass('popup_container'))
                {
                    this.__$target = $(e.currentTarget).parents().find('tbody.list');

                    if(filter === '')
                    {

                        this.unHighlight(this.__$target.find('tr td mark'));

                        this.__$target.find('tr').removeClass('hidden');
                    }
                    else
                    {

                        this.__$target.find('tr').each( (index, tr) => {

                            let hidden = true;

                            $.map( $(tr).find('td'), (td) => {

                                let tdValue = $(td).text();
                                const regex = new RegExp(filter, 'g');

                                $(td).removeClass('highlight');

                                if(tdValue.search(regex) > -1 && (tdValue.search('Aucun critères') === -1 && tdValue.search('Aucun tags') === -1))
                                {
                                    $(td).html( tdValue.replace(regex, `<mark class='search_result'>${ filter }</mark>`) );
                                    hidden = false;
                                }
                                else
                                    this.unHighlight($(td).find('mark'));

                            } )

                            if(hidden)
                                $(tr).addClass('hidden');

                        } )

                    }

                }

            })
        }
        else
        {
            this.__$container.find('input.filter_with_search').off('input.onTypingApplyFilter');
        }

        return this;
    }


    unHighlight(subjects)
    {

        $(subjects).map( (index, subject) => {

            const content = $(subject).text();
            return $(subject).replaceWith(content);

        } );

    }


    enable()
    {
        super.enable();
        this.onTypingApplyFilter(true)
        ;
    }

    disable()
    {
        super.disable();
        this.onTypingApplyFilter(false)
        ;
    }

}

export default FilterMediasWithSearchBarSubTool;