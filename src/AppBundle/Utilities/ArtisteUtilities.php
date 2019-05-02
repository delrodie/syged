<?php


namespace AppBundle\Utilities;


use Doctrine\ORM\EntityManager;

class ArtisteUtilities
{
    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Verification de l'existence de l'artiste
     */
    public function existe($artiste)
    {
        $artiste = $this->em->getRepository('AppBundle:Artiste')->findOneBy(['nom'=> $artiste]);
        //dump($artiste);die();
        if ($artiste){
            return false;
        }else{
            return true;
        }
    }
}
