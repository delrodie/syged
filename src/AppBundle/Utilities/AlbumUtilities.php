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

    /**
     * Mise a jour de la quantité de l'album
     */
    public function majStock($album, $qte)
    {
        $album = $this->em->getRepository("AppBundle:Album")->findOneBy(['slug'=>$album]);
        $initial = $album->getBrut();
        $album->setBrut($initial + $qte);
        $this->em->persist($album);
        $this->em->flush();

        return true;
    }

    /**
     * Modification de la quantité de l'album
     */
    public function modifStock($album, $qte)
    {
        $album = $this->em->getRepository("AppBundle:Album")->findOneBy(['slug'=>$album]);
        $initial = $album->getBrut();
        $album->setBrut($initial - $qte);
        $this->em->persist($album);
        $this->em->flush();

        return true;
    }

    /**
     * Stickage des albums
     */
    public function stickage($album, $qte)
    {
        $album = $this->em->getRepository("AppBundle:Album")->findOneBy(['slug'=>$album]);
        $initial = $album->getBrut();
        $stike = $album->getSticke();
        $album->setBrut($initial - $qte);
        $album->setSticke($qte + $stike);
        $this->em->persist($album);
        $this->em->flush();

        return true;
    }

    /**
     * Modification du stickage des albums
     */
    public function modifStickage($album, $initial, $qte)
    {
        $album = $this->em->getRepository("AppBundle:Album")->findOneBy(['slug'=>$album]);
        $album->setBrut($initial - $qte);
        $album->setSticke($qte);
        $this->em->flush();

        return true;
    }

    /**
     * Suppression de la quantité du stickage
     */
    public function supStickage($album, $initial, $qte)
    {
        $album = $this->em->getRepository("AppBundle:Album")->findOneBy(['slug'=>$album]);
        $album->setBrut($initial);
        $stock = $album->getSticke();
        $reste = $stock - $qte;
        if ($reste < 0) return false;
        $album->setSticke($reste);
        $this->em->flush();
        return true;
    }

    /**
     * Vente de l'album
     */
    public function vente($album, $qte)
    {
       $album = $this->em->getRepository("AppBundle:Album")->findOneBy(['slug'=>$album]);
       $album->setSticke($album->getSticke() - $qte);
       $album->setDistribue($album->getDistribue() + $qte);
       $this->em->flush();
       return true;
    }

    /**
     * Modification de la vente
     */
    public function modifVente($albumID, $qteInitial, $qte)
    {
        $album = $this->em->getRepository('AppBundle:Album')->findOneBy(['slug'=>$albumID]);
        if ($this->supVente($albumID, $qteInitial)){
            $album->setSticke($album->getSticke() - $qte);
            $album->setDistribue($album->getDistribue() + $qte);
            $this->em->flush();
            return true;
        }else{
            return false;
        }
    }

    /**
     * Suppression de la vente
     */
    public function supVente($album, $qteInitial)
    {
        $album = $this->em->getRepository('AppBundle:Album')->findOneBy(['slug'=>$album]);
        // Deduction de la quantité totale la quantité distribuée de la quantité initiale vendue
        $album->setSticke($album->getSticke() + $qteInitial);
        $album->setDistribue($album->getDistribue() - $qteInitial);
        $this->em->flush();
        return true;
    }
}
