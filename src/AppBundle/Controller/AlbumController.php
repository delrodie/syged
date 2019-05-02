<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Album;
use AppBundle\Utilities\AlbumUtilities;
use AppBundle\Utilities\ArtisteUtilities;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Album controller.
 *
 * @Route("album")
 */
class AlbumController extends Controller
{
    /**
     * Lists all album entities.
     *
     * @Route("/", name="album_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('album_new');
    }

    /**
     * Creates a new album entity.
     *
     * @Route("/new", name="album_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, AlbumUtilities $utilities, ArtisteUtilities $artisteUtilities)
    {
        $album = new Album();
        $form = $this->createForm('AppBundle\Form\AlbumType', $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($utilities->existe($album->getTitre(), $album->getArtiste())){
                $this->addFlash('error', "L'album ".$album->getTitre()." de l'artiste ".$album->getArtiste()->getNom()." existe déjà.");
            }else{
                $em->persist($album);
                $em->flush();
                $artisteUtilities->ajoutAlbum($album->getArtiste());
                $this->addFlash('notice', "L'album ".$album->getTitre()." de l'artiste ".$album->getArtiste()->getNom()." a été ajouté avec succès!");
            }

            return $this->redirectToRoute('album_new');
        }

        $em = $this->getDoctrine()->getManager();
        $albums = $em->getRepository('AppBundle:Album')->findListASC();

        return $this->render('album/new.html.twig', array(
            'album' => $album,
            'albums' => $albums,
            'form' => $form->createView(),
            'current_page' => 'album'
        ));
    }

    /**
     * Finds and displays a album entity.
     *
     * @Route("/{id}", name="album_show")
     * @Method("GET")
     */
    public function showAction(Album $album)
    {
        $deleteForm = $this->createDeleteForm($album);

        return $this->render('album/show.html.twig', array(
            'album' => $album,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing album entity.
     *
     * @Route("/{slug}/edit", name="album_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Album $album)
    {
        $deleteForm = $this->createDeleteForm($album);
        $editForm = $this->createForm('AppBundle\Form\AlbumType', $album);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('album_new');
        }
        $em = $this->getDoctrine()->getManager();
        $albums = $em->getRepository('AppBundle:Album')->findListASC();

        return $this->render('album/edit.html.twig', array(
            'album' => $album,
            'albums' => $albums,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'current_page' => 'album',
        ));
    }

    /**
     * Deletes a album entity.
     *
     * @Route("/{id}", name="album_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Album $album, ArtisteUtilities $artisteUtilities)
    {
        $form = $this->createDeleteForm($album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($album);
            $em->flush();
            $artisteUtilities->supAlbum($album->getArtiste());
        }

        return $this->redirectToRoute('album_index');
    }

    /**
     * Creates a form to delete a album entity.
     *
     * @param Album $album The album entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Album $album)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('album_delete', array('id' => $album->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
