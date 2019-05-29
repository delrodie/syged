<?php


namespace AppBundle\Utilities;


use Doctrine\ORM\EntityManager;

class DistributeurUtilities
{
    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Verification de l'existence du distributeur
     */
    public function existe($distributeur)
    {
        $distributeur = $this->em->getRepository('AppBundle:Distributeur')->findOneBy(['nom'=> $distributeur]);
        //dump($artiste);die();
        if ($distributeur){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Mise a jour du credit du distributeur
     */
    public function addCredit($distributueur, $montant)
    {
        $distributueur = $this->em->getRepository("AppBundle:Distributeur")->findOneBy(['id'=>$distributueur]);
        $distributueur->setCredit($distributueur->getCredit() + $montant);
        $this->em->flush();
        return true;
    }
}
