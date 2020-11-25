<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
    * @Route("/", name = "main_page_unlogged", methods={"GET"})
    */
    public function unlogged(Request $request, EntityManagerInterface $entityManager){
        // TODO: zobrazenie recently updated groups a najnovsie prispevky od users
        /**$search_val = $request->query->get('search_val');
        if ( $search_val )
        {
            $search = new SearchController();
            $users =  $search->search_users($request, $entityManager, $search_val);
            $groups = $search->search_groups($request, $entityManager, $search_val);
            dump($groups);
            return $this->render('common/search.html.twig', ['search_val' => $search_val,
                'users' => $users,
                'groups' => $groups,
            ]);
        }
*/
        return $this->render('unlogged/index.html.twig', [
            'loggedUser' => null    
        ]);
    }




}
