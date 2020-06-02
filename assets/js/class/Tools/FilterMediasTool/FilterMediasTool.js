import Tool from "../Tool";
import FilterMediasByTypeSubTool from "./FilterMediasByTypeSubTool/FilterMediasByTypeSubTool";
import FilterMediasByOrientationSubTool from "./FilterMediasByOrientationSubTool/FilterMediasByOrientationSubTool";
import FilterMediasByCharacteristicsSubTool from "./FilterMediasByCharacteristicsSubTool/FilterMediasByCharacteristicsSubTool";

class FilterMediasTool extends Tool
{

    constructor()
    {
        super();
        this.__name = this.constructor.name;
        this.__subTools = [
            new FilterMediasByTypeSubTool(),
            new FilterMediasByOrientationSubTool(),
            new FilterMediasByCharacteristicsSubTool(),
        ];
        this.__anFilterIsActive = false;
        this.__activeFilters = [];
    }

    activeSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        this.__subTools[ this.getSubToolIndex(subToolName) ].setToolBox(this.getToolBox());
        this.__subTools[ this.getSubToolIndex(subToolName) ].setParent(this);
        this.__subTools[ this.getSubToolIndex(subToolName) ].enable();

        return this;
    }

    getSubTool(subToolName)
    {

        if(!this.subToolIsRegistered(subToolName))
            throw new Error(`'${subToolName}' subTool is not registered !`);

        return this.__subTools[ this.getSubToolIndex(subToolName) ];

    }

    subToolIsRegistered(subToolName)
    {
        return this.getSubToolIndex( subToolName ) !== -1;
    }

    getSubToolIndex(subToolName)
    {
        return this.__subTools.findIndex( subTool =>  subTool.getName() === subToolName );
    }

    isAnFilterIsActive()
    {
        return this.__anFilterIsActive;
    }

    getActiveFilters()
    {
        return this.__activeFilters;
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
            throw new Error(`Attempt to register an filter, but an filter is already registered with same property ! Use 'replaceAnRegisteredFilter' for replacement`);

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

        else if(!this.isFilterAlreadyRegistered(filter))
            throw new Error(`Attempt to replace an filter, but this filter is not registered with same property ! Use 'registerNewFilter' for register it`);

        else
        {
            this.__activeFilters.splice(this.getRegisteredFilterIndex(filter), 1);

            this.__activeFilters.push(filter);
        }

    }

    isFilterAlreadyRegistered(filter)
    {
        return this.getRegisteredFilterIndex(filter) !== -1;
    }

    getRegisteredFilterIndex(filter)
    {
        return this.__activeFilters.findIndex( activeFilter => activeFilter.property === filter.property );
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