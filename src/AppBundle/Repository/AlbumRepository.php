<?php

namespace AppBundle\Repository;

use AppBundle\Controller\AlbumController;
use AppBundle\Controller\StickageController;

/**
 * AlbumRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AlbumRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Liste alphabetique des albums
     * @uses AlbumController::newAction()
     * @uses AlbumController::editAction()
     */
    public function findListASC()
    {
        return $this->liste()->getQuery()->getResult();
    }

    /**
     * liste des albums a sticker
     * @uses StickageController::indexAction()
     */
    public function findListToStick()
    {
        return $this->liste()->where('a.brut <> 0')->orWhere('a.sticke <> 0')->getQuery()->getResult();
    }

    /**
     * Liste des albums
     */
    public function liste()
    {
        return $this->createQueryBuilder('a')
                    ->orderBy('a.titre', 'ASC')
            ;
    }

    /**
     * Selection de l'album
     */
     public function findAlbums($album)
     {
       return $this->createQueryBuilder('a')
                   ->where('a.slug = :album')
                   ->setParameter('album', $album)
            ;
     }
}
