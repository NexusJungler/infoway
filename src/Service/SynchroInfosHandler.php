<?php


namespace App\Service;


use App\Entity\Customer\Synchro;
use App\Entity\Customer\SynchroElement;
use DateTime;
use Doctrine\ORM\PersistentCollection;
use Exception;

class SynchroInfosHandler
{

    /**
     * @param Synchro $synchro
     * @return array
     */
    public function getSynchroDiffDates(Synchro $synchro): array
    {

        $synchroDiffStart = "";
        $synchroDiffEnd = "";

        if(sizeof($synchro->getSynchroElements()->getValues()) > 0)
        {
            $synchroElements = $synchro->getSynchroElements()->getValues();
            //$synchroElementsLength = sizeof($synchroElements);

            $synchroDiffStart = $synchroElements[0]->getDiffusionStart()->format("Y-m-d");
            $synchroDiffEnd = end($synchroElements)->getDiffusionEnd()->format("Y-m-d");

        }

        return [
            $synchroDiffStart,
            $synchroDiffEnd
        ];
    }

    /**
     * @param SynchroElement[] $synchroElements
     * @param string $propertyName
     * @return array
     * @throws Exception
     */
    public function getAllSynchroElementsAssociatedPropertyIds(array  $synchroElements, string $propertyName)
    {

        $ids = [];

        foreach ($synchroElements as $synchroElement)
        {

            if( !method_exists($synchroElement, "get" . ucfirst($propertyName)) )
            {

                if( !method_exists($synchroElement, "get" . ucfirst($propertyName). "s") )
                    throw new Exception( sprintf("No method '%s' or '%s' found in '%s'", ("get" . ucfirst($propertyName)), ("get" . ucfirst($propertyName) . "s"), get_class($synchroElement)) );

                else
                    $propertyName .= 's';
            }

            $associatedProperty = call_user_func_array([ $synchroElement, "get" . ucfirst($propertyName) ], []);

            if( ($associatedProperty instanceof PersistentCollection)  )
            {
                if(!empty($associatedProperty->getValues()))
                {

                    foreach ($associatedProperty->getValues() as $value)
                    {
                        $ids[] = $value->getId();
                    }

                }
            }
            else
                $ids[] = $associatedProperty->getId();

        }

        return $ids;
    }
    
}