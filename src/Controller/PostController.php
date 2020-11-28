<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Post;
use App\Entity\Thread;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/group/show/{group_id}/thread/show/{thread_id}/post/create", name="create_post")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request, $group_id, $thread_id): Response
    {
        $post = new Post();
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $threadRepository = $em->getRepository(Thread::class);

        $form = $this->createFormBuilder($post)
            ->setAction($this->generateUrl('create_post', [
                    "group_id" => $group_id,
                    "thread_id" => $thread_id
                ]))
            ->setMethod('POST')
            ->add('text', TextareaType::class, [
            'label' => "Create new post",
                'attr' => array(
                    'placeholder' => "What is on your mind?",
                    'style' => "height: 120px;"
                ),
                'required' => true
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Create post',
                'attr' => array(
                    'class' => 'btn btn-primary float-right'
                ),
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreationDate(new \DateTime('now'));
            $post->setCreatedBy($user);
            $post->setRating(0);
            $post->setThread($threadRepository->find($thread_id));
            $post->setPost(null);

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('group.thread.show', [
                'group_id' => $group_id,
                'thread_id' => $thread_id
            ]);
        }


        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
