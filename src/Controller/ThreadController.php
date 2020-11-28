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
        $posts = $thread->getPosts();
        return $this->render('thread/show.html.twig', [
                'thread' => $thread,
                'posts' => $posts,
                'loggedUser' => $this->getUser()
            ]);
    }

    private function userLike($thread_id, $group_id, $value_search, $twig, $twig_key) {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $threadRepository = $em->getRepository(Thread::class);
        $threadUserRepository = $em->getRepository(ThreadUser::class);
        $groupRepository = $em->getRepository(Group::class);

        $liked = $threadUserRepository->findBy([
            'threads' => $threadRepository->find($thread_id),
            'group_list' => $groupRepository->find($group_id),
            'users' => $user,
            "liked" => $value_search
        ]);

        return $this->render(
            $twig,
            [
                $twig_key => !empty($liked),
                "thread_id" => $thread_id
            ]
        );
    }

    public function userLiked($thread_id, $group_id) {
        return $this->userLike($thread_id, $group_id, "like", "thread/like.html.twig", "liked");
    }

    public function userDisliked($thread_id, $group_id) {
        return $this->userLike($thread_id, $group_id, "dislike", "thread/dislike.html.twig", "disliked");
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

        $thread_user = new ThreadUser();
        $thread_user->setUsers($user);
        $thread_user->setThreads($threadRepository->find($thread_id));
        $thread_user->setGroupList($groupRepository->find($group_id));
        $thread_user->setLiked($action);

        $thread = $threadRepository->find($thread_id);

        $record = $threadUserRepository->findOneBy([
            'threads' => $thread,
            'group_list' => $groupRepository->find($group_id),
            'users' => $user
        ]);

        if ($record) {
            switch ($action) {
                case "like":
                    $record->setLiked($action);
                    $thread->setRating($thread->getRating() + 2);
                    break;
                case "unlike":
                    $em->remove($record);
                    $thread->setRating($thread->getRating() - 1);
                    break;
                case "dislike":
                    $record->setLiked($action);
                    $thread->setRating($thread->getRating() - 2);
                    break;
                case "undislike":
                    $em->remove($record);
                    $thread->setRating($thread->getRating() + 1);
                    break;
            }
        } else {
            $em->persist($thread_user);
            if ($action == "like") {
                $thread->setRating($thread->getRating() + 1);
            } else if ($action == "dislike") {
                $thread->setRating($thread->getRating() - 1);
            }
        }

        $em->flush();

        $liked = $threadUserRepository->findBy([
            'threads' => $threadRepository->find($thread_id),
            'group_list' => $groupRepository->find($group_id),
            'liked' => "like"
        ]);

        $disliked = $threadUserRepository->findBy([
            'threads' => $threadRepository->find($thread_id),
            'group_list' => $groupRepository->find($group_id),
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
