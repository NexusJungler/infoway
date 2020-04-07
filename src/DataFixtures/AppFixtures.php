<?php

namespace App\DataFixtures;



use App\Entity\Admin\Action;
use App\Entity\Admin\Country;
use App\Entity\Admin\Customer;
use App\Entity\Admin\Feature;
use App\Entity\Admin\Permission;
use App\Entity\Admin\Role;
use App\Entity\Admin\Subject;
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

        $this->loadRoles($manager);
        $this->loadTimeZones($manager);
        $this->loadCountries($manager);
        $this->loadCustomers($manager);
        //$this->loadUsers($manager);
        //$this->loadPermissions($manager);

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


    // Backup, Don't remove this
    /*
    private function loadPermissions(\Doctrine\Persistence\ObjectManager &$manager)
    {

        $roleGod = $this->getPersistedEntity($manager, ['className' => Role::class, 'property' => 'name', 'value' => 'God']);
        $roleSuperAdmin = $this->getPersistedEntity($manager, ['className' => Role::class, 'property' => 'name', 'value' => 'Super Admin']);
        $roleAdmin = $this->getPersistedEntity($manager, ['className' => Role::class, 'property' => 'name', 'value' => 'Admin']);
        $roleUser = $this->getPersistedEntity($manager, ['className' => Role::class, 'property' => 'name', 'value' => 'User']);

        $features = [
            0 => [ 'name' => 'Template', 'branch' => null,
                'permissions' => [
                    [ 'action' => 'accéder', 'subject' => 'stage 1', 'roles' => [ $roleGod, $roleSuperAdmin ] ],
                    [ 'action' => 'accéder', 'subject' => 'stage 2', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin ] ],
                    [ 'action' => 'accéder', 'subject' => 'stage 3', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'accéder', 'subject' => 'template', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'créer', 'subject' => 'template', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'editer', 'subject' => 'template', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'supprimer', 'subject' => 'template', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'importer', 'subject' => 'template', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'dupliquer', 'subject' => 'template', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                ]
            ],

            1 => [ 'name' => 'Programmation', 'branch' => null,
                'permissions' => [
                    [ 'action' => 'afficher', 'subject' => 'Programmation', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'créer', 'subject' => 'Programmation', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'editer', 'subject' => 'Programmation', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'supprimer', 'subject' => 'Programmation', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'dupliquer', 'subject' => 'Programmation', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                ]
            ],

            2 => [ 'name' => 'Incruste', 'branch' => null,
                'permissions' => [
                    [ 'action' => 'afficher', 'subject' => 'Incruste', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'créer', 'subject' => 'Incruste', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'editer', 'subject' => 'Incruste', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'supprimer', 'subject' => 'Incruste', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'importer', 'subject' => 'Incruste', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'dupliquer', 'subject' => 'Incruste', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                ]
            ],

            3 => [ 'name' => 'Priceboard', 'branch' => null,
                'permissions' => [
                    [ 'action' => 'accéder', 'subject' => 'Priceboard', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'créer', 'subject' => 'Priceboard', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'editer', 'subject' => 'Priceboard', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'supprimer', 'subject' => 'Priceboard', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'importer', 'subject' => 'Priceboard', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'dupliquer', 'subject' => 'Priceboard', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                ]
            ],

            4 => [ 'name' => 'Tag', 'branch' => null,
                'permissions' => [
                    [ 'action' => 'créer', 'subject' => 'Tag', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'editer', 'subject' => 'Tag', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'supprimer', 'subject' => 'Tag', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'dupliquer', 'subject' => 'Tag', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                ]
            ],

            5 => [ 'name' => 'Configuration', 'branch' => null,
                'permissions' => [
                    [ 'action' => 'accéder', 'subject' => 'Configuration', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'créer', 'subject' => 'Configuration', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'editer', 'subject' => 'Configuration', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'supprimer', 'subject' => 'Configuration', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
                    [ 'action' => 'dupliquer', 'subject' => 'Configuration', 'roles' => [ $roleGod, $roleSuperAdmin, $roleAdmin, $roleUser ] ],
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
                //$permission->setFeature($feature);

                $action = $this->getPersistedEntity($manager, ['className' => Action::class, 'property' => 'name', 'value' => ucfirst($featurePermission['action'])]);
                $action = $action ?? new Action();

                $subject = $this->getPersistedEntity($manager, ['className' => Subject::class, 'property' => 'name', 'value' => ucfirst($featurePermission['subject'])]);
                $subject = $subject ?? new Subject();

                $action->setName(ucfirst($featurePermission['action']));
                $subject->setName(ucfirst($featurePermission['subject']));


                foreach ($featurePermission['roles'] as $role)
                {
                    if($role === null)
                        throw new \Exception(sprintf("Internal Error : attempt to add role on permission but role is null ! Check this feature roles : '%' ", $appFeature['name']));

                    // $permission->addRole() will call $role->addPermission()
                    $permission->addRole($role);
                }


                // $action->addPermission will call permission->setAction(, so we don't need to use permission->setAction() here
                // $subject->addPermission will call permission->setSubject(, so we don't need to use permission->setAction() here
                $action->addPermission($permission);
                $subject->addPermission($permission);

                $feature->addPermission($permission);

                $manager->persist($action);
                $manager->persist($subject);
                $manager->persist($feature);
                $manager->persist($permission);

            }


        }

    }*/

    private function loadRoles(\Doctrine\Persistence\ObjectManager &$manager) {

        $rolesNames = [
            'God', 'Super Admin', 'Admin', 'User'
        ];

        foreach ($rolesNames as $roleName) {

            $role = new Role();
            $role->setName($roleName);

            $roles[] = $role;

            $manager->persist($role);

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
            $customer->setName( ($i === 1) ? "Quick" : $this->__faker->company )
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

        $tokenGeneratorService = new TokenGeneratorService();

        $customer = $this->getPersistedEntity($manager, ['className' => Customer::class, 'property' => 'name', 'value' => 'Quick']);

        for ($i = 1; $i <= 5; $i++)
        {

            // role = Admin (if $i is pair), User (if $i is odd)
            $role = ($i % 2 === 0) ? $this->getPersistedEntity($manager, ['className' => Role::class, 'property' => 'name', 'value' => 'Admin']) : $this->getPersistedEntity($manager, ['className' => Role::class, 'property' => 'name', 'value' => 'User']);

            $user = new User();
            $user->setFirstName($this->__faker->firstName)
                ->setLastName($this->__faker->lastName)
                ->setUsername($this->__faker->userName)
                ->setPassword($this->__encoder->encodePassword($user, $this->__faker->password))
                ->setEmail($this->__faker->email)
                ->setPhoneNumber($this->__faker->phoneNumber)
                ->setRegistrationToken($this->__encoder->encodePassword($user, $tokenGeneratorService->generate(15)))
                ->setPasswordResetToken(null)
                ->setRole($role)
                //->setCustomer($customer)
                ->setCompanyPiece(1);

            $customer->addUser($user);

            $manager->persist($user);

        }

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
