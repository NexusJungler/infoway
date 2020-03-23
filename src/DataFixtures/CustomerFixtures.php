<?php


namespace App\DataFixtures;


use App\Entity\Admin\Country;
use App\Entity\Customer\CompanyPiece;
use App\Entity\Customer\CompanyPieceType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\{ Factory, Generator };
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;


class CustomerFixtures extends Fixture implements FixtureGroupInterface
{


    /**
     * @var Generator
     */
    private Generator $__faker;


    public function __construct()
    {
        // @see: https://github.com/fzaninotto/Faker#basic-usage
        $this->__faker = Factory::create('fr_FR');
    }


    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {

        /***      Datas creation start      ***/

        $this->loadCompanyPieceTypes($manager);
        $this->loadCompanyPieces($manager);

        /***      Datas creation end      ***/




        /***      Datas Flushing      ***/
        $manager->flush();

    }


    public static function getGroups(): array
    {
        // @see : https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html#fixture-groups-only-executing-some-fixtures
        return [ 'customer' ];
    }


    private function loadCompanyPieceTypes(ObjectManager &$manager)
    {

        $types = [
            'Filiale' => 1,
            'Continent' => 2,
            'Pays' => 3,
            'Franchise' => 4,
            'Site' => 5
        ];


        foreach ($types as $type => $level)
        {

            $companyPieceType = new CompanyPieceType();
            $companyPieceType->setName($type)
                ->setLevel($level);

            $manager->persist($companyPieceType);

        }


    }


    private function loadCompanyPieces(ObjectManager &$manager)
    {

        for ($i = 1; $i <= 5; $i++)
        {

            $pieceType = $this->getPersistedEntity($manager, ['className' => CompanyPieceType::class, 'property' => 'level', 'value' => $i]);

            // is it possible to access to another entity manager ?????
            //$countryId = $manager->getRepository(Country::class)->findOneByName("France")->getId();
            //$timeZoneId = $manager->getRepository(Country::class)->findOneByName("Europe/Paris")->getId();


            $companyPiece = new CompanyPiece();
            $companyPiece->setName($this->__faker->company)
                //->setType($pieceType)
                ->setAddress($this->__faker->address)
                ->setPostalCode($this->__faker->postcode)
                ->setPhoneNumber($this->__faker->phoneNumber)
                ->setDescription($this->__faker->text)
                ->setCity($this->__faker->city)
                ->setCountry(1)
                ->setLogoName($this->__faker->imageUrl($width = 640, $height = 480))
                ->setTimeZone(1);

            $pieceType->addCompanyPiece($companyPiece);

            $manager->persist($companyPiece);

        }


    }


    /**
     * @param ObjectManager $manager reference of ObjectManager
     * @param array $criteria used for search entity
     * @param array $options
     * @return array|object
     * @throws \Exception
     */
    private function getPersistedEntity(ObjectManager &$manager, array $criteria = [ 'className' => null, 'property' => null, 'value' => null ], array $options = [ 'maxResult' => 1, 'mode_strict' => false ])
    {

        $defaultOptions =  [ 'maxResult' => 1, 'mode_strict' => true ];
        $defaultCriteria = [ 'className' => null, 'property' => null, 'value' => null ];


        foreach (array_keys($options) as $key)
        {

            if(isset($defaultOptions[$key]))
            {

                if(gettype($defaultOptions[$key]) === gettype($options[$key]))
                    $defaultOptions[$key] = $options[$key];

                else
                    throw new \Exception(sprintf("Internal Error : type of '%s' key is not valid ! Excepted typeof is '%s', '%s' given ", $key, gettype($defaultOptions[$key]), gettype($options[$key])));

            }
            else
                throw new \Exception(sprintf("Internal Error : key '%s' is not found ! Accepted keys is : '%s'", $key, $this->implode(", ", array_keys($defaultOptions))));

        }


        if(sizeof($defaultCriteria) === sizeof($criteria))
        {

            $persistedEntities = array_values($manager->getUnitOfWork()->getScheduledEntityInsertions());

            // @see : https://wiki.php.net/rfc/arrow_functions, https://www.php.net/manual/en/function.array-filter, https://www.php.net/manual/en/function.call-user-func-array
            $filterResult = array_values( array_filter( $persistedEntities, fn($persistedEntity)  => (get_class($persistedEntity) === $criteria['className'] AND property_exists($persistedEntity, $criteria['property'])) ? call_user_func_array([$persistedEntity, 'get' . ucfirst($criteria['property'])], []) === $criteria['value'] : null) );

            if(sizeof($filterResult) > 0)
            {

                if($defaultOptions['maxResult'] === 1)
                    return array_slice($filterResult,0, $defaultOptions['maxResult'])[0];


                return array_slice($filterResult,0, $defaultOptions['maxResult']);

            }

            else
            {

                if($defaultOptions['mode_strict'])
                    throw new \Exception(sprintf("Internal Error : Cannot found entity with criteria ! Criteria used : '%s'", $this->implode(", ", $criteria)));

                else
                    return null;

            }

        }

        else
        {
            $missingKeys = array_diff(array_keys($defaultCriteria), array_keys($criteria));
            throw new \Exception(sprintf("Internal Error : missing '%s' index in criteria ! '%s' function need it for search persisted entity", $this->implode(", ", $missingKeys, false), __FUNCTION__));
        }

    }


    private function implode(string $glue, array $array, bool $addKeys = true): string
    {

        $output = "";
        $i = 0;

        foreach ($array as $index => $value)
        {

            if($addKeys)
                $output .= $index . ' => ' . $value . ( ($i !== sizeof($array) - 1) ? $glue : '' );

            else
                $output .= $value . ( ($i !== sizeof($array) - 1) ? $glue : '' );

            $i++;

        }

        return $output;

    }


}