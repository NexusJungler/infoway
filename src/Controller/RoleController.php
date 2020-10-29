<?php


namespace App\Controller;

use App\Entity\Customer\Role;
use App\Entity\Customer\RoleList;
use App\Form\EditRolesType;
use App\Service\SessionManager;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RoleController extends AbstractController
{

    /**
     * @var SessionManager
     */
    private SessionManager $__sessionManager;
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $__parameterBag;
    /**
     * @var Serializer
     */
    private Serializer $__serializer;

    public function __construct(SessionManager $sessionManager, ParameterBagInterface $parameterBag)
    {

        $circularReferenceHandlingContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $encoder =  new JsonEncoder();
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $circularReferenceHandlingContext);
        $this->__serializer = new Serializer( [ $normalizer, new DateTimeNormalizer() ] , [ $encoder ] );

        $this->__sessionManager = $sessionManager;
        $this->__parameterBag = $parameterBag;

    }

    /**
     * @Route(path="/edit/role/{id}", name="role::edit", methods={"POST", "GET"})
     */
    public function edit(Request $request)
    {

    }

    /**
     * @Route(path="/edit/roles", name="role::editAllRoles", methods={"POST", "GET"})
     */
    public function editAllRoles(Request $request)
    {
        $managerName = strtolower( $this->__sessionManager->get('current_customer')->getName() );
        $manager = $this->getDoctrine()->getManager( $managerName );
        $roles = $manager->getRepository(Role::class)->getAllRolesOrderedByLevel();
        $rolesList = new RoleList();
        $rolesList->setRoles( (new ArrayCollection($roles)) );
        $editRolesForm = $this->createForm(EditRolesType::class, $rolesList);
        $editRolesForm->handleRequest($request);

        if($editRolesForm->isSubmitted() && $editRolesForm->isValid())
        {
            dd($editRolesForm);
        }



        //$editRolesFormView = $editRolesForm->createView();

        //dd($editRolesFormView);

        return $this->render("settings/role/edit_all_roles.html.twig", [
            'roles' => $roles,
            'editRolesForm' => $editRolesForm->createView(),
        ]);
    }

    /**
     * @Route(path="/delete/roles", name="role::deleteRoles", methods={"POST"})
     */
    public function deleteRoles(Request $request)
    {
        dd($request);
    }

    /**
     * @Route(path="/delete/role/{id}", name="role::delete", methods={"POST", "GET"}, requirements={"id": "\d+"})
     */
    public function delete(Request $request, int $id)
    {
        $managerName = strtolower($this->__sessionManager->get('current_customer')->getName());
        $manager = $this->getDoctrine()->getManager($managerName);
        $roleRepository = $manager->getRepository(Role::class);

        $role = $roleRepository->find($id);
        if(!$role)
            throw new Exception(sprintf("No role found with id : %d", $id));

        $manager->remove($role);
        $manager->flush();

        return new JsonResponse([ 'status' => '200 OK' ]);
    }

}