<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Distributeur;
use AppBundle\Utilities\DistributeurUtilities;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Distributeur controller.
 *
 * @Route("distributeur")
 */
class DistributeurController extends Controller
{
    /**
     * Lists all distributeur entities.
     *
     * @Route("/", name="distributeur_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $distributeurs = $em->getRepository('AppBundle:Distributeur')->findAll();

        return $this->render('distributeur/index.html.twig', array(
            'distributeurs' => $distributeurs,
        ));
    }

    /**
     * Creates a new distributeur entity.
     *
     * @Route("/new", name="distributeur_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, DistributeurUtilities $distributeurUtilities)
    {
        $distributeur = new Distributeur();
        $form = $this->createForm('AppBundle\Form\DistributeurType', $distributeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (!$distributeurUtilities->existe($distributeur->getNom())){
                $this->addFlash('error', "Echec: le distributeur ".$distributeur->getNom()." existe déja.");
                return $this->redirectToRoute("distributeur_new");
            }
            $em->persist($distributeur);
            $em->flush();

            $this->addFlash('notice', $distributeur->getNom()." a été ajouté(e) avec succès dans la liste des distributeurs.");

            return $this->redirectToRoute('distributeur_new');
        }
        // Liste des distributeurs
        $em = $this->getDoctrine()->getManager();
        $distributeurs = $em->getRepository("AppBundle:Distributeur")->findListAsc();

        return $this->render('distributeur/new.html.twig', array(
            'distributeur' => $distributeur,
            'distributeurs' => $distributeurs,
            'form' => $form->createView(),
            'current_page' => 'distributeur'
        ));
    }

    /**
     * Finds and displays a distributeur entity.
     *
     * @Route("/{id}", name="distributeur_show")
     * @Method("GET")
     */
    public function showAction(Distributeur $distributeur)
    {
        $deleteForm = $this->createDeleteForm($distributeur);

        return $this->render('distributeur/show.html.twig', array(
            'distributeur' => $distributeur,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing distributeur entity.
     *
     * @Route("/{slug}/edit", name="distributeur_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Distributeur $distributeur)
    {
        $deleteForm = $this->createDeleteForm($distributeur);
        $editForm = $this->createForm('AppBundle\Form\DistributeurType', $distributeur);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('notice', "Les informations de distributeur ".$distributeur->getNom()." ont été modifiées avec succès");

            return $this->redirectToRoute('distributeur_new');
        }
        // Liste des distributeurs
        $em = $this->getDoctrine()->getManager();
        $distributeurs = $em->getRepository("AppBundle:Distributeur")->findListAsc();

        return $this->render('distributeur/edit.html.twig', array(
            'distributeur' => $distributeur,
            'distributeurs' => $distributeurs,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'current_page' => 'distributeur'
        ));
    }

    /**
     * Deletes a distributeur entity.
     *
     * @Route("/{id}", name="distributeur_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Distributeur $distributeur)
    {
        $form = $this->createDeleteForm($distributeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($distributeur);
            $em->flush();
        }

        return $this->redirectToRoute('distributeur_index');
    }

    /**
     * Creates a form to delete a distributeur entity.
     *
     * @param Distributeur $distributeur The distributeur entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Distributeur $distributeur)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('distributeur_delete', array('id' => $distributeur->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
