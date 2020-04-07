<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Contact;
use App\Entity\Admin\Customer;
use App\Entity\Customer\Site;
use App\Repository\Customer\SiteRepository;
use App\Service\ArraySearchRecursiveService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{

    private $_registry ;
    /**
     * @var ArraySearchRecursiveService
     */
    private $__searchRecursiveService;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
        $this->_registry = $registry ;
        $this->__searchRecursiveService = new ArraySearchRecursiveService();
    }

    public function findCustomerWithSiteByName($customerName)
    {

        $customer = $this->findOneByName($customerName);

        $customManager = $this->_registry->getManager($customerName);

        $customerSiteRepo = $customManager->getRepository(Site::class);

        $customerSites = $customerSiteRepo->findAll();

        dd($customerSites);

    }


    /**
     * Add new contact in customer if is not already exist
     *
     * @param array $contactsSend
     * @param Customer $customer
     */
    public function addNewContact(array $contactsSend, Customer &$customer)
    {

        $contactRepo = $this->_em->getRepository(Contact::class);

        foreach ($contactsSend as $contactSend)
        {

            $contact = $contactRepo->findOneByEmail($contactSend['email']) ?? new Contact();

            $contact->setFirstName($contactSend['firstname'])
                    ->setLastName($contactSend['lastname'])
                    ->setPhoneNumber($contactSend['phonenumber'])
                    ->setEmail($contactSend['email'])
                    ->setStatus($contactSend['status']);

            if($contact->getId() === null)
            {
                $customer->addContact($contact);
            }

        }

    }


    /**
     * Remove contact from customer if is removed by admin in form
     *
     * @param array $contactsSend
     * @param Customer $customer
     */
    public function removeUnnecessaryContact(array $contactsSend, Customer &$customer)
    {

        $contactsRegistered = $customer->getContacts()->getValues();

        if(sizeof($contactsRegistered) > sizeof($contactsSend))
        {

            foreach ($contactsRegistered as $contact)
            {
                if($this->__searchRecursiveService->search($contact->getEmail(), $contactsSend) === false)
                    $customer->removeContact($contact);
            }

        }

    }
    
    
    // /**
    //  * @return Customer[] Returns an array of Customer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Customer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
