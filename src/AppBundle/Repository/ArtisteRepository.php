<?php

namespace AppBundle\Repository;

use AppBundle\Controller\ArtisteController;

/**
 * ArtisteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArtisteRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Liste des artistes par ordre alphabetique
     * @uses ArtisteController::newAction()
     */
    public function findListAsc()
    {
        return $this->createQueryBuilder('a')
                    ->orderBy('a.nom')
                    ->getQuery()->getResult()
            ;
    }

    /**
     * Liste des artistes pour la selection de l'album
     */
    public function liste()
    {
        return $this->createQueryBuilder('a')
                    ->where('a.statut = 1')
                    ->orderBy('a.nom')
            ;
    }

    /**
     * Selection de l'Artiste
     */
     public function findArtiste($artiste)
     {
       return $this->createQueryBuilder('a')
                   ->where('a.id = :artiste')
                   ->setParameter('artiste', $artiste)
            ;
     }
}
