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

    /**
     * Ajout du nombre d'album dans la liste de l'artiste
     */
    public function ajoutAlbum($artiste)
    {
        $art = $this->em->getRepository('AppBundle:Artiste')->findOneBy(['id'=>$artiste->getId()]);
        $qte = $art->getAlbums();
        $art->setAlbums($qte + 1);
        $this->em->persist($art);
        $this->em->flush();

        return true;
    }

    /**
     * Reduction du nombre d'album dans la liste de l'artiste
     */
    public function supAlbum($artiste)
    {
        $art = $this->em->getRepository('AppBundle:Artiste')->findOneBy(['id'=>$artiste->getId()]);
        $qte = $art->getAlbums();
        $art->setAlbums($qte - 1);
        $this->em->persist($art);
        $this->em->flush();

        return true;
    }
}
