<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController // classe HomeController qui hérite de la classe AbstractController
{

    public function __construct(EntityManagerInterface $manager){
            $this->manager = $manager;
    }

    // ceci est une annotation elle est lu au meme titre que le reste
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response //ici on retourne une reponse donc un return
    {
        // va en bdd est recupere moi les donner de article
            $articles = $this->manager->getRepository(Article::class)->findAll(); // findAll() -> recupere moi tout


        return $this->render(
            'home/index.html.twig',  // la fonction render renvoi une vue a l internaute
            [
                'articles' => $articles, // ici on passe des variables a la vue pour y acceder dans celle ci
            ]
        );
    }



    /**
     * @Route("/redirect", name="app_redirect")
     */
    public function redirectToUser()  //ici on retourne une reponse donc un return
    {
        // recuperer le role de l utilisateur
        // si il a le role admin alor on le redirige vers dashboard sinon vers account
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_dashboard');
        }else{
            return $this->redirectToRoute('app_account');
        }
// La fonction $this->getUser() permet de recuperer l utilisateur connecté coter php

        // if ($this->getUser()->getRole() == 'Admin') {
        //     return $this->redirectToRoute('app_dashboard');
        // }else{
        //     return $this->redirectToRoute('app_account');
        // }
    }
}
