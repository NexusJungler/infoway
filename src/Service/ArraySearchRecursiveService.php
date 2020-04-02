<?php


namespace App\Service;


class ArraySearchRecursiveService
{

    public function __construct()
    {

    }

    public function search($search, array $array, $currentKey = null, bool $allPath = false)
    {

        $pos = false;

        foreach ($array as $key => $value)
        {

            if($value === $search)
            {

                if(is_numeric($key) && $allPath)
                    return $currentKey . '["' .$key . '"]';


                else
                    if ($allPath) return $currentKey . '["' .$key . '"]';

                    else
                    {

                        $currentKey = str_replace("[",null, $currentKey);
                        $currentKey = str_replace("]",null, $currentKey);
                        return intval($currentKey);
                    }

            }

            if(is_array($value))
            {

                $pos = $this->search($search, $value, $currentKey . '[' . $key . ']', $allPath);
                if($pos)
                    return $pos;

            }

        }

        return $pos;

    }

}