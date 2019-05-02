<?php


namespace AppBundle\Utilities;


use Doctrine\ORM\EntityManager;

class AlbumUtilities
{
    function __construct(EntityManager $entity)
    {
        $this->em = $entity;
    }

    /**
     * verification d el'existence de l'album
     */
    public function existe($titre, $artiste)
    {
        $album = $this->em->getRepository('AppBundle:Album')->findOneBy(['titre'=>$titre, 'artiste'=>$artiste]);
        if ($album){
            return true;
        }else{
            return false;
        }
    }
}
