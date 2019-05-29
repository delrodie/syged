<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Vente;
use AppBundle\Utilities\AlbumUtilities;
use AppBundle\Utilities\DistributeurUtilities;
use AppBundle\Utilities\VenteUtilities;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Vente controller.
 *
 * @Route("vente")
 */
class VenteController extends Controller
{
    /**
     * Lists all vente entities.
     *
     * @Route("/", name="vente_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ventes = $em->getRepository('AppBundle:Vente')->findAll();
        $albums = $em->getRepository("AppBundle:Album")->findListToSell();

        return $this->render('vente/index.html.twig', array(
            'ventes' => $ventes,
            'albums' => $albums,
            'current_page' => 'distribution'
        ));
    }

    /**
     * Creates a new vente entity.
     *
     * @Route("/new/{album}", name="vente_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $album, VenteUtilities $venteUtilities, AlbumUtilities $albumUtilities, DistributeurUtilities $distributeurUtilities)
    {
        $vente = new Vente();
        $form = $this->createForm('AppBundle\Form\VenteType', $vente, ['album'=> $album]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // Verification si le nombre stické est suffisant pour la vente
            $albums = $em->getRepository("AppBundle:Album")->findOneBy(['slug'=>$album]);
            if ($albums->getSticke() < $vente->getQte()){
                $this->addFlash('error', "Echec de vente car la quantité à vendre suppérieure à la quantité stickée.");
                return $this->redirectToRoute('vente_new',['album'=>$album]);
            }
            // Montant total de la vente
            $vente->setMontant($venteUtilities->montant($vente->getQte(), $vente->getPrix()));
            $em->persist($vente);
            $em->flush();
            // mise a jour de la table album (sticke et distribution)
            $albumUtilities->vente($album,$vente->getQte());

            // mise a jour de la table distributeur (credit)
            $distributeurUtilities->addCredit($vente->getDistributeur()->getId(), $vente->getMontant());

            $this->addFlash('notice', "Vente effectuée avec succès");
            return $this->redirectToRoute('vente_new', array('album' => $vente->getAlbum()->getSlug()));
        }
        // Liste des albums a vendre
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('AppBundle:Album')->findOneBy(['slug'=>$album]);
        $ventes = $em->getRepository('AppBundle:Vente')->findBy(['album'=>$album->getId()]);

        return $this->render('vente/new.html.twig', array(
            'vente' => $vente,
            'form' => $form->createView(),
            'ventes' => $ventes,
            'album' => $album,
            'current_page' => 'distribution'
        ));
    }

    /**
     * Finds and displays a vente entity.
     *
     * @Route("/{id}", name="vente_show")
     * @Method("GET")
     */
    public function showAction(Vente $vente)
    {
        $deleteForm = $this->createDeleteForm($vente);

        return $this->render('vente/show.html.twig', array(
            'vente' => $vente,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing vente entity.
     *
     * @Route("/{id}/edit", name="vente_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Vente $vente)
    {
        $deleteForm = $this->createDeleteForm($vente);
        $editForm = $this->createForm('AppBundle\Form\VenteType', $vente);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vente_edit', array('id' => $vente->getId()));
        }

        return $this->render('vente/edit.html.twig', array(
            'vente' => $vente,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a vente entity.
     *
     * @Route("/{id}", name="vente_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Vente $vente)
    {
        $form = $this->createDeleteForm($vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($vente);
            $em->flush();
        }

        return $this->redirectToRoute('vente_index');
    }

    /**
     * Creates a form to delete a vente entity.
     *
     * @param Vente $vente The vente entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Vente $vente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('vente_delete', array('id' => $vente->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
