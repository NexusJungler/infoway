import Tool from "../Tool";
import FilterMediasByTypeSubTool from "./FilterMediasByTypeSubTool/FilterMediasByTypeSubTool";
import FilterMediasByOrientationSubTool from "./FilterMediasByOrientationSubTool/FilterMediasByOrientationSubTool";
import FilterMediasByAssociatedDataSubTool from "./FilterMediasByAssociatedDataSubTool/FilterMediasByAssociatedDataSubTool";
import FilterMediasWithSearchBarSubTool from "./FilterMediasWithSearchBarSubTool/FilterMediasWithSearchBarSubTool";
import ParentTool from "../ParentTool";

class FilterMediasTool extends ParentTool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__subTools = [
            new FilterMediasByTypeSubTool(),
            new FilterMediasByOrientationSubTool(),
            new FilterMediasByAssociatedDataSubTool(),
            new FilterMediasWithSearchBarSubTool(),
        ];
        this.__anFilterIsActive = false;
        this.__activeFilters = [];
        this.__$mediasContainer = $(".medias_list_container");
    }

    getAgainMediaListContainer()
    {
        this.__$mediasContainer = $(".medias_list_container");
    }

    getMediasContainer()
    {
        return this.__$mediasContainer;
    }

    isAnFilterIsActive()
    {
        return this.__anFilterIsActive;
    }

    getActivedFilters()
    {
        let filters = '';

        this.__activeFilters.forEach( (activeFilter) => {

            filters += `[${activeFilter.property}*='${activeFilter.value}']`;

        } );

        return filters;
    }

    registerNewFilter(filter= {property: null, value: null})
    {

        if( typeof filter !== 'object' )
            throw new Error(`Attempt to register an filter, but filter must be an object`);

        else if(!filter.hasOwnProperty('property'))
            throw new Error(`Attempt to register an filter, but filter must contain 'property' property`);

        else if(!filter.hasOwnProperty('value'))
            throw new Error(`Attempt to register an filter, but filter must contain 'value' property`);

        else if(this.isFilterAlreadyRegistered(filter))
            throw new Error(`Attempt to register an filter, but an filter is already registered with same datas ! Use 'replaceAnRegisteredFilter' for replacement`);

        else
            this.__activeFilters.push(filter);

    }

    replaceAnRegisteredFilter(filter)
    {

        if( typeof filter !== 'object' )
            throw new Error(`Attempt to register an filter, but filter must be an object`);

        else if(!filter.hasOwnProperty('property'))
            throw new Error(`Attempt to register an filter, but filter must contain 'property' property`);

        else if(!filter.hasOwnProperty('value'))
            throw new Error(`Attempt to register an filter, but filter must contain 'value' property`);

        else if(!this.findFilterByProperty(filter.property))
            throw new Error(`Attempt to replace an filter, but this filter is not registered ! Use 'registerNewFilter' for register it`);

        else
        {
            //console.log(this.getFilterIndexByProperty(filter.property)); debugger

            // replace registered filter with the new filter
            this.__activeFilters.splice(this.getFilterIndexByProperty(filter.property), 1, filter);
            //console.log(this.__activeFilters); debugger
            //this.__activeFilters.push(filter);
        }

    }

    removeFilter(filter)
    {

        if( typeof filter !== 'object' )
            throw new Error(`Attempt to register an filter, but filter must be an object`);

        else if(!filter.hasOwnProperty('property'))
            throw new Error(`Attempt to register an filter, but filter must contain 'property' property`);

        else if(!filter.hasOwnProperty('value'))
            throw new Error(`Attempt to register an filter, but filter must contain 'value' property`);

        else if(!this.isFilterAlreadyRegistered(filter))
            throw new Error(`Attempt to remove an filter, but this filter is not registered ! Use 'registerNewFilter' for register it`);

        else
            this.__activeFilters.splice(this.getRegisteredFilterIndex(filter), 1);

    }

    removeAllFilters()
    {
        this.__anFilterIsActive = false;
        this.__activeFilters = [];
    }

    removeFilterByProperty(property)
    {

        if(!this.findFilterByProperty(property))
            throw new Error(`Attempt to remove an filter, but this filter is not registered ! Use 'registerNewFilter' for register it`);

        this.__activeFilters.splice(this.getFilterIndexByProperty(property), 1);

    }

    findFilterByProperty(property)
    {
        return this.getFilterIndexByProperty(property) !== -1;
    }

    getFilterIndexByProperty(property)
    {
        return this.__activeFilters.findIndex( activeFilter => activeFilter.property === property );
    }

    isFilterAlreadyRegistered(filter)
    {
        return this.getRegisteredFilterIndex(filter) !== -1;
    }

    getRegisteredFilterIndex(filter)
    {
        return this.__activeFilters.findIndex( activeFilter => activeFilter.property === filter.property && activeFilter.value === filter.value );
    }

    registerFiltersInParent(filter)
    {

        if(this.isFilterAlreadyRegistered(filter))
            this.replaceAnRegisteredFilter(filter);

        else
            this.registerNewFilter(filter);

    }

    enable()
    {
        super.enable();
    }

    disable()
    {
        super.disable();
    }

}

export default FilterMediasTool;