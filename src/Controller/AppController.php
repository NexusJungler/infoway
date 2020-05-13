<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response };
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{

    /**
     *
     * @Route("/", name="app")
     * @Route("/template", name="app::home")
     *
     * @param Request $request
     * @return Response
     */
    public function homePage(Request $request): Response
    {

//        if($this->getUser() === null)
//            return $this->redirectToRoute("user::login");

        $customer = [
            'ARES',
            'Q087',
            'AEAS',
            'Q087A2',
            'ARAS',
            'Q08'
        ];

        $location = (object) [
            'city' => 'Paris',
            'timezone' => 'Europe/Paris',
            'date_format' => 'd-m-Y',
            'clock_format' => 24
        ];

        //dump($location);

        return $this->render("home/zone-diffusion.html.twig", [
            'customer' => $customer,
            'location' => $location
        ]);
    }

    /**
     * @Route(path="/test", name="app:test")
     *
     * @param Request $request
     * @return Response
     */
    public function test(Request $request): Response
    {

        return $this->render("test.html.twig", [

        ]);

    }

    /** Page Produits **/

    /**
     * @Route(path="/products", name="app:products")
     *
     * @param Request $request
     * @return Response
     */
    public function products(Request $request): Response
    {
        $customer = [
            'nom',
            'format',
            'categorie',
            'description',
            'tags',
            'Q08'
        ];
        return $this->render("products/products_price.html.twig", [
            'customer' => $customer
        ]);
    }

    /**
     * @Route(path="/create-product", name="app:create-product")
     *
     * @param Request $request
     * @return Response
     */
    public function create_product(Request $request): Response
    {
        $customer = [
            'nom',
            'format',
            'categorie',
            'description',
            'tags',
            'Q08'
        ];
        return $this->render("products/create_product.html.twig", [
            'customer' => $customer
        ]);
    }

    /**
     * @Route(path="/categories", name="app:categories")
     *
     * @param Request $request
     * @return Response
     */
    public function categories(Request $request): Response
    {
        
        return $this->render("products/categories.html.twig", [
        ]);
    }

    /**
     * @Route(path="/prix", name="app:prix")
     *
     * @param Request $request
     * @return Response
     */
    public function prix(Request $request): Response
    {
        $customer = [
            'nom',
            'format',
            'categorie',
            'description',
            'tags',
            'Q08'
        ];
        return $this->render("products/price.html.twig", [
            'customer' => $customer
        ]);
    }



    /**
     * @Route(path="/site", name="app:site")
     *
     * @param Request $request
     * @return Response
     */
    public function site(Request $request): Response
    {
        $customer = [
            'nom',
            'format',
            'categorie',
            'description',
            'tags',
            'Q08'
        ];
        return $this->render("site.html.twig", [
            'customer' => $customer
        ]);
    }

    /**
     * @Route(path="/programming", name="app:programming")
     *
     * @param Request $request
     * @return Response
     */
    public function programming(Request $request): Response
    {
        $customer = [
            'nom',
            'format',
            'categorie',
            'description',
            'tags',
            'Q08'
        ];
        return $this->render("programming.html.twig", [
            'customer' => $customer
        ]);

    }

    /**
     * @Route(path="/media", name="app:media")
     *
     * @param Request $request
     * @return Response
     */
    public function media(Request $request): Response
    {
        $customer = [
            'nom',
            'format',
            'categorie',
            'description',
            'tags',
            'Q08'
        ];
        return $this->render("media.html.twig", [
            'customer' => $customer
        ]);

    }

    /**
     * @Route(path="/info", name="app:info")
     *
     * @param Request $request
     * @return Response
     */
    public function info(Request $request): Response
    {
        $customer = [
            'nom',
            'format',
            'categorie',
            'description',
            'tags',
            'Q08'
        ];
        return $this->render("info.html.twig", [
            'customer' => $customer
        ]);

    }

    /**
     * @Route(path="/createuser", name="app:createuser")
     *
     * @param Request $request
     * @return Response
     */
    public function createUser(Request $request): Response
    {

        return $this->render("settings/create-user.html.twig", [

        ]);

    }

    /**
     * @Route(path="settinguser", name="app:settinguser")
     *
     * @param Request $request
     * @return Response
     */
    public function settinguser(Request $request): Response
    {

        return $this->render("settings/setting-user.html.twig", [

        ]);

    }

    /**
     * @Route(path="/enseigne", name="app:enseigne")
     *
     * @param Request $request
     * @return Response
     */
    public function enseigne(Request $request): Response
    {

        return $this->render("settings/enseigne.html.twig", [

        ]);

    }


    /**
     * @Route(path="/managementtags", name="app:managementtags")
     *
     * @param Request $request
     * @return Response
     */
    public function managementtags(Request $request): Response
    {

        return $this->render("Tags/show.html.twig", [

        ]);

    }

    /**  MEDIA **/

    /**
     * @Route(path="/image", name="app:image")
     *
     * @param Request $request
     * @return Response
     */
    public function image(Request $request): Response
    {

        return $this->render("image/media-image.html.twig", [

        ]);

    }

    /**
     * @Route(path="/create-date", name="app:date")
     *
     * @param Request $request
     * @return Response
     */
    public function date(Request $request): Response
    {

        return $this->render("pricesfactories/create-date.html.twig", [

        ]);

    }


}