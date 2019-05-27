<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stockage;
use AppBundle\Utilities\AlbumUtilities;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Stockage controller.
 *
 * @Route("stockage")
 */
class StockageController extends Controller
{
    /**
     * Lists all stockage entities.
     *
     * @Route("/", name="stockage_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stockages = $em->getRepository('AppBundle:Stockage')->findAll();
        $albums = $em->getRepository('AppBundle:Album')->findListASC();

        return $this->render('stockage/index.html.twig', array(
            'stockages' => $stockages,
            'albums' => $albums,
            'current_page' => 'album'
        ));
    }

    /**
     * Creates a new stockage entity.
     *
     * @Route("/new/{artiste}-{album}", name="stockage_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $artiste, $album, AlbumUtilities $albumUtilities)
    {
        $stockage = new Stockage();
        $form = $this->createForm('AppBundle\Form\StockageType', $stockage, ['artiste'=>$artiste, 'album'=>$album]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $initial = $em->getRepository('AppBundle:Album')->findOneBy(['slug'=>$album])->getBrut();
            if (!$initial){ $initial = 0;}
            $stockage->setStockinitial($initial); //dump($stockage);die();
            $em->persist($stockage);
            $em->flush();

            // Mise a jour du stock de l'album dans la table album
            if ($albumUtilities->majStock($album, $stockage->getQte())){
                $this->addFlash('notice', "L'approvisionnement  a été effectué avec succès!");
            }else{
                $this->addFlash('error', "Echec de l'approvisionnement!");
            }

            return $this->redirectToRoute('stockage_index');
        }

        $em = $this->getDoctrine()->getManager();
        $stockages = $em->getRepository('AppBundle:Stockage')->findApprovisionnement($album); //dump($album);die();
        $album = $em->getRepository('AppBundle:Album')->findOneBy(['slug'=>$album]); //dump($album);die();

        return $this->render('stockage/new.html.twig', array(
            'stockage' => $stockage,
            'stockages' => $stockages,
            'album' => $album,
            'form' => $form->createView(),
            'current_page' => 'album'
        ));
    }

    /**
     * Finds and displays a stockage entity.
     *
     * @Route("/{id}", name="stockage_show")
     * @Method("GET")
     */
    public function showAction(Stockage $stockage)
    {
        $deleteForm = $this->createDeleteForm($stockage);

        return $this->render('stockage/show.html.twig', array(
            'stockage' => $stockage,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing stockage entity.
     *
     * @Route("/{id}/edit", name="stockage_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Stockage $stockage, AlbumUtilities $albumUtilities)
    {
        $deleteForm = $this->createDeleteForm($stockage);
        $editForm = $this->createForm('AppBundle\Form\StockageType', $stockage, ['artiste'=>$stockage->getAlbum()->getArtiste()->getId(), 'album'=>$stockage->getAlbum()->getSlug()]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $initial = $request->get('stockInitial');
            $modif = $stockage->getQte();
            $definitif = $initial - $modif;
            $this->getDoctrine()->getManager()->flush();

            // Mise a jour du stock de l'album dans la table album
            if ($albumUtilities->modifStock($stockage->getAlbum()->getSlug(), $definitif)){
                $this->addFlash('notice', "L'approvisionnement  a été effectué avec succès!");
            }else{
                $this->addFlash('error', "Echec de l'approvisionnement!");
            }

            return $this->redirectToRoute('stockage_edit', array('id' => $stockage->getId()));
        }

        $em = $this->getDoctrine()->getManager();
        $stockages = $em->getRepository('AppBundle:Stockage')->findApprovisionnement($stockage->getAlbum()->getSlug()); //dump($album);die();
        $album = $em->getRepository('AppBundle:Album')->findOneBy(['slug'=>$stockage->getAlbum()->getSlug()]); //dump($album);die();

        return $this->render('stockage/edit.html.twig', array(
            'stockage' => $stockage,
            'stockages' => $stockages,
            'album' => $album,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'current_page' => 'album'
        ));
    }

    /**
     * Deletes a stockage entity.
     *
     * @Route("/{id}", name="stockage_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Stockage $stockage, AlbumUtilities $albumUtilities)
    {
        $form = $this->createDeleteForm($stockage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Mise a jour du stock de l'album dans la table album
            if ($albumUtilities->modifStock($stockage->getAlbum()->getSlug(), $stockage->getQte())){
                $em->remove($stockage);
                $em->flush();
                $this->addFlash('notice', "L'approvisionnement  a été effectué avec succès!");
            }else{
                $this->addFlash('error', "Echec de l'approvisionnement!");
            }
        }

        return $this->redirectToRoute('stockage_index');
    }

    /**
     * Creates a form to delete a stockage entity.
     *
     * @param Stockage $stockage The stockage entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Stockage $stockage)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stockage_delete', array('id' => $stockage->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
