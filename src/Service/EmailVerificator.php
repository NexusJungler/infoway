<?php


namespace App\Service;


class EmailVerificator
{

    public function __construct()
    {

    }

    public function isValidEmail(string $email): bool
    {
        // check if email have basic format
        if(filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
        {
            // check if email is valid and exist before return response !!!!
            return true;
        }

        return false;
    }

}