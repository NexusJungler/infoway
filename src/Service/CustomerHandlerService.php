<?php
namespace App\Service;

use App\Entity\Admin\Contact;
use App\Entity\Admin\Customer;
use App\Errors\CustomerError;
use App\Errors\InvalidCustomerLogo;
use App\Errors\InvalidCustomerNameError;
use App\Repository\Admin\CustomerRepository;
use Doctrine\ORM\EntityManager;

class CustomerHandlerService {

    private $_customerRepo ;
    private $_contactHandler ;
    private $_flashBagHandler ;

    public function __construct(CustomerRepository $customerRepo,ContactHandlerService $contactHandlerService, FlashBagHandler $flashBagHandler)
    {
        $this->_customerRepo = $customerRepo;
        $this->_contactHandler = $contactHandlerService ;
        $this->_flashBagHandler = $flashBagHandler ;

    }

    public function areCustomerDatasValid(Customer $customer) : bool{
        return (
             $this->isNameValid( $customer->getName() ) &&
             $this->isLogoIsValid( $customer->getLogo() ) &&
             $this->isContactIsValid( $customer->getContact() )
        ) ;
    }

    public function isNameValid($name) : bool{
        if(  is_string($name) ){
            return true;
        }else{
            $error = new InvalidCustomerNameError() ;
            $this->_flashBagHandler->getFlashBag()->set('error', $error->errorToArray() ) ;
            return false;
        }
    }

    public function isLogoIsValid($logo) : bool{
        if(  is_string($logo) ){
            return true;
        }else{
            $error = new InvalidCustomerLogo() ;
            $this->_flashBagHandler->getFlashBag()->set('error', $error->errorToArray() ) ;
            return false;
        }
    }

    public function isContactIsValid(Contact $contact) : bool{
        return $this->_contactHandler->areContactDatasValids( $contact ,  new CustomerError() ) ;
    }



    public function isCustomerExist(string $name) :bool{
        $existingCustomer = $this->_customerRepo->findOneBy(['name' => $name]) ;
        return $existingCustomer instanceof Customer ;
    }
    public function filterArrayById( $arrayToFilter ){
        $arrayFilteredById = [] ;
        foreach( $arrayToFilter as $entryToFilter ) {
            if( ! method_exists($entryToFilter, 'getId') )  throw new \Error('cannot find Getter') ;
            $arrayFilteredById[ $entryToFilter->getId() ] = $entryToFilter ;
        }
        return  $arrayFilteredById ;
    }
}