<?php


namespace App\Controller;


use App\Entity\Admin\{ Action AS Admin_Action, Country AS Admin_Country, Customer AS Admin_Customer, Feature AS Admin_Feature, Permission AS Admin_Permission, Role AS Admin_Role, Subject AS Admin_Subject, TimeZone AS Admin_TimeZone, User AS Admin_User };
use App\Repository\Admin\{ PermissionRepository AS Admin_PermissionRepository };
use App\Service\TokenGeneratorService;
use Faker\{ Factory, Generator };
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\{ PermissionsHandler };


/**
 * This class is used for code testing
 */
class TestController extends AbstractController
{


    /**
     * @var ObjectManager
     */
    private ObjectManager $__manager;


    public function __construct()
    {

    }

}