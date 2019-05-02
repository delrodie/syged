<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Artiste;
use AppBundle\Utilities\ArtisteUtilities;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Artiste controller.
 *
 * @Route("admin/artiste")
 */
class ArtisteController extends Controller
{
    /**
     * Lists all artiste entities.
     *
     * @Route("/", name="admin_artiste_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('admin_artiste_new');
    }

    /**
     * Creates a new artiste entity.
     *
     * @Route("/new", name="admin_artiste_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, ArtisteUtilities $utilities)
    {
        $artiste = new Artiste();
        $form = $this->createForm('AppBundle\Form\ArtisteType', $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager(); //dump($artiste->getNom());die();
            if ($utilities->existe($artiste->getNom())){
                $em->persist($artiste);
                $em->flush();

                $this->addFlash('notice', "L'artiste ".$artiste->getNom()." a été ajouté avec succès!!");
            }else{
                $this->addFlash('error', "Echec l'artiste ".$artiste->getNom()." existe déjà! Veuillez ajouter un autre.");
            }


            return $this->redirectToRoute('admin_artiste_new');
        }

        //Liste des artistes
        $em = $this->getDoctrine()->getManager();
        $artistes = $em->getRepository('AppBundle:Artiste')->findListAsc();

        return $this->render('artiste/new.html.twig', array(
            'artiste' => $artiste,
            'artistes' => $artistes,
            'form' => $form->createView(),
            'current_page' => 'artiste'
        ));
    }

    /**
     * Finds and displays a artiste entity.
     *
     * @Route("/{id}", name="admin_artiste_show")
     * @Method("GET")
     */
    public function showAction(Artiste $artiste)
    {
        $deleteForm = $this->createDeleteForm($artiste);

        return $this->render('artiste/show.html.twig', array(
            'artiste' => $artiste,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing artiste entity.
     *
     * @Route("/{slug}/edit", name="admin_artiste_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Artiste $artiste)
    {
        $deleteForm = $this->createDeleteForm($artiste);
        $editForm = $this->createForm('AppBundle\Form\ArtisteType', $artiste);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('notice', "L'artiste ".$artiste->getNom()." a été modifié avec succès!");

            return $this->redirectToRoute('admin_artiste_new');
        }

        // Liste des artistes
        $em = $this->getDoctrine()->getManager();
        $artistes = $em->getRepository('AppBundle:Artiste')->findListAsc();

        return $this->render('artiste/edit.html.twig', array(
            'artiste' => $artiste,
            'artistes' => $artistes,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'current_page' => 'artiste',
        ));
    }

    /**
     * Deletes a artiste entity.
     *
     * @Route("/{id}", name="admin_artiste_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Artiste $artiste)
    {
        $form = $this->createDeleteForm($artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($artiste);
            $em->flush();
        }

        return $this->redirectToRoute('admin_artiste_index');
    }

    /**
     * Creates a form to delete a artiste entity.
     *
     * @param Artiste $artiste The artiste entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Artiste $artiste)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_artiste_delete', array('id' => $artiste->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
