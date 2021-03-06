<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'current_page' => 'home',
        ]);
    }

    /**
     * @Route("/template/bon-commande", name="bon_commande")
     */
    public function commandeAction()
    {
        return $this->render('template/commande.html.twig',[
            'current_page' => 'home'
        ]);
    }
}
