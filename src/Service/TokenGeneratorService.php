<?php


namespace App\Service;


class TokenGeneratorService
{

    public function __construct()
    {

    }

    public function generate(int $length = 80)
    {
        // substr() used for return a token with same length of $length
        return substr(bin2hex(random_bytes($length)), $length);
    }

}