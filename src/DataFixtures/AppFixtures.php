<?php

namespace App\DataFixtures;


use App\Entity\Action;
use App\Entity\Country;
use App\Entity\Customer;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\Site;
use App\Entity\Stage;
use App\Entity\Subject;
use App\Entity\TimeZone;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface encoder for user password
     */
    private $encoder;


    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {

        //================= creating fake data generator instance start =================//

        // use the factory to create a Faker\Generator instance
        $faker= Factory::create('fr_FR');

        //================= creating fake data generator instance end =================//


        //===============    creation start     ===============//


        // create stages start
        /*$stage1 = new Stage();
        $stage1->setName("Stage 1");

        $stage2 = new Stage();
        $stage2->setName("Stage 2");

        $stage3 = new Stage();
        $stage3->setName("Stage 3");*/
        // create stages end


        // create users start
        $user = new User();
        $user->setLogin("infoway")
                ->setName("infoway")
                 ->setPassword($this->encoder->encodePassword($user, "infoway"))
                 ->setEmail("cbaby@infoway.fr")
                 ->setRegistrationDate(new \DateTime())
                 ->setRegistrationIsConfirmed(true)
                 ->setPhoneNumber($faker->phoneNumber);
        // create users end


        // create customer start

        $country = new Country();
        $country->setName($faker->unique()->country);

        for ($i = 1; $i <= 10; $i++)
        {

            $timeZone = new TimeZone();
            $timeZone->setName($faker->unique()->timezone);

            if($i === 1)
            {

                $site = new Site();
                $site->setName($faker->unique()->company)
                     ->setAddress($faker->unique()->address)
                     ->setPostalCode($faker->unique()->postcode)
                     ->setCity("Paris")
                     ->setCountry($country)
                     ->setPhoneNumber($faker->unique()->phoneNumber)
                     ->setDescription($faker->unique()->text(255))
                     ->setTimezone($timeZone);


                $customer = new Customer();
                $customer->setName("Infoway")
                         ->setAddress($faker->unique()->address)
                         ->setPostalCode($faker->unique()->postcode)
                         ->setCity("Paris")
                         ->setCountry($country)
                         ->setPhoneNumber($faker->unique()->phoneNumber)
                         ->setDescription($faker->unique()->text(255))
                         ->setTimezone($timeZone);

                $site->setCustomer($customer);

                $user->setCustomer($customer)
                     ->setSite($site);

                $customer->addUser($user)
                         ->addSite($site);
            }



            else
            {

                $site = new Site();
                $site->setName($faker->unique()->company)
                     ->setAddress($faker->unique()->address)
                     ->setPostalCode($faker->unique()->postcode)
                     ->setCity($faker->unique()->city)
                     ->setCountry($country)
                     ->setPhoneNumber($faker->unique()->phoneNumber)
                     ->setDescription($faker->unique()->text(255))
                     ->setTimezone($timeZone);

                $customer = new Customer();
                $customer->setName($faker->unique()->company)
                         ->setAddress($faker->unique()->address)
                         ->setPostalCode($faker->unique()->postcode)
                         ->setCity($faker->unique()->city)
                         ->setCountry($country)
                         ->setPhoneNumber($faker->unique()->phoneNumber)
                         ->setDescription($faker->unique()->text(255))
                         ->setTimezone($timeZone);

                $site->setCustomer($customer);

                $customer->addUser($user);
            }

            $manager->persist($customer);
            $manager->persist($country);
            $manager->persist($site);
            $manager->persist($timeZone);

        }

        // create customer end


        // create roles start
        $roleUser = new Role();
        $roleUser->setName("User");

        $roleAdmin = new Role();
        $roleAdmin->setName("Admin");

        $roleSuperAdmin = new Role();
        $roleSuperAdmin->setName("Super admin");

        $roleGod = new Role();
        $roleGod->setName("God");
        // create roles end


        // create actions start
        /*$actionShow = new Action();
        $actionShow->setName("Afficher");

        $actionCreate = new Action();
        $actionCreate->setName("Créer");

        $actionEdit = new Action();
        $actionEdit->setName("Editer");

        $actionDuplicate = new Action();
        $actionDuplicate->setName("Dupliquer");

        $actionDelete = new Action();
        $actionDelete->setName("Supprimer");

        $actionAccess = new Action();
        $actionAccess->setName("Accéder");

        $actionAttribute = new Action();
        $actionAttribute->setName("Attribuer");

        $actionAttributeMedia = new Action();
        $actionAttributeMedia->setName("Attribuer un media");

        $actionAttributePrice = new Action();
        $actionAttributePrice->setName("Attribuer un prix");

        $actionAttributeText = new Action();
        $actionAttributeText->setName("Attribuer un text");

        $actionRemove = new Action();
        $actionRemove->setName("Retirer");

        $actionOpen = new Action();
        $actionOpen->setName("Ouvrir");

        $actionSave = new Action();
        $actionSave->setName("Enregistrer");

        $actionExit = new Action();
        $actionExit->setName("Quitter");

        $actionMove = new Action();
        $actionMove->setName("Déplacer");

        $actionTransformZone = new Action();
        $actionTransformZone->setName("Transformer");

        $actionMoveForwardZone = new Action();
        $actionMoveForwardZone->setName("Avancer une zone");

        $actionMoveBackZone = new Action();
        $actionMoveBackZone->setName("Reculer une zone");

        $actionPutZoneInForeground = new Action();
        $actionPutZoneInForeground->setName("Mettre au premier plan");

        $actionPutZoneInBackground = new Action();
        $actionPutZoneInBackground->setName("Mettre au dernier plan");

        $actionAssociate = new Action();
        $actionAssociate->setName("Associer");

        $actionUploadMedia = new Action();
        $actionUploadMedia->setName("Uploader un média");

        $actionLock= new Action();
        $actionLock->setName("Verrouiller");

        $actionUnlock = new Action();
        $actionUnlock->setName("Déverouiller");

        $actionAssociateProductToZone = new Action();
        $actionAssociateProductToZone->setName("Associer un produit");

        $actionAssociateCategoryToZone = new Action();
        $actionAssociateCategoryToZone->setName("Associer une catégorie");

        $actionOverwrite = new Action();
        $actionOverwrite->setName("Ecraser");

        $actionZoom = new Action();
        $actionZoom->setName("Agrandir");

        $actionZoomOut = new Action();
        $actionZoomOut->setName("Diminuer");

        $actionHide = new Action();
        $actionHide->setName("Masquer");
        // create actions end


        // create subjects start
        $subjectTemplate = new Subject();
        $subjectTemplate->setName("Template");

        $subjectTemplateModel = new Subject();
        $subjectTemplateModel->setName("Template modele");

        $subjectStage1 = new Subject();
        $subjectStage1->setName("Stage 1");

        $subjectStage2 = new Subject();
        $subjectStage2->setName("Stage 2");

        $subjectStage3 = new Subject();
        $subjectStage3->setName("Stage 3");

        $subjectRole = new Subject();
        $subjectRole->setName("Role");

        $subjectUserPermission = new Subject();
        $subjectUserPermission->setName("Permissions");

        $subjectUser = new Subject();
        $subjectUser->setName("Utilisateurs");

        $subjectZone = new Subject();
        $subjectZone->setName("Zone");

        $subjectZoneText = new Subject();
        $subjectZoneText->setName("Zone texte");

        $subjectZonePrice = new Subject();
        $subjectZonePrice->setName("Zone prix");

        $subjectZoneMedia = new Subject();
        $subjectZoneMedia->setName("Zone média");

        $subjectBackground = new Subject();
        $subjectBackground->setName("Background");

        $subjectPropertiesOfZone = new Subject();
        $subjectPropertiesOfZone->setName("Proprietés d'une zone");

        $subjectStyleOfText = new Subject();
        $subjectStyleOfText->setName("Style de texte");

        $subjectClassOfIncruste = new Subject();
        $subjectClassOfIncruste->setName("Classe d'incruste");

        $subjectMedia = new Subject();
        $subjectMedia->setName("Media");
        // create subjects end


        // create permissions start
        $permissionShowTemplate = new Permission();
        $permissionCreateTemplate = new Permission();
        $permissionEditTemplate = new Permission();
        $permissionDuplicateTemplate = new Permission();
        $permissionDeleteTemplate = new Permission();
        $permissionAccessToStage1 = new Permission();
        $permissionAccessToStage2 = new Permission();
        $permissionAccessToStage3 = new Permission();
        $permissionAttributeRole = new Permission();
        $permissionAttributePermission = new Permission();
        $permissionRemovePermission = new Permission();
        $permissionDeleteUser = new Permission();
        $permissionCreateUser = new Permission();
        $permissionShowTemplateModel = new Permission();
        $permissionCreateTemplateModel = new Permission();
        $permissionEditTemplateModel = new Permission();
        $permissionDuplicateTemplateModel = new Permission();
        $permissionSaveTemplateModel = new Permission();
        $permissionOverwriteTemplateModel = new Permission();
        $permissionDeleteTemplateModel = new Permission();
        $permissionQuitTemplate = new Permission();
        $permissionSaveTemplate = new Permission();
        $permissionOverwriteTemplate = new Permission();
        $permissionQuitTemplateModel = new Permission();
        $permissionCreateTextZone = new Permission();
        $permissionCreateMediaZone = new Permission();
        $permissionCreatePriceZone = new Permission();
        $permissionMoveTextZone = new Permission();
        $permissionMoveMediaZone = new Permission();
        $permissionMovePriceZone = new Permission();
        $permissionDeleteTextZone = new Permission();
        $permissionDeleteMediaZone = new Permission();
        $permissionDeletePriceZone = new Permission();
        $permissionDuplicateTextZone = new Permission();
        $permissionDuplicateMediaZone = new Permission();
        $permissionDuplicatePriceZone = new Permission();
        $permissionTransformTextZone = new Permission();
        $permissionTransformMediaZone = new Permission();
        $permissionTransformPriceZone = new Permission();
        $permissionMoveForwardTextZone = new Permission();
        $permissionMoveForwardMediaZone = new Permission();
        $permissionMoveForwardPriceZone = new Permission();
        $permissionMoveBackTextZone = new Permission();
        $permissionMoveBackMediaZone = new Permission();
        $permissionMoveBackPriceZone = new Permission();
        $permissionPutTextZoneInForeground = new Permission();
        $permissionPutMediaZoneInForeground = new Permission();
        $permissionPutPriceZoneInForeground = new Permission();
        $permissionPutTextZoneInBackground = new Permission();
        $permissionPutMediaZoneInBackground = new Permission();
        $permissionPutPriceZoneInBackground = new Permission();
        $permissionZoomOnTemplate = new Permission();
        $permissionZoomOnTemplateModel = new Permission();
        $permissionZoomOutTemplate = new Permission();
        $permissionZoomOutTemplateModel = new Permission();
        $permissionHideZone = new Permission();
        $permissionAssociateTextZone = new Permission();
        $permissionAssociateMediaZone = new Permission();
        $permissionAssociatePriceZone = new Permission();
        $permissionAssociateCategoryToTextZone = new Permission();
        $permissionAssociateCategoryToMediaZone = new Permission();
        $permissionAssociateCategoryToPriceZone = new Permission();
        $permissionAssociateProductToTextZone = new Permission();
        $permissionAssociateProductToMediaZone = new Permission();
        $permissionAssociateProductToPriceZone = new Permission();
        $permissionLockTextZone = new Permission();
        $permissionLockMediaZone = new Permission();
        $permissionLockPriceZone = new Permission();
        $permissionUnlockTextZone = new Permission();
        $permissionUnlockMediaZone = new Permission();
        $permissionUnlockPriceZone = new Permission();
        $permissionAttributeBackgroundToTextZone = new Permission();
        $permissionAttributeBackgroundToMediaZone = new Permission();
        $permissionAttributeBackgroundToPriceZone = new Permission();
        $permissionCreateBackground = new Permission();
        $permissionAttributeTextToTextZone = new Permission();
        $permissionAttributeMediaToMediaZone = new Permission();
        $permissionAttributePriceToPriceZone = new Permission();
        $permissionCreateIncrusteClass = new Permission();
        $permissionAttributeIncrusteClass = new Permission();
        $permissionCreateTextStyle = new Permission();
        $permissionAttributeStyleToText = new Permission();
        $permissionEditTextZoneProperties = new Permission();
        $permissionEditMediaZoneProperties = new Permission();
        $permissionEditPriceZoneProperties = new Permission();
        $permissionImportTemplate = new Permission();
        $permissionUpload = new Permission();*/
        // create permissions end


        //===============    creation end     ===============//



        //===============    adding start     ===============//


        // adding roles to user start
        $user->setRole($roleGod);
        // adding roles to user end

        $roleGod->addUser($user);


        // adding permissions to actions start
        /*$actionShow->addPermission($permissionShowTemplate);

        $actionCreate->addPermission($permissionCreateBackground);
        $actionCreate->addPermission($permissionCreateIncrusteClass);
        $actionCreate->addPermission($permissionCreateMediaZone);
        $actionCreate->addPermission($permissionCreatePriceZone);
        $actionCreate->addPermission($permissionCreateTemplate);
        $actionCreate->addPermission($permissionCreateTemplateModel);
        $actionCreate->addPermission($permissionCreateTextStyle);
        $actionCreate->addPermission($permissionCreateTextZone);
        $actionCreate->addPermission($permissionCreateUser);

        $actionEdit->addPermission($permissionEditTemplateModel);
        $actionEdit->addPermission($permissionEditTemplate);
        $actionEdit->addPermission($permissionEditTextZoneProperties);

        $actionDuplicate->addPermission($permissionDuplicateMediaZone);
        $actionDuplicate->addPermission($permissionDuplicatePriceZone);
        $actionDuplicate->addPermission($permissionDuplicateTemplate);
        $actionDuplicate->addPermission($permissionDuplicateTextZone);

        $actionDelete->addPermission($permissionDeleteMediaZone);
        $actionDelete->addPermission($permissionDeletePriceZone);
        $actionDelete->addPermission($permissionDeleteTemplate);
        $actionDelete->addPermission($permissionDeleteTemplateModel);
        $actionDelete->addPermission($permissionDeleteTextZone);
        $actionDelete->addPermission($permissionDeleteUser);

        $actionAccess->addPermission($permissionAccessToStage1);
        $actionAccess->addPermission($permissionAccessToStage2);
        $actionAccess->addPermission($permissionAccessToStage3);

        $actionAttribute->addPermission($permissionAttributeBackgroundToMediaZone);
        $actionAttribute->addPermission($permissionAttributeBackgroundToPriceZone);
        $actionAttribute->addPermission($permissionAttributeBackgroundToTextZone);
        $actionAttribute->addPermission($permissionAttributeIncrusteClass);
        $actionAttribute->addPermission($permissionAttributePermission);
        $actionAttribute->addPermission($permissionAttributeRole);
        $actionAttribute->addPermission($permissionAttributeStyleToText);

        $actionAttributePrice->addPermission($permissionAttributePriceToPriceZone);

        $actionAttributeMedia->addPermission($permissionAttributeMediaToMediaZone);

        $actionAttributeText->addPermission($permissionAttributeTextToTextZone);

        $actionRemove->addPermission($permissionRemovePermission);

        $actionOpen->addPermission($permissionImportTemplate);

        $actionSave->addPermission($permissionSaveTemplate);
        $actionSave->addPermission($permissionSaveTemplateModel);

        $actionExit->addPermission($permissionQuitTemplate);
        $actionExit->addPermission($permissionQuitTemplateModel);

        $actionMove->addPermission($permissionMoveTextZone);
        $actionMove->addPermission($permissionMoveMediaZone);
        $actionMove->addPermission($permissionMovePriceZone);

        $actionTransformZone->addPermission($permissionTransformMediaZone);
        $actionTransformZone->addPermission($permissionTransformPriceZone);
        $actionTransformZone->addPermission($permissionTransformTextZone);

        $actionMoveForwardZone->addPermission($permissionMoveForwardMediaZone);
        $actionMoveForwardZone->addPermission($permissionMoveForwardPriceZone);
        $actionMoveForwardZone->addPermission($permissionMoveForwardTextZone);

        $actionMoveBackZone->addPermission($permissionMoveBackMediaZone);
        $actionMoveBackZone->addPermission($permissionMoveBackPriceZone);
        $actionMoveBackZone->addPermission($permissionMoveBackTextZone);

        $actionPutZoneInForeground->addPermission($permissionPutMediaZoneInForeground);
        $actionPutZoneInForeground->addPermission($permissionPutPriceZoneInForeground);
        $actionPutZoneInForeground->addPermission($permissionPutTextZoneInForeground);

        $actionPutZoneInBackground->addPermission($permissionPutMediaZoneInBackground);
        $actionPutZoneInBackground->addPermission($permissionPutPriceZoneInBackground);
        $actionPutZoneInBackground->addPermission($permissionPutTextZoneInBackground);

        $actionAssociate->addPermission($permissionAssociateCategoryToMediaZone);
        $actionAssociate->addPermission($permissionAssociateCategoryToPriceZone);
        $actionAssociate->addPermission($permissionAssociateCategoryToTextZone);
        $actionAssociate->addPermission($permissionAssociateMediaZone);
        $actionAssociate->addPermission($permissionAssociatePriceZone);
        $actionAssociate->addPermission($permissionAssociateTextZone);
        $actionAssociate->addPermission($permissionAssociateProductToMediaZone);
        $actionAssociate->addPermission($permissionAssociateProductToPriceZone);
        $actionAssociate->addPermission($permissionAssociateProductToTextZone);

        $actionUploadMedia->addPermission($permissionUpload);

        $actionLock->addPermission($permissionUnlockTextZone);
        $actionLock->addPermission($permissionUnlockMediaZone);
        $actionLock->addPermission($permissionUnlockPriceZone);

        $actionAssociateProductToZone->addPermission($permissionAssociateProductToTextZone);
        $actionAssociateProductToZone->addPermission($permissionAssociateProductToMediaZone);
        $actionAssociateProductToZone->addPermission($permissionAssociateProductToPriceZone);

        $actionAssociateCategoryToZone->addPermission($permissionAssociateCategoryToTextZone);
        $actionAssociateCategoryToZone->addPermission($permissionAssociateCategoryToMediaZone);
        $actionAssociateCategoryToZone->addPermission($permissionAssociateCategoryToPriceZone);

        $actionOverwrite->addPermission($permissionOverwriteTemplate);
        $actionOverwrite->addPermission($permissionOverwriteTemplateModel);

        $actionZoom->addPermission($permissionZoomOnTemplate);
        $actionZoom->addPermission($permissionZoomOnTemplateModel);

        $actionZoomOut->addPermission($permissionZoomOutTemplate);
        $actionZoomOut->addPermission($permissionZoomOutTemplateModel);

        $actionHide->addPermission($permissionHideZone);
        // adding permissions to actions end



        // adding permissions to subjects start
        $subjectTemplate->addPermission($permissionShowTemplate);
        $subjectTemplate->addPermission($permissionCreateTemplate);
        $subjectTemplate->addPermission($permissionEditTemplate);
        $subjectTemplate->addPermission($permissionDuplicateTemplate);
        $subjectTemplate->addPermission($permissionDeleteTemplate);
        $subjectTemplate->addPermission($permissionQuitTemplate);
        $subjectTemplate->addPermission($permissionSaveTemplate);
        $subjectTemplate->addPermission($permissionOverwriteTemplate);
        $subjectTemplate->addPermission($permissionImportTemplate);

        $subjectTemplateModel->addPermission($permissionShowTemplateModel);
        $subjectTemplateModel->addPermission($permissionCreateTemplateModel);
        $subjectTemplateModel->addPermission($permissionEditTemplateModel);
        $subjectTemplateModel->addPermission($permissionDuplicateTemplateModel);
        $subjectTemplateModel->addPermission($permissionDeleteTemplateModel);
        $subjectTemplateModel->addPermission($permissionQuitTemplateModel);
        $subjectTemplateModel->addPermission($permissionSaveTemplateModel);
        $subjectTemplateModel->addPermission($permissionOverwriteTemplateModel);


        $subjectStage1->addPermission($permissionAccessToStage1);

        $subjectStage2->addPermission($permissionAccessToStage2);

        $subjectStage3->addPermission($permissionAccessToStage3);

        $subjectRole->addPermission($permissionAttributeRole);

        $subjectUserPermission->addPermission($permissionAttributePermission);
        $subjectUserPermission->addPermission($permissionRemovePermission);

        $subjectUser->addPermission($permissionCreateUser);
        $subjectUser->addPermission($permissionDeleteUser);

        $subjectZoneText->addPermission($permissionCreateTextZone);
        $subjectZoneText->addPermission($permissionMoveTextZone);
        $subjectZoneText->addPermission($permissionDeleteTextZone);
        $subjectZoneText->addPermission($permissionDuplicateTextZone);
        $subjectZoneText->addPermission($permissionTransformTextZone);
        $subjectZoneText->addPermission($permissionMoveForwardTextZone);
        $subjectZoneText->addPermission($permissionMoveBackTextZone);
        $subjectZoneText->addPermission($permissionPutTextZoneInForeground);
        $subjectZoneText->addPermission($permissionPutTextZoneInBackground);
        $subjectZoneText->addPermission($permissionAssociateTextZone);
        $subjectZoneText->addPermission($permissionAssociateCategoryToTextZone);
        $subjectZoneText->addPermission($permissionAssociateProductToTextZone);
        $subjectZoneText->addPermission($permissionLockTextZone);
        $subjectZoneText->addPermission($permissionUnlockTextZone);
        $subjectZoneText->addPermission($permissionAttributeBackgroundToTextZone);
        $subjectZoneText->addPermission($permissionAttributeTextToTextZone);
        $subjectZoneText->addPermission($permissionEditTextZoneProperties);


        $subjectZonePrice->addPermission($permissionCreatePriceZone);
        $subjectZonePrice->addPermission($permissionMovePriceZone);
        $subjectZonePrice->addPermission($permissionDeletePriceZone);
        $subjectZonePrice->addPermission($permissionDuplicatePriceZone);
        $subjectZonePrice->addPermission($permissionTransformPriceZone);
        $subjectZonePrice->addPermission($permissionMoveForwardPriceZone);
        $subjectZonePrice->addPermission($permissionMoveBackPriceZone);
        $subjectZonePrice->addPermission($permissionPutPriceZoneInForeground);
        $subjectZonePrice->addPermission($permissionPutPriceZoneInBackground);
        $subjectZonePrice->addPermission($permissionAssociatePriceZone);
        $subjectZonePrice->addPermission($permissionAssociateCategoryToPriceZone);
        $subjectZonePrice->addPermission($permissionAssociateProductToPriceZone);
        $subjectZonePrice->addPermission($permissionLockPriceZone);
        $subjectZonePrice->addPermission($permissionUnlockPriceZone);
        $subjectZonePrice->addPermission($permissionAttributeBackgroundToPriceZone);
        $subjectZonePrice->addPermission($permissionAttributePriceToPriceZone);
        $subjectZonePrice->addPermission($permissionEditPriceZoneProperties);


        $subjectZoneMedia->addPermission($permissionCreateMediaZone);
        $subjectZoneMedia->addPermission($permissionMoveMediaZone);
        $subjectZoneMedia->addPermission($permissionDeleteMediaZone);
        $subjectZoneMedia->addPermission($permissionDuplicateMediaZone);
        $subjectZoneMedia->addPermission($permissionTransformMediaZone);
        $subjectZoneMedia->addPermission($permissionMoveForwardMediaZone);
        $subjectZoneMedia->addPermission($permissionMoveBackMediaZone);
        $subjectZoneMedia->addPermission($permissionPutMediaZoneInForeground);
        $subjectZoneMedia->addPermission($permissionPutMediaZoneInBackground);
        $subjectZoneMedia->addPermission($permissionAssociateMediaZone);
        $subjectZoneMedia->addPermission($permissionAssociateCategoryToMediaZone);
        $subjectZoneMedia->addPermission($permissionAssociateProductToMediaZone);
        $subjectZoneMedia->addPermission($permissionLockMediaZone);
        $subjectZoneMedia->addPermission($permissionUnlockMediaZone);
        $subjectZoneMedia->addPermission($permissionAttributeBackgroundToMediaZone);
        $subjectZoneMedia->addPermission($permissionAttributeMediaToMediaZone);
        $subjectZoneMedia->addPermission($permissionEditMediaZoneProperties);


        $subjectBackground->addPermission($permissionCreateBackground);
        $subjectBackground->addPermission($permissionAttributeBackgroundToTextZone);
        $subjectBackground->addPermission($permissionAttributeBackgroundToMediaZone);
        $subjectBackground->addPermission($permissionAttributeBackgroundToPriceZone);

        $subjectPropertiesOfZone->addPermission($permissionEditTextZoneProperties);

        $subjectStyleOfText->addPermission($permissionCreateTextStyle);
        $subjectStyleOfText->addPermission($permissionAttributeStyleToText);

        $subjectClassOfIncruste->addPermission($permissionCreateIncrusteClass);
        $subjectClassOfIncruste->addPermission($permissionAttributeIncrusteClass);

        $subjectMedia->addPermission($permissionUpload);
        // adding permissions to subjects end


        // adding permissions to roles start
        $roleSuperAdmin->addPermission($permissionShowTemplate);
        $roleSuperAdmin->addPermission($permissionCreateTemplate);
        $roleSuperAdmin->addPermission($permissionEditTemplate);
        $roleSuperAdmin->addPermission($permissionDuplicateTemplate);
        $roleSuperAdmin->addPermission($permissionDeleteTemplate);
        $roleSuperAdmin->addPermission($permissionAccessToStage1);
        $roleSuperAdmin->addPermission($permissionAccessToStage2);
        $roleSuperAdmin->addPermission($permissionAccessToStage3);
        $roleSuperAdmin->addPermission($permissionAttributeRole);
        $roleSuperAdmin->addPermission($permissionAttributePermission);
        $roleSuperAdmin->addPermission($permissionRemovePermission);
        $roleSuperAdmin->addPermission($permissionDeleteUser);
        $roleSuperAdmin->addPermission($permissionCreateUser);
        $roleSuperAdmin->addPermission($permissionShowTemplateModel);
        $roleSuperAdmin->addPermission($permissionCreateTemplateModel);
        $roleSuperAdmin->addPermission($permissionEditTemplateModel);
        $roleSuperAdmin->addPermission($permissionDuplicateTemplateModel);
        $roleSuperAdmin->addPermission($permissionSaveTemplateModel);
        $roleSuperAdmin->addPermission($permissionOverwriteTemplateModel);
        $roleSuperAdmin->addPermission($permissionDeleteTemplateModel);
        $roleSuperAdmin->addPermission($permissionQuitTemplate);
        $roleSuperAdmin->addPermission($permissionSaveTemplate);
        $roleSuperAdmin->addPermission($permissionOverwriteTemplate);
        $roleSuperAdmin->addPermission($permissionQuitTemplateModel);
        $roleSuperAdmin->addPermission($permissionCreateTextZone);
        $roleSuperAdmin->addPermission($permissionCreateMediaZone);
        $roleSuperAdmin->addPermission($permissionCreatePriceZone);
        $roleSuperAdmin->addPermission($permissionMoveTextZone);
        $roleSuperAdmin->addPermission($permissionMoveMediaZone);
        $roleSuperAdmin->addPermission($permissionMovePriceZone);
        $roleSuperAdmin->addPermission($permissionDeleteTextZone);
        $roleSuperAdmin->addPermission($permissionDeleteMediaZone);
        $roleSuperAdmin->addPermission($permissionDeletePriceZone);
        $roleSuperAdmin->addPermission($permissionDuplicateTextZone);
        $roleSuperAdmin->addPermission($permissionDuplicateMediaZone);
        $roleSuperAdmin->addPermission($permissionDuplicatePriceZone);
        $roleSuperAdmin->addPermission($permissionTransformTextZone);
        $roleSuperAdmin->addPermission($permissionTransformMediaZone);
        $roleSuperAdmin->addPermission($permissionTransformPriceZone);
        $roleSuperAdmin->addPermission($permissionMoveForwardTextZone);
        $roleSuperAdmin->addPermission($permissionMoveForwardMediaZone);
        $roleSuperAdmin->addPermission($permissionMoveForwardPriceZone);
        $roleSuperAdmin->addPermission($permissionMoveBackTextZone);
        $roleSuperAdmin->addPermission($permissionMoveBackMediaZone);
        $roleSuperAdmin->addPermission($permissionMoveBackPriceZone);
        $roleSuperAdmin->addPermission($permissionPutTextZoneInForeground);
        $roleSuperAdmin->addPermission($permissionPutMediaZoneInForeground);
        $roleSuperAdmin->addPermission($permissionPutPriceZoneInForeground);
        $roleSuperAdmin->addPermission($permissionPutTextZoneInBackground);
        $roleSuperAdmin->addPermission($permissionPutMediaZoneInBackground);
        $roleSuperAdmin->addPermission($permissionPutPriceZoneInBackground);
        $roleSuperAdmin->addPermission($permissionZoomOnTemplate);
        $roleSuperAdmin->addPermission($permissionZoomOnTemplateModel);
        $roleSuperAdmin->addPermission($permissionZoomOutTemplate);
        $roleSuperAdmin->addPermission($permissionZoomOutTemplateModel);
        $roleSuperAdmin->addPermission($permissionHideZone);
        $roleSuperAdmin->addPermission($permissionAssociateTextZone);
        $roleSuperAdmin->addPermission($permissionAssociateMediaZone);
        $roleSuperAdmin->addPermission($permissionAssociatePriceZone);
        $roleSuperAdmin->addPermission($permissionAssociateCategoryToTextZone);
        $roleSuperAdmin->addPermission($permissionAssociateCategoryToMediaZone);
        $roleSuperAdmin->addPermission($permissionAssociateCategoryToPriceZone);
        $roleSuperAdmin->addPermission($permissionAssociateProductToTextZone);
        $roleSuperAdmin->addPermission($permissionAssociateProductToMediaZone);
        $roleSuperAdmin->addPermission($permissionAssociateProductToPriceZone);
        $roleSuperAdmin->addPermission($permissionLockTextZone);
        $roleSuperAdmin->addPermission($permissionLockMediaZone);
        $roleSuperAdmin->addPermission($permissionLockPriceZone);
        $roleSuperAdmin->addPermission($permissionUnlockTextZone);
        $roleSuperAdmin->addPermission($permissionUnlockMediaZone);
        $roleSuperAdmin->addPermission($permissionUnlockPriceZone);
        $roleSuperAdmin->addPermission($permissionAttributeBackgroundToTextZone);
        $roleSuperAdmin->addPermission($permissionAttributeBackgroundToMediaZone);
        $roleSuperAdmin->addPermission($permissionAttributeBackgroundToPriceZone);
        $roleSuperAdmin->addPermission($permissionCreateBackground);
        $roleSuperAdmin->addPermission($permissionAttributeTextToTextZone);
        $roleSuperAdmin->addPermission($permissionAttributeMediaToMediaZone);
        $roleSuperAdmin->addPermission($permissionAttributePriceToPriceZone);
        $roleSuperAdmin->addPermission($permissionCreateIncrusteClass);
        $roleSuperAdmin->addPermission($permissionAttributeIncrusteClass);
        $roleSuperAdmin->addPermission($permissionCreateTextStyle);
        $roleSuperAdmin->addPermission($permissionAttributeStyleToText);
        $roleSuperAdmin->addPermission($permissionEditTextZoneProperties);
        $roleSuperAdmin->addPermission($permissionEditMediaZoneProperties);
        $roleSuperAdmin->addPermission($permissionEditPriceZoneProperties);
        $roleSuperAdmin->addPermission($permissionImportTemplate);
        $roleSuperAdmin->addPermission($permissionUpload);


        $roleAdmin->addPermission($permissionAccessToStage2);
        $roleAdmin->addPermission($permissionAccessToStage3);

        $roleUser->addPermission($permissionAccessToStage3);
        // adding permissions to roles end


        // adding stage to permission start
        $permissionShowTemplate->addStage($stage1);
        $permissionCreateTemplate->addStage($stage1);
        $permissionEditTemplate->addStage($stage1);
        $permissionDuplicateTemplate->addStage($stage1);
        $permissionDeleteTemplate->addStage($stage1);
        $permissionAccessToStage1->addStage($stage1);
        $permissionAccessToStage2->addStage($stage1);
        $permissionAccessToStage3->addStage($stage1);
        $permissionAttributeRole->addStage($stage1);
        $permissionAttributePermission->addStage($stage1);
        $permissionRemovePermission->addStage($stage1);
        $permissionDeleteUser->addStage($stage1);
        $permissionCreateUser->addStage($stage1);
        $permissionShowTemplateModel->addStage($stage1);
        $permissionCreateTemplateModel->addStage($stage1);
        $permissionEditTemplateModel->addStage($stage1);
        $permissionDuplicateTemplateModel->addStage($stage1);
        $permissionSaveTemplateModel->addStage($stage1);
        $permissionOverwriteTemplateModel->addStage($stage1);
        $permissionDeleteTemplateModel->addStage($stage1);
        $permissionQuitTemplate->addStage($stage1);
        $permissionSaveTemplate->addStage($stage1);
        $permissionOverwriteTemplate->addStage($stage1);
        $permissionQuitTemplateModel->addStage($stage1);
        $permissionCreateTextZone->addStage($stage1);
        $permissionCreateMediaZone->addStage($stage1);
        $permissionCreatePriceZone->addStage($stage1);
        $permissionMoveTextZone->addStage($stage1);
        $permissionMoveMediaZone->addStage($stage1);
        $permissionMovePriceZone->addStage($stage1);
        $permissionDeleteTextZone->addStage($stage1);
        $permissionDeleteMediaZone->addStage($stage1);
        $permissionDeletePriceZone->addStage($stage1);
        $permissionDuplicateTextZone->addStage($stage1);
        $permissionDuplicateMediaZone->addStage($stage1);
        $permissionDuplicatePriceZone->addStage($stage1);
        $permissionTransformTextZone->addStage($stage1);
        $permissionTransformMediaZone->addStage($stage1);
        $permissionTransformPriceZone->addStage($stage1);
        $permissionMoveForwardTextZone->addStage($stage1);
        $permissionMoveForwardMediaZone->addStage($stage1);
        $permissionMoveForwardPriceZone->addStage($stage1);
        $permissionMoveBackTextZone->addStage($stage1);
        $permissionMoveBackMediaZone->addStage($stage1);
        $permissionMoveBackPriceZone->addStage($stage1);
        $permissionPutTextZoneInForeground->addStage($stage1);
        $permissionPutMediaZoneInForeground->addStage($stage1);
        $permissionPutPriceZoneInForeground->addStage($stage1);
        $permissionPutTextZoneInBackground->addStage($stage1);
        $permissionPutMediaZoneInBackground->addStage($stage1);
        $permissionPutPriceZoneInBackground->addStage($stage1);
        $permissionZoomOnTemplate->addStage($stage1);
        $permissionZoomOnTemplateModel->addStage($stage1);
        $permissionZoomOutTemplate->addStage($stage1);
        $permissionZoomOutTemplateModel->addStage($stage1);
        $permissionHideZone->addStage($stage1);
        $permissionAssociateTextZone->addStage($stage1);
        $permissionAssociateMediaZone->addStage($stage1);
        $permissionAssociatePriceZone->addStage($stage1);
        $permissionAssociateCategoryToMediaZone->addStage($stage1);
        $permissionAssociateCategoryToTextZone->addStage($stage1);
        $permissionAssociateCategoryToPriceZone->addStage($stage1);
        $permissionAssociateProductToTextZone->addStage($stage1);
        $permissionAssociateProductToMediaZone->addStage($stage1);
        $permissionAssociateProductToPriceZone->addStage($stage1);
        $permissionLockTextZone->addStage($stage1);
        $permissionLockMediaZone->addStage($stage1);
        $permissionLockPriceZone->addStage($stage1);
        $permissionUnlockTextZone->addStage($stage1);
        $permissionUnlockMediaZone->addStage($stage1);
        $permissionUnlockPriceZone->addStage($stage1);
        $permissionAttributeBackgroundToTextZone->addStage($stage1);
        $permissionAttributeBackgroundToMediaZone->addStage($stage1);
        $permissionAttributeBackgroundToPriceZone->addStage($stage1);
        $permissionCreateBackground->addStage($stage1);
        $permissionAttributeTextToTextZone->addStage($stage1);
        $permissionAttributeMediaToMediaZone->addStage($stage1);
        $permissionAttributePriceToPriceZone->addStage($stage1);
        $permissionCreateIncrusteClass->addStage($stage1);
        $permissionAttributeIncrusteClass->addStage($stage1);
        $permissionCreateTextStyle->addStage($stage1);
        $permissionAttributeStyleToText->addStage($stage1);
        $permissionEditTextZoneProperties->addStage($stage1);
        $permissionEditMediaZoneProperties->addStage($stage1);
        $permissionEditPriceZoneProperties->addStage($stage1);
        $permissionImportTemplate->addStage($stage1);
        $permissionUpload->addStage($stage1);
        // adding stage to permission start


        // adding actions to permissions start
        $permissionShowTemplate->setAction($actionShow);
        $permissionCreateTemplate->setAction($actionCreate);
        $permissionEditTemplate->setAction($actionEdit);
        $permissionDuplicateTemplate->setAction($actionDuplicate);
        $permissionDeleteTemplate->setAction($actionDelete);
        $permissionAccessToStage1->setAction($actionAccess);
        $permissionAccessToStage2->setAction($actionAccess);
        $permissionAccessToStage3->setAction($actionAccess);
        $permissionAttributeRole->setAction($actionAttribute);
        $permissionAttributePermission->setAction($actionAttribute);
        $permissionRemovePermission->setAction($actionRemove);
        $permissionDeleteUser->setAction($actionDelete);
        $permissionCreateUser->setAction($actionCreate);
        $permissionShowTemplateModel->setAction($actionShow);
        $permissionCreateTemplateModel->setAction($actionCreate);
        $permissionEditTemplateModel->setAction($actionEdit);
        $permissionDuplicateTemplateModel->setAction($actionDuplicate);
        $permissionSaveTemplateModel->setAction($actionSave);
        $permissionOverwriteTemplateModel->setAction($actionOverwrite);
        $permissionDeleteTemplateModel->setAction($actionDelete);
        $permissionQuitTemplate->setAction($actionExit);
        $permissionSaveTemplate->setAction($actionSave);
        $permissionOverwriteTemplate->setAction($actionOverwrite);
        $permissionQuitTemplateModel->setAction($actionExit);
        $permissionCreateTextZone->setAction($actionCreate);
        $permissionCreateMediaZone->setAction($actionCreate);
        $permissionCreatePriceZone->setAction($actionCreate);
        $permissionMoveTextZone->setAction($actionMove);
        $permissionMoveMediaZone->setAction($actionMove);
        $permissionMovePriceZone->setAction($actionMove);
        $permissionDeleteTextZone->setAction($actionDelete);
        $permissionDeleteMediaZone->setAction($actionDelete);
        $permissionDeletePriceZone->setAction($actionDelete);
        $permissionDuplicateTextZone->setAction($actionDuplicate);
        $permissionDuplicateMediaZone->setAction($actionDuplicate);
        $permissionDuplicatePriceZone->setAction($actionDuplicate);
        $permissionTransformTextZone->setAction($actionTransformZone);
        $permissionTransformMediaZone->setAction($actionTransformZone);
        $permissionTransformPriceZone->setAction($actionTransformZone);
        $permissionMoveForwardTextZone->setAction($actionMoveForwardZone);
        $permissionMoveForwardMediaZone->setAction($actionMoveForwardZone);
        $permissionMoveForwardPriceZone->setAction($actionMoveForwardZone);
        $permissionMoveBackTextZone->setAction($actionMoveBackZone);
        $permissionMoveBackMediaZone->setAction($actionMoveBackZone);
        $permissionMoveBackPriceZone->setAction($actionMoveBackZone);
        $permissionPutTextZoneInForeground->setAction($actionPutZoneInForeground);
        $permissionPutMediaZoneInForeground->setAction($actionPutZoneInForeground);
        $permissionPutPriceZoneInForeground->setAction($actionPutZoneInForeground);
        $permissionPutTextZoneInBackground->setAction($actionPutZoneInBackground);
        $permissionPutMediaZoneInBackground->setAction($actionPutZoneInBackground);
        $permissionPutPriceZoneInBackground->setAction($actionPutZoneInBackground);
        $permissionZoomOnTemplate->setAction($actionZoom);
        $permissionZoomOnTemplateModel->setAction($actionZoom);
        $permissionZoomOutTemplate->setAction($actionZoomOut);
        $permissionZoomOutTemplateModel->setAction($actionZoomOut);
        $permissionHideZone->setAction($actionHide);
        $permissionAssociateTextZone->setAction($actionAssociate);
        $permissionAssociateMediaZone->setAction($actionAssociate);
        $permissionAssociatePriceZone->setAction($actionAssociate);
        $permissionAssociateCategoryToMediaZone->setAction($actionAssociateCategoryToZone);
        $permissionAssociateCategoryToTextZone->setAction($actionAssociateCategoryToZone);
        $permissionAssociateCategoryToPriceZone->setAction($actionAssociateCategoryToZone);
        $permissionAssociateProductToTextZone->setAction($actionAssociateProductToZone);
        $permissionAssociateProductToMediaZone->setAction($actionAssociateProductToZone);
        $permissionAssociateProductToPriceZone->setAction($actionAssociateProductToZone);
        $permissionLockTextZone->setAction($actionLock);
        $permissionLockMediaZone->setAction($actionLock);
        $permissionLockPriceZone->setAction($actionLock);
        $permissionUnlockTextZone->setAction($actionUnlock);
        $permissionUnlockMediaZone->setAction($actionUnlock);
        $permissionUnlockPriceZone->setAction($actionUnlock);
        $permissionAttributeBackgroundToTextZone->setAction($actionAttribute);
        $permissionAttributeBackgroundToMediaZone->setAction($actionAttribute);
        $permissionAttributeBackgroundToPriceZone->setAction($actionAttribute);
        $permissionCreateBackground->setAction($actionCreate);
        $permissionAttributeTextToTextZone->setAction($actionAttributeText);
        $permissionAttributeMediaToMediaZone->setAction($actionAttributeMedia);
        $permissionAttributePriceToPriceZone->setAction($actionAttributePrice);
        $permissionCreateIncrusteClass->setAction($actionCreate);
        $permissionAttributeIncrusteClass->setAction($actionAttribute);
        $permissionCreateTextStyle->setAction($actionCreate);
        $permissionAttributeStyleToText->setAction($actionAttribute);
        $permissionEditTextZoneProperties->setAction($actionEdit);
        $permissionEditMediaZoneProperties->setAction($actionEdit);
        $permissionEditPriceZoneProperties->setAction($actionEdit);
        $permissionImportTemplate->setAction($actionOpen);
        $permissionUpload->setAction($actionUploadMedia);
        // adding actions to permissions end


        // adding subjects to permissions start
        $permissionShowTemplate->setSubject($subjectTemplate);
        $permissionCreateTemplate->setSubject($subjectTemplate);
        $permissionEditTemplate->setSubject($subjectTemplate);
        $permissionDuplicateTemplate->setSubject($subjectTemplate);
        $permissionDeleteTemplate->setSubject($subjectTemplate);
        $permissionQuitTemplate->setSubject($subjectTemplate);
        $permissionSaveTemplate->setSubject($subjectTemplate);
        $permissionOverwriteTemplate->setSubject($subjectTemplate);

        $permissionAccessToStage1->setSubject($subjectStage1);
        $permissionAccessToStage2->setSubject($subjectStage2);
        $permissionAccessToStage3->setSubject($subjectStage3);

        $permissionAttributeRole->setSubject($subjectRole);

        $permissionAttributePermission->setSubject($subjectUserPermission);
        $permissionRemovePermission->setSubject($subjectUserPermission);

        $permissionDeleteUser->setSubject($subjectUser);
        $permissionCreateUser->setSubject($subjectUser);

        $permissionShowTemplateModel->setSubject($subjectTemplateModel);
        $permissionCreateTemplateModel->setSubject($subjectTemplateModel);
        $permissionEditTemplateModel->setSubject($subjectTemplateModel);
        $permissionDuplicateTemplateModel->setSubject($subjectTemplateModel);
        $permissionDeleteTemplateModel->setSubject($subjectTemplateModel);
        $permissionQuitTemplateModel->setSubject($subjectTemplateModel);
        $permissionSaveTemplateModel->setSubject($subjectTemplateModel);
        $permissionOverwriteTemplateModel->setSubject($subjectTemplateModel);



        $permissionMoveMediaZone->setSubject($subjectZoneMedia);
        $permissionDeleteMediaZone->setSubject($subjectZoneMedia);
        $permissionDuplicateMediaZone->setSubject($subjectZoneMedia);
        $permissionTransformMediaZone->setSubject($subjectZoneMedia);
        $permissionMoveBackMediaZone->setSubject($subjectZoneMedia);
        $permissionPutMediaZoneInBackground->setSubject($subjectZoneMedia);
        $permissionPutMediaZoneInForeground->setSubject($subjectZoneMedia);
        $permissionAssociateMediaZone->setSubject($subjectZoneMedia);
        $permissionCreateMediaZone->setSubject($subjectZoneMedia);
        $permissionAssociateCategoryToMediaZone->setSubject($subjectZoneMedia);
        $permissionAssociateProductToMediaZone->setSubject($subjectZoneMedia);
        $permissionLockMediaZone->setSubject($subjectZoneMedia);
        $permissionUnlockMediaZone->setSubject($subjectZoneMedia);
        $permissionMoveForwardMediaZone->setSubject($subjectZoneMedia);
        $permissionAttributeBackgroundToMediaZone->setSubject($subjectZoneMedia);
        $permissionAttributeMediaToMediaZone->setSubject($subjectZoneMedia);



        $permissionMoveTextZone->setSubject($subjectZoneText);
        $permissionCreateTextZone->setSubject($subjectZoneText);
        $permissionDeleteTextZone->setSubject($subjectZoneText);
        $permissionDuplicateTextZone->setSubject($subjectZoneText);
        $permissionTransformTextZone->setSubject($subjectZoneText);
        $permissionMoveForwardTextZone->setSubject($subjectZoneText);
        $permissionMoveBackTextZone->setSubject($subjectZoneText);
        $permissionAssociateTextZone->setSubject($subjectZoneText);
        $permissionAssociateCategoryToTextZone->setSubject($subjectZoneText);
        $permissionAssociateProductToTextZone->setSubject($subjectZoneText);
        $permissionLockTextZone->setSubject($subjectZoneText);
        $permissionAttributeBackgroundToTextZone->setSubject($subjectZoneText);
        $permissionAttributeTextToTextZone->setSubject($subjectZoneText);
        $permissionUnlockTextZone->setSubject($subjectZoneText);
        $permissionPutTextZoneInForeground->setSubject($subjectZoneText);
        $permissionPutTextZoneInBackground->setSubject($subjectZoneText);



        $permissionCreatePriceZone->setSubject($subjectZonePrice);
        $permissionMovePriceZone->setSubject($subjectZonePrice);
        $permissionDeletePriceZone->setSubject($subjectZonePrice);
        $permissionDuplicatePriceZone->setSubject($subjectZonePrice);
        $permissionTransformPriceZone->setSubject($subjectZonePrice);
        $permissionMoveForwardPriceZone->setSubject($subjectZonePrice);
        $permissionMoveBackPriceZone->setSubject($subjectZonePrice);
        $permissionAssociatePriceZone->setSubject($subjectZonePrice);
        $permissionAssociateCategoryToPriceZone->setSubject($subjectZonePrice);
        $permissionAssociateProductToPriceZone->setSubject($subjectZonePrice);
        $permissionLockPriceZone->setSubject($subjectZonePrice);
        $permissionPutPriceZoneInForeground->setSubject($subjectZonePrice);
        $permissionPutPriceZoneInBackground->setSubject($subjectZonePrice);
        $permissionUnlockPriceZone->setSubject($subjectZonePrice);
        $permissionAttributePriceToPriceZone->setSubject($subjectZonePrice);
        $permissionAttributeBackgroundToPriceZone->setSubject($subjectZonePrice);


        $permissionZoomOnTemplate->setSubject($subjectTemplate);
        $permissionZoomOnTemplateModel->setSubject($subjectTemplateModel);
        $permissionZoomOutTemplate->setSubject($subjectTemplate);
        $permissionZoomOutTemplateModel->setSubject($subjectTemplateModel);
        $permissionHideZone->setSubject($subjectZone);

        $permissionCreateBackground->setSubject($subjectBackground);

        $permissionCreateIncrusteClass->setSubject($subjectClassOfIncruste);
        $permissionAttributeIncrusteClass->setSubject($subjectClassOfIncruste);

        $permissionCreateTextStyle->setSubject($subjectStyleOfText);

        $permissionEditMediaZoneProperties->setSubject($subjectZoneMedia);
        $permissionEditTextZoneProperties->setSubject($subjectZoneText);
        $permissionEditPriceZoneProperties->setSubject($subjectZonePrice);

        $permissionUpload->setSubject($subjectMedia);
        // adding subjects to permissions end


        // adding roles to permissions start
        $permissionShowTemplate->addRole($roleSuperAdmin);
        $permissionCreateTemplate->addRole($roleSuperAdmin);
        $permissionEditTemplate->addRole($roleSuperAdmin);
        $permissionDuplicateTemplate->addRole($roleSuperAdmin);
        $permissionDeleteTemplate->addRole($roleSuperAdmin);
        $permissionAccessToStage1->addRole($roleSuperAdmin);
        $permissionAccessToStage2->addRole($roleSuperAdmin);
        $permissionAccessToStage2->addRole($roleAdmin);
        $permissionAccessToStage3->addRole($roleSuperAdmin);
        $permissionAccessToStage3->addRole($roleAdmin);
        $permissionAccessToStage3->addRole($roleUser);
        $permissionAttributeRole->addRole($roleSuperAdmin);
        $permissionAttributePermission->addRole($roleSuperAdmin);
        $permissionRemovePermission->addRole($roleSuperAdmin);
        $permissionDeleteUser->addRole($roleSuperAdmin);
        $permissionCreateUser->addRole($roleSuperAdmin);
        $permissionShowTemplateModel->addRole($roleSuperAdmin);
        $permissionCreateTemplateModel->addRole($roleSuperAdmin);
        $permissionEditTemplateModel->addRole($roleSuperAdmin);
        $permissionSaveTemplateModel->addRole($roleSuperAdmin);
        $permissionOverwriteTemplateModel->addRole($roleSuperAdmin);
        $permissionDeleteTemplateModel->addRole($roleSuperAdmin);
        $permissionQuitTemplate->addRole($roleSuperAdmin);
        $permissionSaveTemplate->addRole($roleSuperAdmin);
        $permissionOverwriteTemplate->addRole($roleSuperAdmin);
        $permissionQuitTemplateModel->addRole($roleSuperAdmin);
        $permissionCreateTextZone->addRole($roleSuperAdmin);
        $permissionCreateMediaZone->addRole($roleSuperAdmin);
        $permissionCreatePriceZone->addRole($roleSuperAdmin);
        $permissionMoveTextZone->addRole($roleSuperAdmin);
        $permissionMoveMediaZone->addRole($roleSuperAdmin);
        $permissionMovePriceZone->addRole($roleSuperAdmin);
        $permissionDeleteTextZone->addRole($roleSuperAdmin);
        $permissionDeleteMediaZone->addRole($roleSuperAdmin);
        $permissionDeletePriceZone->addRole($roleSuperAdmin);
        $permissionDuplicateTextZone->addRole($roleSuperAdmin);
        $permissionDuplicateMediaZone->addRole($roleSuperAdmin);
        $permissionDuplicatePriceZone->addRole($roleSuperAdmin);
        $permissionTransformTextZone->addRole($roleSuperAdmin);
        $permissionTransformMediaZone->addRole($roleSuperAdmin);
        $permissionTransformPriceZone->addRole($roleSuperAdmin);
        $permissionMoveForwardTextZone->addRole($roleSuperAdmin);
        $permissionMoveForwardMediaZone->addRole($roleSuperAdmin);
        $permissionMoveForwardPriceZone->addRole($roleSuperAdmin);
        $permissionMoveBackTextZone->addRole($roleSuperAdmin);
        $permissionMoveBackMediaZone->addRole($roleSuperAdmin);
        $permissionMoveBackPriceZone->addRole($roleSuperAdmin);
        $permissionPutTextZoneInForeground->addRole($roleSuperAdmin);
        $permissionPutMediaZoneInForeground->addRole($roleSuperAdmin);
        $permissionPutPriceZoneInForeground->addRole($roleSuperAdmin);
        $permissionPutTextZoneInBackground->addRole($roleSuperAdmin);
        $permissionPutMediaZoneInBackground->addRole($roleSuperAdmin);
        $permissionPutPriceZoneInBackground->addRole($roleSuperAdmin);
        $permissionZoomOnTemplate->addRole($roleSuperAdmin);
        $permissionZoomOnTemplateModel->addRole($roleSuperAdmin);
        $permissionZoomOutTemplate->addRole($roleSuperAdmin);
        $permissionZoomOutTemplateModel->addRole($roleSuperAdmin);
        $permissionHideZone->addRole($roleSuperAdmin);
        $permissionAssociateTextZone->addRole($roleSuperAdmin);
        $permissionAssociateMediaZone->addRole($roleSuperAdmin);
        $permissionAssociatePriceZone->addRole($roleSuperAdmin);
        $permissionAssociateCategoryToTextZone->addRole($roleSuperAdmin);
        $permissionAssociateCategoryToMediaZone->addRole($roleSuperAdmin);
        $permissionAssociateCategoryToPriceZone->addRole($roleSuperAdmin);
        $permissionAssociateProductToTextZone->addRole($roleSuperAdmin);
        $permissionAssociateProductToMediaZone->addRole($roleSuperAdmin);
        $permissionAssociateProductToPriceZone->addRole($roleSuperAdmin);
        $permissionLockTextZone->addRole($roleSuperAdmin);
        $permissionLockMediaZone->addRole($roleSuperAdmin);
        $permissionLockPriceZone->addRole($roleSuperAdmin);
        $permissionUnlockTextZone->addRole($roleSuperAdmin);
        $permissionUnlockMediaZone->addRole($roleSuperAdmin);
        $permissionUnlockPriceZone->addRole($roleSuperAdmin);
        $permissionAttributeBackgroundToTextZone->addRole($roleSuperAdmin);
        $permissionAttributeBackgroundToMediaZone->addRole($roleSuperAdmin);
        $permissionAttributeBackgroundToPriceZone->addRole($roleSuperAdmin);
        $permissionCreateBackground->addRole($roleSuperAdmin);
        $permissionAttributeTextToTextZone->addRole($roleSuperAdmin);
        $permissionAttributeMediaToMediaZone->addRole($roleSuperAdmin);
        $permissionAttributePriceToPriceZone->addRole($roleSuperAdmin);
        $permissionCreateIncrusteClass->addRole($roleSuperAdmin);
        $permissionAttributeIncrusteClass->addRole($roleSuperAdmin);
        $permissionCreateTextStyle->addRole($roleSuperAdmin);
        $permissionAttributeStyleToText->addRole($roleSuperAdmin);
        $permissionEditTextZoneProperties->addRole($roleSuperAdmin);
        $permissionEditMediaZoneProperties->addRole($roleSuperAdmin);
        $permissionEditPriceZoneProperties->addRole($roleSuperAdmin);
        $permissionUpload->addRole($roleSuperAdmin);
        // adding roles to permissions end


        // adding permissions to users start

        // adding permissions to users end


        // adding users to permissions start

        // adding users to permissions end


        // adding role to stage start
        $stage1->addRole($roleSuperAdmin);

        $stage2->addRole($roleSuperAdmin);
        $stage2->addRole($roleAdmin);

        $stage3->addRole($roleSuperAdmin);
        $stage3->addRole($roleAdmin);
        $stage3->addRole($roleUser);
        // adding role to stage end


        // adding stage to role start
        $roleSuperAdmin->addStage($stage1);
        $roleSuperAdmin->addStage($stage2);
        $roleSuperAdmin->addStage($stage3);

        $roleAdmin->addStage($stage2);
        $roleAdmin->addStage($stage3);

        $roleUser->addStage($stage3);
        // adding stage to role end*/

        //===============    adding end     ===============//



        //===============    entity persisting start     ===============//

        // persisting stage start
        /*$manager->persist($stage1);
        $manager->persist($stage2);
        $manager->persist($stage3);*/
        // persisting stage end


        // persisting users start
        $manager->persist($user);
        // persisting users end


        // persisting roles start
        $manager->persist($roleUser);
        $manager->persist($roleAdmin);
        $manager->persist($roleSuperAdmin);
        $manager->persist($roleGod);
        // persisting roles end


        // persisting actions start
        /*$manager->persist($actionShow);
        $manager->persist($actionCreate);
        $manager->persist($actionEdit);
        $manager->persist($actionDuplicate);
        $manager->persist($actionDelete);
        $manager->persist($actionAccess);
        $manager->persist($actionAttribute);
        $manager->persist($actionAttributePrice);
        $manager->persist($actionAttributeText);
        $manager->persist($actionAttributeMedia);
        $manager->persist($actionRemove);
        $manager->persist($actionOpen);
        $manager->persist($actionSave);
        $manager->persist($actionExit);
        $manager->persist($actionMove);
        $manager->persist($actionTransformZone);
        $manager->persist($actionMoveForwardZone);
        $manager->persist($actionMoveBackZone);
        $manager->persist($actionPutZoneInForeground);
        $manager->persist($actionPutZoneInBackground);
        $manager->persist($actionAssociate);
        $manager->persist($actionUploadMedia);
        $manager->persist($actionLock);
        $manager->persist($actionUnlock);
        $manager->persist($actionAssociateProductToZone);
        $manager->persist($actionAssociateCategoryToZone);
        $manager->persist($actionOverwrite);
        $manager->persist($actionZoom);
        $manager->persist($actionZoomOut);
        $manager->persist($actionHide);
        // persisting actions end


        // persisting subjects start
        $manager->persist($subjectTemplate);
        $manager->persist($subjectTemplateModel);
        $manager->persist($subjectStage1);
        $manager->persist($subjectStage2);
        $manager->persist($subjectStage3);
        $manager->persist($subjectRole);
        $manager->persist($subjectUserPermission);
        $manager->persist($subjectUser);
        $manager->persist($subjectZone);
        $manager->persist($subjectZoneText);
        $manager->persist($subjectZonePrice);
        $manager->persist($subjectZoneMedia);
        $manager->persist($subjectBackground);
        $manager->persist($subjectPropertiesOfZone);
        $manager->persist($subjectStyleOfText);
        $manager->persist($subjectClassOfIncruste);
        $manager->persist($subjectMedia);
        // persisting subjects end


        // persisting permissions start
        $manager->persist($permissionShowTemplate);
        $manager->persist($permissionCreateTemplate);
        $manager->persist($permissionEditTemplate);
        $manager->persist($permissionDuplicateTemplate);
        $manager->persist($permissionDeleteTemplate);
        $manager->persist($permissionAccessToStage1);
        $manager->persist($permissionAccessToStage2);
        $manager->persist($permissionAccessToStage3);
        $manager->persist($permissionAttributeRole);
        $manager->persist($permissionAttributePermission);
        $manager->persist($permissionRemovePermission);
        $manager->persist($permissionDeleteUser);
        $manager->persist($permissionCreateUser);
        $manager->persist($permissionShowTemplateModel);
        $manager->persist($permissionCreateTemplateModel);
        $manager->persist($permissionEditTemplateModel);
        $manager->persist($permissionDuplicateTemplateModel);
        $manager->persist($permissionSaveTemplateModel);
        $manager->persist($permissionOverwriteTemplateModel);
        $manager->persist($permissionDeleteTemplateModel);
        $manager->persist($permissionQuitTemplateModel);
        $manager->persist($permissionQuitTemplate);
        $manager->persist($permissionSaveTemplate);
        $manager->persist($permissionOverwriteTemplate);
        $manager->persist($permissionCreateTextZone);
        $manager->persist($permissionCreateMediaZone);
        $manager->persist($permissionCreatePriceZone);
        $manager->persist($permissionMoveTextZone);
        $manager->persist($permissionMoveMediaZone);
        $manager->persist($permissionMovePriceZone);
        $manager->persist($permissionDeleteTextZone);
        $manager->persist($permissionDeleteMediaZone);
        $manager->persist($permissionDeletePriceZone);
        $manager->persist($permissionDuplicateTextZone);
        $manager->persist($permissionDuplicateMediaZone);
        $manager->persist($permissionDuplicatePriceZone);
        $manager->persist($permissionTransformTextZone);
        $manager->persist($permissionTransformMediaZone);
        $manager->persist($permissionTransformPriceZone);
        $manager->persist($permissionMoveForwardTextZone);
        $manager->persist($permissionMoveForwardMediaZone);
        $manager->persist($permissionMoveForwardPriceZone);
        $manager->persist($permissionMoveBackTextZone);
        $manager->persist($permissionMoveBackMediaZone);
        $manager->persist($permissionMoveBackPriceZone);
        $manager->persist($permissionPutTextZoneInForeground);
        $manager->persist($permissionPutMediaZoneInForeground);
        $manager->persist($permissionPutPriceZoneInForeground);
        $manager->persist($permissionPutTextZoneInBackground);
        $manager->persist($permissionPutMediaZoneInBackground);
        $manager->persist($permissionPutPriceZoneInBackground);
        $manager->persist($permissionZoomOnTemplate);
        $manager->persist($permissionZoomOnTemplateModel);
        $manager->persist($permissionZoomOutTemplate);
        $manager->persist($permissionZoomOutTemplateModel);
        $manager->persist($permissionHideZone);
        $manager->persist($permissionAssociateTextZone);
        $manager->persist($permissionAssociateMediaZone);
        $manager->persist($permissionAssociatePriceZone);
        $manager->persist($permissionAssociateCategoryToTextZone);
        $manager->persist($permissionAssociateCategoryToMediaZone);
        $manager->persist($permissionAssociateCategoryToPriceZone);
        $manager->persist($permissionAssociateProductToTextZone);
        $manager->persist($permissionAssociateProductToMediaZone);
        $manager->persist($permissionAssociateProductToPriceZone);
        $manager->persist($permissionLockTextZone);
        $manager->persist($permissionLockMediaZone);
        $manager->persist($permissionLockPriceZone);
        $manager->persist($permissionUnlockTextZone);
        $manager->persist($permissionUnlockMediaZone);
        $manager->persist($permissionUnlockPriceZone);
        $manager->persist($permissionAttributeBackgroundToTextZone);
        $manager->persist($permissionAttributeBackgroundToMediaZone);
        $manager->persist($permissionAttributeBackgroundToPriceZone);
        $manager->persist($permissionCreateBackground);
        $manager->persist($permissionAttributeTextToTextZone);
        $manager->persist($permissionAttributeMediaToMediaZone);
        $manager->persist($permissionAttributePriceToPriceZone);
        $manager->persist($permissionCreateIncrusteClass);
        $manager->persist($permissionAttributeIncrusteClass);
        $manager->persist($permissionCreateTextStyle);
        $manager->persist($permissionAttributeStyleToText);
        $manager->persist($permissionEditTextZoneProperties);
        $manager->persist($permissionEditMediaZoneProperties);
        $manager->persist($permissionEditPriceZoneProperties);
        $manager->persist($permissionImportTemplate);
        $manager->persist($permissionUpload);*/
        // persisting permissions end


        //===============    entity persisting end     ===============//




        //===============    entity flush start     ===============//

        $manager->flush();

        //===============    entity flush end     ===============//


    }
}
