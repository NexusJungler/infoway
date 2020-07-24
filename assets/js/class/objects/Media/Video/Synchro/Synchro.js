import Video from "../Video";


class Synchro extends Video
{

    constructor()
    {
        super();
        this.__className = this.constructor.name;
        this.__preview = "";
        this.__position = 0;
        this.__videos = [];
    }

    getPreview()
    {
        return this.__preview;
    }

    setPreview(preview) {

        if( typeof preview === 'undefined')
        {
            preview = this.buildHtml();
        }

        if( !(typeof preview !== "string") )
            throw new Error(`${ this.__className }.preview must be instance of string, but '${typeof position}' given !`);

        this.__preview = preview;

        return this;
    }

    getPosition() {
        return this.__position;
    }

    setPosition(position) {

        if( !(typeof position !== "number") )
            throw new Error(`${ this.__className }.position must be instance of number, but '${typeof position}' given !`);

        this.__position = position;

        return this;

    }

    getVideos()
    {
        return this.__videos;
    }

    addVideo(video)
    {

        if( !(video instanceof Video) )
            throw new Error(`Parameter of ${ this.__className }.addVideo() must be instance of Video, but '${typeof video}' given !`);

        if(!this.videoIsAlreadyRegistered(video))
        {
            video.addSynchro(this);
            this.__videos.push(video);
        }

        return this;
    }

    removeVideo(video)
    {

        if( !(video instanceof Video) )
            throw new Error(`Parameter of ${ this.__className }.addVideo() must be instance of Video, but '${typeof video}' given !`);

        if(this.videoIsAlreadyRegistered(video))
        {
            this.__videos.splice(this.getRegisteredVideoIndex(video) , 1);

            video.removeSynchro(this);
        }

        return this;
    }

    removeAllVideos()
    {
        this.__videos.map( video => {
            video.removeSynchro(this);
        } );

        this.__videos = [];

        return this;
    }

    videoIsAlreadyRegistered(video)
    {
        return this.getRegisteredVideoIndex(video) !== -1;
    }

    getRegisteredVideoIndex(video)
    {
        return this.__videos.findIndex( registeredVideo =>  registeredVideo.getName() === video.getName() );
    }

    buildHtml()
    {
        return `
        
        <div class="synchro" id="synchro_video_${ this.__id }">
            
            <div class="synchro_preview_container">
                ${ this.__preview }
            </div>
            
            <div class="synchro_video_name_container">
                <p class="synchro_video_name"> ${ this.__name } </p>
            </div>
            
            <div class="synchro_video_position_container">
                <p class="synchro_video_position"> ${ this.__position } </p>
            </div>
            
        </div>
        
        `;
    }

}

export default Synchro;