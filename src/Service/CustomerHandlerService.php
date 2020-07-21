<?php
namespace App\Service;

use App\Entity\Admin\Contact;
use App\Entity\Admin\Customer;
use App\Errors\Customer\CustomerDatabaseCreationError;
use App\Errors\Customer\CustomerError;
use App\Errors\Customer\ExistingCustomerError;
use App\Errors\Customer\ExistingDatabaseError;
use App\Errors\Customer\InvalidCustomerLogo;
use App\Errors\Customer\InvalidCustomerNameError;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

class CustomerHandlerService {

    private $_customerRepo ;
    private $_contactHandler ;
    private $_flashBagHandler ;
    private $_databaseHandler ;
    private $_logoDir ;
    private $_em ;

    public function __construct(EntityManagerInterface $em ,ContactHandlerService $contactHandlerService, FlashBagHandler $flashBagHandler, DatabaseAccessHandler $databaseHandler, string $logoDir)
    {
        $this->_em = $em;
        $this->_customerRepo = $this->_em->getRepository(Customer::class );
        $this->_contactHandler = $contactHandlerService ;
        $this->_flashBagHandler = $flashBagHandler ;
        $this->_databaseHandler = $databaseHandler ;
        $this->_logoDir = $logoDir ;

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


    public function isDatabaseWithCustomerEnteredNameAlreadyExist(Customer $customer) :bool {

        if( $this->_databaseHandler->databaseExist( $customer->getName() ) ){
            $error = new ExistingDatabaseError() ;
            $this->_flashBagHandler->getFlashBag()->set('error',$error->errorToArray() ) ;

            return true;
        }else{
            return false;
        }

    }

    public function createCustomerDatabase( Customer $customer ){

        if      ( $this->isDatabaseWithCustomerEnteredNameAlreadyExist($customer) ) return false;

        elseif  (   ! $this->_databaseHandler->registerDatabaseConnexion( $customer->getName() )
                ||  ! $this->_databaseHandler->createDatabase( $customer->getName() )
                ||  ! $this->_databaseHandler->hydrateDb($customer->getName())
        )
        {
            $error = new CustomerDatabaseCreationError() ;
            $this->_flashBagHandler->getFlashBag()->set('error',$error->errorToArray() ) ;

            return false ;
        }
        else {

            return true ;
        }

    }

    public function insertCustomerInDb( Customer $customer ){
        $this->_em->persist($customer);
        $this->_em->flush();
        return is_int( $customer->getId() ) ;
    }

    public function handleCustomerLogoFromForm(Customer $customer, FormInterface $form) : bool
    {

        $logoFile = $form->get('logo')->getData();

        if($logoFile){
            $newFileName = $customer->getName() . '.' . $logoFile->guessExtension();
            $logoFile->move(
                $this->_logoDir,
                $newFileName
            );
            $customer->setLogo($newFileName);

            return true ;
        }else{
            $error = new InvalidCustomerLogo() ;
            $this->_flashBagHandler->getFlashBag()->set('error',$error->errorToArray() ) ;
            return false;
        }
    }

    public function isCustomerWithNameEnteredAlreadyExist(Customer $customer) :bool{

        $existingCustomer = $this->_customerRepo->findOneBy( ['name' => $customer->getName()] ) ;

        if( $existingCustomer instanceof Customer )
        {
            $error = new ExistingCustomerError() ;
            $this->_flashBagHandler->getFlashBag()->set('error',$error->errorToArray() ) ;

            return true;
        }else{
            return false;
        }
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