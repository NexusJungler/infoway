import ToolBox from "./ToolBox";

class Tool {

    constructor()
    {
        this.__name = null;
        this.__isActived = false;
        this.__toolBox = null;
        this.__subTools = [];
    }

    getName()
    {
        return this.__name;
    }

    setName(name)
    {

        if(typeof name !== 'string' || typeof name === "undefined" || name === "" || name === null)
            throw new Error(`${ this.__name }.name must be instance of string, but '${typeof name}' given !`);

        this.__name = name;

        return this;
    }

    setToolBox(toolBox)
    {

        if( !(toolBox instanceof ToolBox) )
            throw new Error(`${ this.__name }.setToolBox must be instance of ToolBox, but '${typeof toolBox}' given !`);

        this.__toolBox = toolBox;
        // console.log(`ToolBox is now added in '${this.__name}' !`);

        return this;
    }

    getToolBox()
    {
        return this.__toolBox;
    }

    getUrlParam(parameterName)
    {
        let urlParameter = "undefined";

        if(window.location.href.indexOf(parameterName) > -1)
        {
            window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                urlParameter[key] = value;
            });
        }

        return urlParameter;
    }

    showLoadingPopup()
    {
        $('.popup_loading_container').addClass('is_open');
    }

    hideLoadingPopup()
    {
        $('.popup_loading_container').removeClass('is_open');
    }

    isActive()
    {
        return this.__isActived;
    }

    enable()
    {
        if(!this.isActive())
        {
            // console.log(`'${this.__name}' is enabled !`);
            this.__isActived = true;
        }
    }

    disable()
    {
        if(this.isActive())
        {
            // console.log(`'${this.__name}' is disabled !`);
            this.__isActived = false;
        }
    }

}

export default Tool;