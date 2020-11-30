<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Post;
use App\Entity\PostUser;
use App\Entity\Thread;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/group/show/{group_id}/thread/show/{thread_id}/post/create", name="create_post")
     * @param Request $request
     * @param $group_id
     * @param $thread_id
     * @return Response
     */
    public function create(Request $request, $group_id, $thread_id): Response
    {
        $post = new Post();
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $threadRepository = $em->getRepository(Thread::class);
        $group = $em->getRepository(Group::class)->find($group_id);

        $this->denyAccessUnlessGranted("GROUP_MEMBER", [$group, $this->getUser()]);

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

    /**
     * @Route("/group/show/{group_id}/thread/show/{thread_id}/post/{post_id}/edit", name="edit_post")
     *
     */
    public function edit($group_id, $thread_id, $post_id, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $post = $this->getDoctrine()->getRepository(Post::class)->find($post_id);
        $this->denyAccessUnlessGranted("OWNER", $post);

        $form = $this->createFormBuilder($post)
            ->setAction($this->generateUrl('edit_post', [
                "group_id" => $group_id,
                "thread_id" => $thread_id,
                "post_id" => $post_id
            ]))
            ->setMethod('POST')
            ->add('text', TextareaType::class, [
                'label' => "Edit post",
                'attr' => array(
                    'placeholder' => "Text to be saved...",
                ),
                'required' => true
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save post',
                'attr' => array(
                    'class' => 'btn btn-primary float-right'
                ),
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Post $post */
            $post = $form->getData();
            $post->setCreationDate(new \DateTime('now'));
            $em->persist($post);
            $em->flush();

            return new JsonResponse(
                [
                    'date' => $post->getStringCreationDate(),
                    'message' => $post->getText()
                ], 200);
        }

        return new JsonResponse(
            [
                'message' => 'Success',
                'form' => $this->renderView('post/create.html.twig',
                [
                    'form' => $form->createView(),
                ])
            ], 200);
    }

    /**
     * @Route("/group/show/{group_id}/thread/show/{thread_id}/post/{post_id}/delete", name="delete_post")
     *
     */
    public function delete($group_id, $thread_id, $post_id){
        $em = $this->getDoctrine()->getManager();
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        $post = $postRepository->find($post_id);

        $this->denyAccessUnlessGranted("OWNER", $post);

        $post_likes = $post->getPostUsers();

        $em->remove($post);
        foreach ($post_likes as $like) {
            $em->remove($like);
        }
        $em->flush();

        return new JsonResponse(
            [
                "postCount" => $em->getRepository(Thread::class)->find($thread_id)->getPostsCount()
            ],
            Response::HTTP_OK
        );
    }

    public function userLike($thread_id, $group_id, $post_id) {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $threadRepository = $em->getRepository(Thread::class);
        $threadUserRepository = $em->getRepository(PostUser::class);
        $postRepository = $em->getRepository(Post::class);
        $groupRepository = $em->getRepository(Group::class);

        $thread = $threadRepository->find($thread_id);
        $group = $groupRepository->find($group_id);
        $post = $postRepository->find($post_id);

        $liked = $threadUserRepository->findBy([
            'threads' => $thread,
            'group_list' => $group,
            'posts' => $post,
            'users' => $user,
            "liked" => "like"
        ]);

        $disliked = $threadUserRepository->findBy([
            'threads' => $thread,
            'group_list' => $group,
            'posts' => $post,
            'users' => $user,
            "liked" => "dislike"
        ]);

        return $this->render(
            "post/ratings.html.twig",
            [
                "liked" => !empty($liked),
                "disliked" => !empty($disliked),
                "post_id" => $post_id
            ]
        );
    }

    /**
     * @Route("/group/show/{group_id}/thread/show/{thread_id}/post/{post_id}/liker", name="like_post")
     */
    public function liker($group_id, $thread_id, $post_id, Request $request) {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if (strpos($request->headers->get('Content-Type'), 'application/json') === 0) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }

        $action = $request->request->get("action");
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $threadRepository = $em->getRepository(Thread::class);
        $postUserRepository = $em->getRepository(PostUser::class);
        $groupRepository = $em->getRepository(Group::class);
        $postRepository = $em->getRepository(Post::class);

        $thread = $threadRepository->find($thread_id);
        $group = $groupRepository->find($group_id);
        $post = $postRepository->find($post_id);

        $post_user = new PostUser();
        $post_user->setUsers($user);
        $post_user->setThreads($thread);
        $post_user->setGroupList($group);
        $post_user->setPosts($post);
        $post_user->setLiked($action);

        $record = $postUserRepository->findOneBy([
            'threads' => $thread,
            'group_list' => $group,
            'posts' => $post,
            'users' => $user
        ]);

        if ($record) {
            switch ($action) {
                case "like":
                case "dislike":
                    $record->setLiked($action);
                    break;
                case "unlike":
                case "undislike":
                    $em->remove($record);
                    break;
            }
        } else {
            $em->persist($post_user);
        }
        $em->flush();

        $liked = $postUserRepository->findBy([
            'threads' => $threadRepository->find($thread_id),
            'group_list' => $groupRepository->find($group_id),
            'posts' => $postRepository->find($post),
            'liked' => "like"
        ]);

        $disliked = $postUserRepository->findBy([
            'threads' => $threadRepository->find($thread_id),
            'group_list' => $groupRepository->find($group_id),
            'posts' => $postRepository->find($post),
            'liked' => "dislike"
        ]);

        $count = count($liked) - count($disliked);
        $post->setRating($count);
        $em->flush();

        return new Response(
            json_encode([[
                "rating" => $count
            ]], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}


