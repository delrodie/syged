<?php


namespace AppBundle\Utilities;


use Doctrine\ORM\EntityManager;

class VenteUtilities
{
    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Calcul du montant total de la vente
     */
    public function montant($qte, $prix)
    {
        return $montant = $qte *$prix;
    }
}
