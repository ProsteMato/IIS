<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Post;
use App\Entity\PostUser;
use App\Entity\Thread;
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

    private function userLike($thread_id, $group_id, $post_id, $value_search, $twig, $twig_key) {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $threadRepository = $em->getRepository(Thread::class);
        $threadUserRepository = $em->getRepository(PostUser::class);
        $postRepository = $em->getRepository(Post::class);
        $groupRepository = $em->getRepository(Group::class);

        $liked = $threadUserRepository->findBy([
            'threads' => $threadRepository->find($thread_id),
            'group_list' => $groupRepository->find($group_id),
            'posts' => $postRepository->find($post_id),
            'users' => $user,
            "liked" => $value_search
        ]);

        return $this->render(
            $twig,
            [
                $twig_key => !empty($liked),
                "post_id" => $post_id
            ]
        );
    }

    public function userLiked($thread_id, $group_id, $post_id) {
        return $this->userLike($thread_id, $group_id, $post_id, "like", "post/like.html.twig", "liked");
    }

    public function userDisliked($thread_id, $group_id, $post_id) {
        return $this->userLike($thread_id, $group_id, $post_id, "dislike", "post/dislike.html.twig", "disliked");
    }

    /**
     * @Route("/group/show/{group_id}/thread/show/{thread_id}/post/{post_id}/liker", name="like_post")
     */
    public function liker($group_id, $thread_id, $post_id, Request $request) {
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

        $post_user = new PostUser();
        $post_user->setUsers($user);
        $post_user->setThreads($threadRepository->find($thread_id));
        $post_user->setGroupList($groupRepository->find($group_id));
        $post_user->setPosts($postRepository->find($post_id));
        $post_user->setLiked($action);

        $post = $postRepository->find($post_id);

        $record = $postUserRepository->findOneBy([
            'threads' => $threadRepository->find($thread_id),
            'group_list' => $groupRepository->find($group_id),
            'posts' => $post,
            'users' => $user
        ]);

        if ($record) {
            switch ($action) {
                case "like":
                    $record->setLiked($action);
                    $post->setRating($post->getRating() + 2);
                    break;
                case "unlike":
                    $em->remove($record);
                    $post->setRating($post->getRating() - 1);
                    break;
                case "dislike":
                    $record->setLiked($action);
                    $post->setRating($post->getRating() - 2);
                    break;
                case "undislike":
                    $em->remove($record);
                    $post->setRating($post->getRating() + 1);
                    break;
            }
        } else {
            $em->persist($post_user);
            if ($action == "like") {
                $post->setRating($post->getRating() + 1);
            } else if ($action == "dislike") {
                $post->setRating($post->getRating() - 1);
            }
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

        return new Response(
            json_encode([[
                "rating" => count($liked) - count($disliked)
            ]], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}


