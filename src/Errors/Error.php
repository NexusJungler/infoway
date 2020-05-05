<?php

namespace App\Errors;


class Error {

    private ?string $message = null ;
    protected ?string $column = null ;
    protected array $errorValue = [] ;

    /**
     * ContactError constructor.
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        $this->message = $message;
        if($this->column !== null && $this->message !== null ) $this->errorValue  = [$this->column => $this->message ] ;

    }


    public function setMessage( string $message ){
        $this->message = $message ;
    }

    public function addChildError(Error $childError){
        $this->errorValue = $childError->errorToArray() ;
    }

    public function errorToArray(){
        return [$this->column => $this->errorValue ] ;
    }
}

