<?php

namespace Yoda\EventBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yoda\EventBundle\Entity\Event;
use Yoda\EventBundle\Form\EventType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Event controller.
 *
 */
class EventController extends Controller
{
    /**
     * Lists all Event entities.
     * @Template()
     * @Route("/", name="event")
     */
    public function indexAction()
    {
        // Get username
//        $user = $this->getUser();
//        var_dump($user->getUsername());die;

        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('EventBundle:Event')->findAll();

        return $this->render('event/index.html.twig', array(
            'events' => $events,
        ));

    }

//    /**
//     * Creates a new Event entity. IS NEVER CALLED
//     *
//     */
//    public function createAction(Request $request)
//    {
//        $this->enforceUserSecurity('ROLE_EVENT_CREATE');
//
//        $entity = new Event();
//        $form = $this->createCreateForm($entity);
//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
//            $user = $this->getUser();
//
//            $entity->setOwner($user);
//
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($entity);
//            $em->flush();
//            return $this->redirect($this->generateUrl('/{id}/show', array('id' => $entity->getId())));
//        }
//        return $this->render('EventBundle:Event:new.html.twig', array(
//            'entity' => $entity,
//            'form'   => $form->createView(),
//        ));
//    }

    /**
     * Creates a form to create a Event entity.
     *
     * @param Event $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('event_create'),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }

    /**
     * Creates a new Event entity.
     *
     */
    public function newAction(Request $request)
    {

        $this->enforceUserSecurity('ROLE_EVENT_CREATE');

        $event = new Event();
        $form = $this->createForm(new EventType(), $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $event->setOwner($user);
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return $this->render('event/new.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    private function enforceOwnerSecurity(Event $event)
    {
        $user = $this->getUser();

        if ($user != $event->getOwner()) {
            throw $this->createAccessDeniedException('You are not the owner!!!');
        }
    }

    /**
     * Finds and displays a Event entity.
     *
     */
    public function showAction(Event $event)
    {
        $this->enforceUserSecurity();

        $deleteForm = $this->createDeleteForm($event);

        return $this->render('event/show.html.twig', array(
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     */
    public function editAction(Request $request, Event $event)
    {
        $this->enforceUserSecurity();

        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createForm(new EventType(), $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_edit', array('id' => $event->getId()));
        }

        return $this->render('event/edit.html.twig', array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Event entity.
     *
     */
    public function deleteAction(Request $request, Event $event)
    {
        $this->enforceUserSecurity();

        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * Creates a form to delete a Event entity.
     *
     * @param Event $event The Event entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function enforceUserSecurity($role = 'ROLE_USER')
    {
       $securityContext = $this->container->get('security.authorization_checker');

        if (!$securityContext === $this->get('security.authorization_checker')->isGranted($role)) {
            throw $this->createAccessDeniedException('You do not have permission to access this! Contact your administrator.');
        }
    }
}
