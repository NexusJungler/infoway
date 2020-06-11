<?php


namespace App\Controller;

use App\Entity\Admin\Allergen;
use App\Form\Admin\AllergenType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllergenController extends AbstractController
{
    /**
     * @Route(path="allergens/show", name="allergens::show", methods="GET")
     *
     * @return Response
     * @throws \Exception
     */
    public function show(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $allergens = $em->getRepository(Allergen::class)->findAll();

        return $this->render("allergens/show.html.twig", [
            'allergens' => $allergens
        ]);
    }

    /**
     * @Route(path="allergen/create", name="allergen::create", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request): Response
    {
        $allergen = new Allergen();
        // $allergen->setCreatedAt(new \DateTime());

        $form = $this->createForm(AllergenType::class, $allergen);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $pictoFile = $form->get('pictogram')->getData();
            if ($pictoFile) {
                $originalFilename = pathinfo($pictoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $pictoFile->guessExtension();

                try {
                    $pictoFile->move(
                        $this->getParameter('pictoDirectory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                   $e->getMessage();
                }
                $allergen->setPictogram($newFilename);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($allergen);
            $em->flush();
            return $this->redirectToRoute('allergens::show');
        }

        return $this->render("allergens/create.html.twig", [
            'form' => $form->createView()
        ]);


    }

    /**
     * @Route(path="allergen/edit/{allergen}", name="allergen::edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Allergen $allergen
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, Allergen $allergen): Response
    {
        $em = $this->getDoctrine()->getManager('kfc');
        $form = $this->createForm(AllergenType::class, $allergen);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $pictoFile = $form->get('pictogram')->getData();
            if ($pictoFile) {
                $originalFilename = pathinfo($pictoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $pictoFile->guessExtension();

                try {
                    $pictoFile->move(
                        $this->getParameter('pictoDirectory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $e->getMessage();
                }
                $allergen->setPictogram($newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('allergens::show');
        }

        return $this->render("allergens/create.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(path="allergens/delete", name="allergens::delete", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request): Response
    {
        $allergens = $request->request->get('allergens');
        if($allergens != []) {
            $em = $this->getDoctrine()->getManager();
            $rep = $em->getRepository( Allergen::class);
            foreach ($allergens as $id => $val) {
                $allergen = $rep->find($id);
                $em->remove($allergen);
                $em->flush();
            }
        }
        return $this->redirectToRoute('allergens::show');
    }

}