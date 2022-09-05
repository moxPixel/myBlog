<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArticleController extends AbstractController
{

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/article", name="app_article")
     */
    public function addArticle(Request $request, SluggerInterface $slugger): Response
    {

        $article = new Article(); // instantiation de l'entité Article

        $addArticle = $this->createForm(ArticleType::class, $article); // création du formulaire
        $addArticle->handleRequest($request); // récupération des données du formulaire

        if ($addArticle->isSubmitted() && $addArticle->isValid()) {
            $article->setPublishedAt(new \DateTime);
            $article->setAuthor($this->getUser()->getFullname());
            // UPLOAD IMAGE
            $brochureFile = $addArticle->get('picture')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                // je cree une variable qui sera le nouveau nom du fichier a uploader en bdd
                // Move the file to the directory where brochures are stored
                dd($brochureFile->getClientOriginalName());
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $article->setpicture($newFilename);
            }











            $this->manager->persist($article);
            $this->manager->flush();
        }


        return $this->render('article/index.html.twig', [
            'addArticle' => $addArticle->createView(), // createView() permet de créer la vue du formulaire
        ]);
    }



    /**
     * @Route("/article/single/{id}", name="app_single_article")
     */
    public function viewArticle($id): Response
    {

        $singleArticle = $this->manager->getRepository(Article::class)->findBy(['id' => $id]);
        // SELECT * FROM article WHERE article.id = $id;
        // On recupere l 'article qui a l'id qui correspond a l id de l url

        return $this->render('article/single.html.twig', [
            'singleArticle' => $singleArticle[0]
        ]);
    }



    /**
     * @Route("/article/remove/{id}", name="app_remove_article")
     */
    public function deleteArticle($id)
    {
        $singleArticle = $this->manager->getRepository(Article::class)->findBy(['id' => $id]);
        // DELETE * FROM article WHERE article.id = $id
        $this->manager->remove($singleArticle[0]);
        $this->manager->flush();

        return $this->redirectToRoute('app_home');
    }


    /**
     * @Route("/article/update/{id}", name="app_update_article")
     */
    public function updateArticle($id,Request $request)
    {
        // Je recupere dans la bdd l article grace a l'id
        $singleArticle = $this->manager->getRepository(Article::class)->findBy(['id' => $id]);
        // // J envoi la valeur de picture a null pour eviter tout probleme lors de la materialisation du formulaire
        $singleArticle[0]->setPicture(null);
        // Je materialise le formulaire et je donne l atricle recuperer en bdd a celui ci
        $form = $this->createForm(ArticleType::class,$singleArticle[0]);
        $form->handleRequest($request);

        // Si le formulaire et soumis et en meme temp valide alors j envoi les modification en bdd
        if($form->isSubmitted() && $form->isValid()){
            $this->manager->persist($singleArticle[0]);
            $this->manager->flush();
            // Pour finir je fait une redirection
            return $this->redirectToRoute('app_home');
        }


        return $this->render('article/update.html.twig', [
            'singleArticle' => $singleArticle[0],
            'form' => $form->createView()
        ]);
    }
}
