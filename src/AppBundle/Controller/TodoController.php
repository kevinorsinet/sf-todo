<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use AppBundle\Form\TodoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TodoController
 * @package AppBundle\Controller
 */
class TodoController extends Controller
{
    /**
     * Index action.
     */
    public function indexAction()
    {

        $todos = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findBy(['trashed' => false],
                    ['date' => 'DESC']
                );

        $form = $this
            ->createForm(new TodoType(), null, 
                ['action' => $this->generateUrl('todo_add')]);
        

        return $this->render('AppBundle:Todo:_default_list.html.twig', [
            'form' => $form->createView(),
            'todo' => $todos

        ]);
    }


    public function addAction(Request $request){

        $todo = new Todo();

        $form = $this
            ->createForm(new TodoType(), $todo);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush($todo);

            

            return $this->redirectToRoute('todo_index');
        }

    return $this->render('AppBundle:Todo:index.html.twig', [
            'todo' => $todo,
            'form' => $form->createView(),
        ]);
    }



       public function editAction(Request $request)
    {
        $todo = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($request->attributes->get('id'));

        $formAction = $this->generateUrl('todo_edit', [
            'id' => $todo->getId(),
        ]);

        $form = $this
            ->createForm(new TodoType(), $todo, [
                'action' => $formAction,
            ])
            ->add('submit', 'submit', [
                'label' => 'Modifier',
                'attr'  => [
                'class' => 'btn btn-warning'
                ]
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

          

             return $this->redirectToRoute('todo_index');
        }

        $todos = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findBy([], ['date' => 'DESC']);

        return $this->render('AppBundle:Todo:index.html.twig', [
            'todos' => $todos,
            'form' => $form->createView(),
        ]);

    }


    public function trashAction(Request $request){

    $todo = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($request->attributes->get('id'));

    $todo->setTrashed(true);  
    $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

    return $this->redirectToRoute('todo_index');


    }


    public function listTrashedAction(){

            $todos = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findBy(['trashed' => true],
                    ['date' => 'DESC']
                );

        $form = $this
            ->createForm(new TodoType(), null, 
                ['action' => $this->generateUrl('todo_add')]);
        

        return $this->render('AppBundle:Todo:_trashed_list.html.twig', [
            'form' => $form->createView(),
            'todo' => $todos

        ]);

    }


    public function restore(){


    }


    public function remove(){

    }


}
