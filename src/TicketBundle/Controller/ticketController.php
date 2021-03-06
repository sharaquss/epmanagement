<?php

namespace TicketBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use TicketBundle\Entity\ticket;
use TicketBundle\Form\ticketType;
use CustomMailerBundle\Controller\DefaultController;
use TicketBundle\Repository\ticketRepository;
use AestheticBundle\Containers\BootstrapNavbarElements;
use AestheticBundle\Containers\BootstrapNavbar;
use AestheticBundle\Containers\NavbarHelperElements;
/**
 * ticket controller.
 *
 */
class ticketController extends Controller
{
    private $session;

    /**
     * Lists all ticket entities.
     */
    public function indexAction() {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $em->getRepository('TicketBundle:ticket');


        if($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $upcomingTicketsVisible = $repository->getTicketsUpcomingAdmin();
            $overdueTicketsVisible = $repository->getTicketsOverdueAdmin();
            $doneTicketsVisible = $repository->getTicketsDoneAdmin();
        } else {
            $upcomingTicketsVisible = $repository->getTicketsUpcoming($this->getUser());
            $overdueTicketsVisible = $repository->getTicketsOverdue($this->getUser());
            $doneTicketsVisible = $repository->getTicketsDone($this->getUser());
        }

        $doneCount = count($doneTicketsVisible);
        $upcomingCount = count($upcomingTicketsVisible);
        $overdueCount = count($overdueTicketsVisible);

        if(($doneCount + $upcomingCount + $overdueCount) == 0)
            $percent = 0;
        else
            $percent = ( $doneCount / ($doneCount + $upcomingCount + $overdueCount) ) * 100;

        $progressbarText = "Tickets Completed: " . number_format($percent, 0) . "% (" . $doneCount . "/" . ($doneCount + $upcomingCount + $overdueCount) . ")";


        return $this->render('TicketBundle:Ticket:index.html.twig', array(
            'helper' => (new NavbarHelperElements())->createHelperIndex(),
            'navbarLeft' => (new BootstrapNavbar())->createNavbarIndexLeft(),
            'navbarRight' => (new BootstrapNavbar())->createNavbarStandardRight(),
            'progressbarText' => $progressbarText,
            'progressbarValue' => $percent,
            'completedTickets' => $doneTicketsVisible,
            'upcomingTickets' => $upcomingTicketsVisible,
            'overdueTickets' => $overdueTicketsVisible,
            'controllerAction' => 'indexAction()'
        ));
    }

    public function firstIndexAction() {
        $this->session = new Session();
        $this->session->getFlashBag()->add('flash_success', 'Good to see you back! See all of your tickets below...');
//        /** @var EntityManager $em */
//        $em = $this->getDoctrine()->getManager();
//
//        /** @var \Doctrine\ORM\EntityRepository $repository */
//        $repository = $em->getRepository('TicketBundle:ticket');
//
//
//        $upcomingTicketsVisible = $repository->findAllUpcomingTicketsUser($this->getUser());
//        $overdueTicketsVisible = $repository->findAllOverdueTicketsUser($this->getUser());
//
//
//        return $this->render('TicketBundle:Ticket:index.html.twig', array(
//            'helper' => (new NavbarHelperElements())->createHelperIndex(),
//            'navbarLeft' => (new BootstrapNavbar())->createNavbarIndexLeft(),
//            'navbarRight' => (new BootstrapNavbar())->createNavbarStandardRight(),
//            'upcomingTickets' => $upcomingTicketsVisible,
//            'overdueTickets' => $overdueTicketsVisible,
//            'controllerAction' => 'indexAction()'
//        ));
        return $this->indexAction();
    }



//    public function sortedAction() {
//    }




    public function showAction($slug) {

        $manager = $this->getDoctrine()->getManager();
        $ticket = $manager->getRepository('TicketBundle:ticket')->findOneBy(array('slug' => $slug));
        $form = $this->createDeleteForm($ticket);
        if(!$ticket) throw $this->createNotFoundException('Unable to find given ticket');


        return $this->render('TicketBundle:Ticket:show.html.twig', array(
            'helper' => (new NavbarHelperElements())->createHelperShow(),
            'navbarLeft' => (new BootstrapNavbar())->createNavbarShowLeft(),
            'navbarRight' => (new BootstrapNavbar())->createNavbarStandardRight(),
            'ticket' => $ticket,
            'delete_form' => $form->createView()
        ));
    }


    public function newAction(Request $request) {

        $ticket = new ticket();
        $form = $this->createForm(new ticketType(), $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setUserCreated($this->getUser());
            $ticket->setDateCreation(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();

            $this->session = new Session();
            $this->session->getFlashBag()
                ->add('flash_success', 'Congratulations on successfully creating a new ticket!');

            $this->get('custommailerservice')->sendNewTicketMail($this->getUser(), $ticket);

            return $this->redirectToRoute('ticketcrud_show', array('slug' => $ticket->getSlug()));
        }

        if($form->isSubmitted() && !$form->isValid()) {
            $this->session = new Session();
            $this->session->getFlashBag()
                ->add('flash_error', 'There are some errors in form data we have received. Please fix them.');
        }

        return $this->render('TicketBundle:Ticket:new.html.twig', array(
            'helper' => (new NavbarHelperElements())->createHelperNew(),
            'navbarLeft' => (new BootstrapNavbar())->createNavbarNewLeft(),
            'navbarRight' => (new BootstrapNavbar())->createNavbarStandardRight(),
            'ticket' => $ticket,
            'form' => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing ticket entity.
     *
     */
    public function editAction(Request $request, ticket $ticket)
    {
        $deleteForm = $this->createDeleteForm($ticket);
        $editForm = $this->createForm(new ticketType(), $ticket);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();
            $this->session = new Session();
            $this->session->getFlashBag()
                ->add('flash_success', 'Your ticket was succesfully altered and changes were saved. Your ticket now looks like this:');

            return $this->redirectToRoute('ticketcrud_show', array('slug' => $ticket->getSlug()));
        }
        else if ($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->session = new Session();
            $this->session->getFlashBag()
                ->add('flash_error', 'Something went wrong with processing your edit. Try again, please!');
        }



        return $this->render('TicketBundle:Ticket:edit.html.twig', array(
            'helper' => (new NavbarHelperElements())->createHelperEdit(),
            'navbarLeft' => (new BootstrapNavbar())->createNavbarEditLeft(),
            'navbarRight' => (new BootstrapNavbar())->createNavbarStandardRight(),
            'ticket' => $ticket,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a ticket entity.
     *
     */
    public function deleteAction(Request $request, ticket $ticket)
    {
        $form = $this->createDeleteForm($ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ticket);
            $em->flush();
        }

        return $this->redirectToRoute('ticketcrud_index');
    }

    /**
     * Creates a form to delete a ticket entity.
     *
     * @param ticket $ticket The ticket entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ticket $ticket)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ticketcrud_delete', array('id' => $ticket->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }




    public function completionAction($id, $format) {
        $manager = $this->getDoctrine()->getManager();
        $ticket = $manager->getRepository('TicketBundle:ticket')->find($id);

        if(!$ticket) throw $this->createNotFoundException('ticket not found :c');

        $ticket->setDone(1);

        $manager->persist($ticket);
        $manager->flush();

        return $this->completionSet($ticket, $format);

    }

    public function unCompletionAction($id, $format) {
        $manager = $this->getDoctrine()->getManager();
        $ticket = $manager->getRepository('TicketBundle:ticket')->find($id);

        if(!$ticket) throw $this->createNotFoundException('ticket not found :c');

        $ticket->setDone(false);
        $manager->persist($ticket);
        $manager->flush();

        return $this->completionSet($ticket, $format);

    }

    public function completionSet(ticket $ticket, $format) {

        if ($format == 'json') {
            $json = array(
                'completed' => $ticket->isDoneBoolean()
            );
            return new JsonResponse($json);
        } else
            return $this->redirect($this->generateUrl('ticketcrud_show', array( 'slug' => $ticket->getSlug())));
    }

}
