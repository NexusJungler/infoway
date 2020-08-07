<?php


namespace App\Service;


use Exception;

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

    public function countOccurrence($search, array $simpleArray)
    {

        $number = 0;

        foreach ($simpleArray as $item)
        {

            if(is_array($item))
                throw new Exception("Attempt to count occurence in recursive array but this function accept only simple array !");

            if($item === $search)
                $number++;
        }

        return $number;
    }

}