<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{

    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHash)
    {
        $this->manager = $manager; // On injecte l'entity manager dans le constructeur
        $this->passwordHash = $passwordHash; // On injecte le service de hashage de mot de passe
    }


    /**
     * @Route("/register", name="app_register")
     */
    public function index(Request $request): Response // Request est une classe  qui permet en locurence de recuprer les donner d un formulaire
    {
        //-1 je vais instancier un nouveau user (importation de la classe User)
        $user = new User();
        //-2 je vais materialisé un formulaire (importation de la classe RegisterType)
        $registerForm = $this->createForm(RegisterType::class, $user); //createForm() est une méthode de AbstractController qui permet de materialisé un formulaire
        $registerForm->handleRequest($request); // handleRequest() est une méthode de AbstractController qui permet de traiter la requete

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            // dd($registerForm->getData()); // la fonction dd()  c'est comme le vardump() ! pour debuger
            $user->setCreatedAt(new \DateTime); // on set la date de création de l'utilisateur

            $passwordEncod = $this->passwordHash->hashPassword($user , $user->getPassword()); // on hash le mot de passe de l'utilisateur
            $user->setPassword($passwordEncod); // on set le mot de passe de l'utilisateur
            $user->setFullname($user->getLastname() . ' ' . $user->getfirstname()); // on set le fullname de l'utilisateur
            $this->manager->persist($user); // On prepare les donnée a etre envoyer ($user)
            $this->manager->flush();  // On envoie les donnée dans la base de donnée
        }

        return $this->render('register/index.html.twig', [
            'registerForm' => $registerForm->createView(), //createView() est une méthode de AbstractController qui permet de créer une vue du formulaire
        ]);
    }
}
