<?php


namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;

class ExpectedChangesList
{
    private $_currentObject;
    private ArrayCollection $_expectedChanges;
    private ArrayCollection $_expectedDates;

    public function __construct() {
        $this->_expectedChanges = new ArrayCollection();
        $this->_expectedDates = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getExpectedChanges()
    {
        return $this->_expectedChanges;
    }

    public function addExpectedChange(ExpectedChange $expectedChange): self
    {
        if (!$this->_expectedChanges->contains($expectedChange)) {
            $this->_expectedChanges[] = $expectedChange;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrentObject()
    {
        return $this->_currentObject;
    }

    /**
     * @param mixed $currentObject
     */
    public function setCurrentObject($currentObject): void
    {
        $this->_currentObject = $currentObject;
    }

    public function addExpectedDate(Date $date): self
    {
        if (!$this->_expectedDates->contains($date)) {
            $this->_expectedDates[] = $date;
        }
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getExpectedDates(): ArrayCollection
    {
        return $this->_expectedDates;
    }


}