<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Thread;
use App\Entity\ThreadUser;
use App\Entity\User;
use App\Form\ThreadType;
use App\Repository\GroupRepository;
use App\Repository\ThreadRepository;
use App\Repository\ThreadUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/group/show/{group_id}/thread", name="group.thread.")
 */
class ThreadController extends AbstractController
{
    /**
     * @Route("/show/{thread_id}", name="show")
     * @param $thread_id
     * @return Response
     */
    public function show($thread_id) {
        $em = $this->getDoctrine()->getManager();
        $threadRepository = $em->getRepository(Thread::class);
        $thread = $threadRepository->find($thread_id);
        $thread->addView();
        $em->persist($thread);
        $em->flush();
        $posts = $thread->getPosts();
        return $this->render('thread/show.html.twig', [
                'thread' => $thread,
                'posts' => $posts,
                'loggedUser' => $this->getUser()
            ]);
    }
    /**
     *  @Route("/{thread_id}/delete", name="delete")
     */
    public function delete($group_id, $thread_id) {
        $em = $this->getDoctrine()->getManager();
        $threadRepository = $em->getRepository(Thread::class);

        /** @var Thread $thread */
        $thread = $threadRepository->find($thread_id);

        $posts = $thread->getPosts();
        $likes_thread = $thread->getThreadUsers();
        $posts_likes = $thread->getPostUsers();

        foreach ($posts as $post) {
            $em->remove($post);
        }

        foreach ($likes_thread as $likes) {
            $em->remove($likes);
        }

        foreach ($posts_likes as $likes) {
            $em->remove($likes);
        }

        $em->remove($thread);
        $em->flush();

        return $this->redirectToRoute("show_group", [
                "group_id" => $group_id
            ]);

    }

    public function userLike($thread_id, $group_id) {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $threadRepository = $em->getRepository(Thread::class);
        $threadUserRepository = $em->getRepository(ThreadUser::class);
        $groupRepository = $em->getRepository(Group::class);

        $thread = $threadRepository->find($thread_id);
        $group = $groupRepository->find($group_id);

        $liked = $threadUserRepository->findBy([
            'threads' => $thread,
            'group_list' => $group,
            'users' => $user,
            "liked" => "like"
        ]);

        $disliked = $threadUserRepository->findBy([
            'threads' => $thread,
            'group_list' => $group,
            'users' => $user,
            "liked" => "dislike"
        ]);


        return $this->render(
            "thread/ratings.html.twig",
            [
                "liked" => !empty($liked),
                "disliked" => !empty($disliked),
                "rating" => $thread->getRating(),
                "thread_id" => $thread_id,
                "group_id" => $group_id
            ]
        );
    }

    /**
     * @Route("/show/{thread_id}/liker", name="like_thread")
     */
    public function liker($group_id, $thread_id, Request $request) {
        if (strpos($request->headers->get('Content-Type'), 'application/json') === 0) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }

        $action = $request->request->get("action");
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $threadRepository = $em->getRepository(Thread::class);
        $threadUserRepository = $em->getRepository(ThreadUser::class);
        $groupRepository = $em->getRepository(Group::class);


        $thread = $threadRepository->find($thread_id);
        $group = $groupRepository->find($group_id);

        $thread_user = new ThreadUser();
        $thread_user->setUsers($user);
        $thread_user->setThreads($thread);
        $thread_user->setGroupList($group);
        $thread_user->setLiked($action);

        $record = $threadUserRepository->findOneBy([
            'threads' => $thread,
            'group_list' => $group,
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
            $em->persist($thread_user);
        }
        $em->flush();

        $liked = $threadUserRepository->findBy([
            'threads' => $thread,
            'group_list' => $group,
            'liked' => "like"
        ]);

        $disliked = $threadUserRepository->findBy([
            'threads' => $thread,
            'group_list' => $group,
            'liked' => "dislike"
        ]);

        $rating = count($liked) - count($disliked);

        $thread->setRating($rating);
        $em->flush();


        return new Response(
            json_encode([[
                "rating" => $rating
            ]], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}
