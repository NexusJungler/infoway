<?php


namespace App\Controller\Back_office;


use App\Entity\Admin\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route(path="/back-office")
 */
class AdminUserController extends AbstractController
{

    /**
     * @Route(path="/show/user/{id}-{name}", name="admin_user::show", methods="GET")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function show(User $user, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $user);

        return $this->render("back-office/user/create_show_edit.html.twig", [
            'form' => $form->createView(),
            'title' => "Voir un utilisateur",
            'sites' => [$user->getSite()],
            'user' => $user
        ]);
    }


    /**
     * @Route(path="/edit/user/{id}-{name}", name="admin_user::edit", methods="GET|POST")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function edit(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("message", "Utilisateur modifié !");

        }

        return $this->render("back-office/user/create_show_edit.html.twig", [
            'form' => $form->createView(),
            'title' => "Editer un utilisateur",
            'sites' => [$user->getSite()]
        ]);
    }


    /**
     * @Route(path="/delete/user/{id}-{name}", name="admin_user::delete", methods="DELETE")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function delete(User $user, Request $request): Response
    {
        if($this->isCsrfTokenValid("delete". $user->getId(), $request->get("_token")))
        {

            $this->getDoctrine()->getManager()->remove($user);

            $this->getDoctrine()->getManager()->flush();
        }
        else
        {
            $this->addFlash("error", "Token CSRF non valide !");
        }

        return $this->redirectToRoute('admin_site::showSiteSalaries', [
            'id' => $user->getSite()->getId(),
            'name' => $user->getSite()->getName(),
        ]);
    }

}