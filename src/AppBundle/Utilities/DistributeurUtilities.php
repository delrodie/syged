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
        $distributeur = $this->em->getRepository("AppBundle:Distributeur")->findOneBy(['id'=>$distributueur]);
        $distributeur->setCredit($distributeur->getCredit() + $montant);
        $this->em->flush();
        return true;
    }

    /**
     * Modification de la vente
     */
    public function updateCredit($distributeurID, $mtInitial, $montant)
    {
        $distributeur = $this->em->getRepository('AppBundle:Distributeur')->findOneBy(['id'=>$distributeurID]);
        // Deduction de l'ancien montant
        if ($this->deleteCredit($distributeurID, $mtInitial)){
            $distributeur->setCredit($distributeur->getCredit() + $montant);
            $this->em->flush();
            return true;
        }else{
            return false;
        }
    }

    /**
     * Suppresion de la vente
     */
    public function deleteCredit($distributeur, $mtInitial)
    {
        $distributeur = $this->em->getRepository('AppBundle:Distributeur')->findOneBy(['id'=>$distributeur]);
        $distributeur->setCredit($distributeur->getCredit() - $mtInitial);
        $this->em->flush();
        return true;
    }
}
