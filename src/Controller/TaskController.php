<?php
namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TaskController extends AbstractController
{
    public function new(Request $request)
    {
        // creates a task and gives it some dummy data for this example
        $task = new Task();

        $form = $this->createFormBuilder($task)
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('select', ChoiceType::class, [
                'choices' => [
                    'Show' => 'show',
                    'Hide' => 'hide'
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('save', SubmitType::class, array('label' => 'Create Task'))
            ->getForm();

        $form->handleRequest($request);

        $data = array();

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $data['task'] = $task;

            // return $this->render('Task/index.html.twig', $data);
        }

        $data['form'] = $form->createView();
            
        return $this->render('Task/index.html.twig', $data);
    }
}