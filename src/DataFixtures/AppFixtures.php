<?php

namespace App\DataFixtures;



use App\Entity\Admin\Country;
use App\Entity\Admin\Customer;
use App\Entity\Admin\Feature;
use App\Entity\Admin\Perimeter;
use App\Entity\Admin\Permission;
use \App\Entity\Customer\Role;
use App\Entity\Admin\TimeZone;
use App\Entity\Admin\User;
use App\Service\TokenGeneratorService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\{ Factory, Generator };
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;



class AppFixtures extends Fixture implements FixtureGroupInterface
{


    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $__encoder;


    /**
     * @var Generator used for generate fake data
     */
    private Generator $__faker;


    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->__encoder = $encoder;

        // @see: https://github.com/fzaninotto/Faker#basic-usage
        $this->__faker = Factory::create('fr_FR');
    }



    public function load(\Doctrine\Persistence\ObjectManager $manager) {

        /***      Datas creation start      ***/

        $this->loadTimeZones($manager);
        $this->loadCountries($manager);
        $this->loadCustomers($manager);
        $this->loadUsers($manager);

        /***      Datas creation end      ***/




        /***      Datas Flushing      ***/
        $manager->flush();

    }


    public static function getGroups(): array
    {
        // @see : https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html#fixture-groups-only-executing-some-fixtures
        return [ 'admin' ];
    }


    private function loadPermissions(\Doctrine\Persistence\ObjectManager &$manager)
    {

        $roleGod = $this->getPersistedEntity($manager, ['className' => Role::class, 'property' => 'name', 'value' => 'God']);
        $roleSuperAdmin = $this->getPersistedEntity($manager, ['className' => Role::class, 'property' => 'name', 'value' => 'Super Admin']);
        $roleAdmin = $this->getPersistedEntity($manager, ['className' => Role::class, 'property' => 'name', 'value' => 'Admin']);
        $roleUser = $this->getPersistedEntity($manager, ['className' => Role::class, 'property' => 'name', 'value' => 'User']);

        $features = [
            0 => [ 'name' => 'Template', 'branch' => null,
                'permissions' => [
                    [ 'name' => 'Accéder au stage 1', 'roles' => [ $roleGod, $roleSuperAdmin ] ],
                    [ 'name' => 'Accéder au stage 2', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin ] ],
                    [ 'name' => 'Accéder au stage 3', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Accéder à un template', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Créer un template', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Editer un template', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Supprimer un template', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Importer un template', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Dupliquer un template', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                ]
            ],

            1 => [ 'name' => 'Programmation', 'branch' => null,
                'permissions' => [
                    [ 'name' => 'Afficher une programmation', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Créer une programmation', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Editer une programmation', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Supprimer une programmation', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Dupliquer une programmation', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                ]
            ],

            2 => [ 'name' => 'Incruste', 'branch' => null,
                'permissions' => [
                    [ 'name' => 'Afficher une incruste', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Créer une incruste', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Editer une incruste', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Supprimer une incruste', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Importer une incruste', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Dupliquer une incruste', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                ]
            ],

            3 => [ 'name' => 'Priceboard', 'branch' => null,
                'permissions' => [
                    [ 'name' => 'Afficher un priceboard', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Créer un priceboard', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Editer un priceboard', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Supprimer un priceboard', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Importer un priceboard', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Dupliquer un priceboard', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                ]
            ],

            4 => [ 'name' => 'Tag', 'branch' => null,
                'permissions' => [
                    [ 'name' => 'Créer un tag', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Editer un tag', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Supprimer un tag', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'name' => 'Dupliquer un tag', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                ]
            ],

        ];

        //$permissions = [];

        foreach ($features as $index => $appFeature)
        {

            $feature = new Feature();

            $feature->setName(ucfirst($appFeature['name']))
                ->setBranch(ucfirst($appFeature['branch']));

            foreach ($appFeature['permissions'] as $featurePermission)
            {

                $permission = new Permission();
                $permission->setName($featurePermission['name']);

                foreach ($featurePermission['roles'] as $role)
                {
                    if($role === null)
                        throw new \Exception(sprintf("Internal Error : attempt to add role on permission but role is null ! Check this feature roles : '%' ", $appFeature['name']));

                    // $permission->addRole() will call $role->addPermission()
                    $permission->addRole($role);
                }

                $feature->addPermission($permission);

                $manager->persist($feature);
                $manager->persist($permission);

            }


        }

    }

    private function loadTimeZones(\Doctrine\Persistence\ObjectManager &$manager) {

        $timezones = [
            "Europe/Amsterdam",	"Europe/Andorra",	"Europe/Astrakhan",	"Europe/Athens", "Europe/Belgrade",	"Europe/Berlin",	"Europe/Bratislava",	"Europe/Brussels",
            "Europe/Bucharest",	"Europe/Budapest",	"Europe/Busingen",	"Europe/Chisinau", "Europe/Copenhagen",	"Europe/Dublin",	"Europe/Gibraltar",	"Europe/Guernsey",
            "Europe/Helsinki",	"Europe/Isle_of_Man",	"Europe/Istanbul",	"Europe/Jersey", "Europe/Kaliningrad",	"Europe/Kiev",	"Europe/Kirov",	"Europe/Lisbon",
            "Europe/Ljubljana",	"Europe/London",	"Europe/Luxembourg",	"Europe/Madrid", "Europe/Malta",	"Europe/Mariehamn",	"Europe/Minsk",	"Europe/Monaco",
            "Europe/Moscow",	"Europe/Oslo",	"Europe/Paris",	"Europe/Podgorica", "Europe/Prague",	"Europe/Riga",	"Europe/Rome",	"Europe/Samara", "Europe/San_Marino",
            "Europe/Sarajevo",	"Europe/Saratov",	"Europe/Simferopol", "Europe/Skopje",	"Europe/Sofia",	"Europe/Stockholm",	"Europe/Tallinn", "Europe/Tirane",
            "Europe/Ulyanovsk",	"Europe/Uzhgorod",	"Europe/Vaduz", "Europe/Vatican",	"Europe/Vienna",	"Europe/Vilnius",	"Europe/Volgograd", "Europe/Warsaw",
            "Europe/Zagreb",	"Europe/Zaporozhye",	"Europe/Zurich"
        ];


        foreach ($timezones as $timezoneName) {

            $timezone = new TimeZone();
            $timezone->setName($timezoneName);

            $manager->persist($timezone);

        }

    }


    private function loadCountries(\Doctrine\Persistence\ObjectManager &$manager) {

        $countriesNames = [
            'Algérie', 'Åland', 'Afghanistan', 'Albanie', 'Samoa américaines', 'Andorre', 'Angola', 'Anguilla', 'Antarctique',
            'Antigua-et-Barbuda', 'Argentine', 'Arménie', 'Aruba', 'Australie', 'Autriche', 'Azerbaïdjan', 'Bahamas', 'Bahreïn',
            'Bangladesh', 'Barbade', 'Biélorussie', 'Belgique', 'Belize', 'Bénin', 'Bermudes', 'Bhoutan', 'Bolivie', 'Bonaire, Saint-Eustache et Saba',
            'Bosnie-Herzégovine', 'Botswana', 'Île Bouvet', 'Brésil', 'Territoire britannique de l\'océan Indien', 'Îles mineures éloignées des États-Unis',
            'Îles Vierges britanniques', 'Îles Vierges des États-Unis', 'Brunei', 'Bulgarie', 'Burkina Faso', 'Burundi', 'Cambodge', 'Cameroun', 'Canada',
            'Cap Vert', 'Îles Caïmans', 'République centrafricaine', 'Tchad', 'Chili', 'Chine', 'Île Christmas', 'Îles Cocos', 'Colombie', 'Comores', 'Congo',
            'Congo (Rép. dém.)', 'Îles Cook', 'Costa Rica', 'Croatie', 'Cuba', 'Curaçao', 'Chypre', 'République tchèque', 'Danemark', 'Djibouti', 'Dominique',
            'République dominicaine', 'Équateur', 'Égypte', 'Salvador', 'Guinée-Équatoriale', 'Érythrée', 'Estonie', 'Éthiopie', 'Îles Malouines', 'Îles Féroé',
            'Fidji', 'Finlande', 'France', 'Guayane', 'Polynésie française', 'Terres australes et antarctiques françaises', 'Gabon', 'Gambie', 'Géorgie', 'Allemagne',
            'Ghana', 'Gibraltar', 'Grèce', 'Groenland', 'Grenade', 'Guadeloupe', 'Guam', 'Guatemala', 'Guernesey', 'Guinée', 'Guinée-Bissau', 'Guyane', 'Haïti',
            'Îles Heard-et-MacDonald', 'Saint-Voir', 'Honduras', 'Hong Kong', 'Hongrie', 'Islande', 'Inde', 'Indonésie', 'Côte d\'Ivoire', 'Iran', 'Irak', 'Irlande',
            'Île de Man', 'Israël', 'Italie', 'Jamaïque', 'Japon', 'Jersey', 'Jordanie', 'Kazakhstan', 'Kenya', 'Kiribati', 'Koweït', 'Kirghizistan', 'Laos',
            'Lettonie', 'Liban', 'Lesotho', 'Liberia', 'Libye', 'Liechtenstein', 'Lituanie', 'Luxembourg', 'Macao', 'Macédoine', 'Madagascar', 'Malawi', 'Malaisie',
            'Maldives', 'Mali', 'Malte', 'Îles Marshall', 'Martinique', 'Mauritanie', 'Île Maurice', 'Mayotte', 'Mexique', 'Micronésie', 'Moldavie', 'Monaco', 'Mongolie',
            'Monténégro', 'Montserrat', 'Maroc', 'Mozambique', 'Myanmar', 'Namibie', 'Nauru', 'Népal', 'Pays-Bas', 'Nouvelle-Calédonie', 'Nouvelle-Zélande', 'Nicaragua',
            'Niger', 'Nigéria', 'Niue', 'Île de Norfolk', 'Corée du Nord', 'Îles Mariannes du Nord', 'Norvège', 'Oman', 'Pakistan', 'Palaos', 'Palestine', 'Panama',
            'Papouasie-Nouvelle-Guinée', 'Paraguay', 'Pérou', 'Philippines', 'Îles Pitcairn', 'Pologne', 'Portugal', 'Porto Rico', 'Qatar', 'République du Kosovo', 'Réunion',
            'Roumanie', 'Russie', 'Rwanda', 'Saint-Barthélemy', 'Sainte-Hélène', 'Saint-Christophe-et-Niévès', 'Saint-Lucie', 'Saint-Martin', 'Saint-Pierre-et-Miquelon',
            'Saint-Vincent-et-les-Grenadines', 'Samoa', 'Saint-Marin', 'Sao Tomé-et-Principe', 'Arabie Saoudite', 'Sénégal', 'Serbie', 'Seychelles', 'Sierra Leone', 'Singapour',
            'Saint Martin (partie néerlandaise)', 'Slovaquie', 'Slovénie', 'Îles Salomon', 'Somalie', 'Afrique du Sud', 'Géorgie du Sud-et-les Îles Sandwich du Sud', 'Corée du Sud',
            'Soudan du Sud', 'Espagne', 'Sri Lanka', 'Soudan', 'Surinam', 'Svalbard et Jan Mayen', 'Swaziland', 'Suède', 'Suisse', 'Syrie', 'Taïwan', 'Tadjikistan', 'Tanzanie',
            'Thaïlande', 'Timor oriental', 'Togo', 'Tokelau', 'Tonga', 'Trinité et Tobago', 'Tunisie', 'Turquie', 'Turkménistan', 'Îles Turques-et-Caïques', 'Tuvalu', 'Uganda',
            'Ukraine', 'Émirats arabes unis', 'Royaume-Uni', 'États-Unis', 'Uruguay', 'Ouzbékistan', 'Vanuatu', 'Venezuela', 'Viêt Nam', 'Wallis-et-Futuna', 'Sahara Occidental',
            'Yémen','Zambie','Zimbabwe'];


        foreach ($countriesNames as $countryName)
        {

            $country = new Country();
            $country->setName($countryName);

            $manager->persist($country);

        }

    }


    private function loadCustomers(\Doctrine\Persistence\ObjectManager &$manager)
    {

        $country = $this->getPersistedEntity($manager, ['className' => Country::class, 'property' => 'name', 'value' => 'France']);
        $timeZone = $this->getPersistedEntity($manager, ['className' => TimeZone::class, 'property' => 'name', 'value' => 'Europe/Paris']);

        for ($i = 1; $i <= 5; $i++)
        {

            $customer = new Customer();
            $customer->setName( ($i === 1) ? "Kfc" : $this->__faker->company )
                ->setAddress($this->__faker->address)
                ->setPostalCode($this->__faker->postcode)
                ->setCity($this->__faker->city)
                ->setPhoneNumber($this->__faker->phoneNumber)
                ->setDescription($this->__faker->text())
                ->setCountry($country)
                ->setLogo("logo_" . $i)
                ->setTimezone($timeZone);

            $manager->persist($customer);

        }

    }


    private function loadUsers(\Doctrine\Persistence\ObjectManager &$manager)
    {

        //$tokenGeneratorService = new TokenGeneratorService();

        $customer = $this->getPersistedEntity($manager, ['className' => Customer::class, 'property' => 'name', 'value' => 'Kfc']);

        $perimeter = new Perimeter();
        $perimeter->setName("Permiter1")
                  ->setLevel(1);

        /*$role = new Role();
        $role->setName("Role1 ")
            ->setLevel(1);*/

        $user = new User();

        $user->setPerimeter($perimeter)
            ->setActivated(0)
            ->setFirstName('User1')
            ->setLastName('toto')
            ->setPassword($this->__encoder->encodePassword(
                $user,
                'totoRtyu3$'
            ))
            ->setPhoneNumber('0143256232')
            ->setActivated(0)
            ->setEmail('cbaby@infoway.fr');

        $customer->addUser($user);

        //$manager->persist($role);
        $manager->persist($user);
        $manager->persist($perimeter);

    }


    /**
     * @param ObjectManager $manager reference of ObjectManager
     * @param array $criteria used for search entity
     * @param array $options
     * @return array|object
     * @throws \Exception
     */
    private function getPersistedEntity(\Doctrine\Persistence\ObjectManager &$manager, array $criteria = [ 'className' => null, 'property' => null, 'value' => null ], array $options = [ 'maxResult' => 1, 'mode_strict' => false ])
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
