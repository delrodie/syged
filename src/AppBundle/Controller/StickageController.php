<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stickage;
use AppBundle\Utilities\AlbumUtilities;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Stickage controller.
 *
 * @Route("stickage")
 */
class StickageController extends Controller
{
    /**
     * Lists all stickage entities.
     *
     * @Route("/", name="stickage_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stickages = $em->getRepository('AppBundle:Stickage')->findAll();
        $albums = $em->getRepository('AppBundle:Album')->findListToStick();

        return $this->render('stickage/index.html.twig', array(
            'stickages' => $stickages,
            'albums' => $albums,
            'current_page' => 'stickage'
        ));
    }

    /**
     * Creates a new stickage entity.
     *
     * @Route("/new/{album}", name="stickage_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $album, AlbumUtilities $albumUtilities)
    {
        $stickage = new Stickage();
        $form = $this->createForm('AppBundle\Form\StickageType', $stickage, ['album'=>$album]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $albums = $em->getRepository('AppBundle:Album')->findOneBy(['slug'=>$album]);
            $stockAlbum = $albums->getBrut();
            // Si la quantité a sticker est supérieur au stock initial alors renvoie echec
            if ($stockAlbum < $stickage->getQte()){
                $this->addFlash('error', "Attention la quantité à sticker est supérieure au stock initial. Veuillez faire la mise a jour du stock");
                return $this->redirectToRoute('stockage_new', ['artiste'=>$albums->getArtiste()->getId(), 'album'=>$albums->getSlug()]);
            }
            $stickage->setStockinitial($stockAlbum);
            //dump($stickage);die();
            $em->persist($stickage);
            $em->flush();

            // Mise a jour du stock de l'album dans la table album
            if ($albumUtilities->stickage($album, $stickage->getQte())){
                $this->addFlash('notice', "Le stickage a été effectué avec succès!");
            }else{
                $this->addFlash('error', "Echec du stickage!");
            }

            return $this->redirectToRoute('stickage_new', ['album'=>$album]);
        }
        // Liste des albums a sticker
        $em = $this->getDoctrine()->getManager();
        $stickages = $em->getRepository('AppBundle:Stickage')->findToStickage($album);
        $album = $em->getRepository('AppBundle:Album')->findOneBy(['slug'=>$album]);

        return $this->render('stickage/new.html.twig', array(
            'stickage' => $stickage,
            'stickages' => $stickages,
            'album' => $album,
            'form' => $form->createView(),
            'current_page' => 'stickage'
        ));
    }

    /**
     * Finds and displays a stickage entity.
     *
     * @Route("/{id}", name="stickage_show")
     * @Method("GET")
     */
    public function showAction(Stickage $stickage)
    {
        $deleteForm = $this->createDeleteForm($stickage);

        return $this->render('stickage/show.html.twig', array(
            'stickage' => $stickage,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing stickage entity.
     *
     * @Route("/{id}/edit", name="stickage_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Stickage $stickage, AlbumUtilities $albumUtilities)
    {
        $deleteForm = $this->createDeleteForm($stickage);
        $editForm = $this->createForm('AppBundle\Form\StickageType', $stickage, ['album'=> $stickage->getAlbum()->getSlug()]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $albums = $em->getRepository('AppBundle:Album')->findOneBy(['slug'=>$stickage->getAlbum()->getSlug()]);
            $stockAlbum = $albums->getBrut();
            // Si la quantité a sticker est supérieur au stock initial alors renvoie echec
            $stockInitial = $request->get('stockInitial'); //dump($stockAlbum);die();
            if ($stickage->getStockinitial() < $stickage->getQte()){
                $this->addFlash('error', "Attention la quantité à sticker est supérieure au stock initial. Veuillez faire la mise a jour du stock");
                return $this->redirectToRoute('stockage_new', ['artiste'=>$albums->getArtiste()->getId(), 'album'=>$albums->getSlug()]);
            }
            //$stickage->setStockinitial($stockAlbum);

            $em->flush();

            // Mise a jour du stock de l'album dans la table album
            if ($albumUtilities->modifStickage($stickage->getAlbum()->getSlug(), $stickage->getStockinitial(), $stickage->getQte())){
                $this->addFlash('notice', "Le stickage a été effectué avec succès!");
            }else{
                $this->addFlash('error', "Echec du stickage!");
            }

            return $this->redirectToRoute('stickage_new', array('album' => $stickage->getAlbum()->getSlug()));
        }
        // Liste des albums a sticker
        $em = $this->getDoctrine()->getManager();
        $stickages = $em->getRepository('AppBundle:Stickage')->findToStickage($stickage->getAlbum()->getSlug());
        $album = $em->getRepository('AppBundle:Album')->findOneBy(['slug'=>$stickage->getAlbum()->getSlug()]);

        return $this->render('stickage/edit.html.twig', array(
            'stickage' => $stickage,
            'stickages' => $stickages,
            'album' => $album,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'current_page' => 'stickage'
        ));
    }

    /**
     * Deletes a stickage entity.
     *
     * @Route("/{id}", name="stickage_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Stickage $stickage, AlbumUtilities $albumUtilities)
    {
        $form = $this->createDeleteForm($stickage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // verification
            $dernier = $em->getRepository("AppBundle:Stickage")->findOneBy(['album'=>$stickage->getAlbum()->getId()], ['id'=>'DESC']);
            if ($dernier->getId() != $stickage->getId()){
                $this->addFlash('error', "Impossible de supprimer ce stickage car il n'est pas le dernier de cet album");
                return $this->redirectToRoute('stickage_edit',['id'=>$stickage->getId()]);
            }
            if ($albumUtilities->supStickage($stickage->getAlbum()->getSlug(), $stickage->getStockinitial(), $stickage->getQte())){
                $em->remove($stickage);
                $em->flush();
                $this->addFlash('succes', "Stickage supprimé avec succès");
                return $this->redirectToRoute('stickage_index');
            }else{
                $this->addFlash('error', "Impossible de supprimer ce stickage car les CD concernés ont déjà été vendus");
                return $this->redirectToRoute('stickage_edit',['id'=>$stickage->getId()]);
            }
        }

    }

    /**
     * Creates a form to delete a stickage entity.
     *
     * @param Stickage $stickage The stickage entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Stickage $stickage)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stickage_delete', array('id' => $stickage->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
