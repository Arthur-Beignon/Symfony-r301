<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Article; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ArticleType;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }


    #[Route('/article/generate', name: 'generate_article')] 
    public function generateArticlet(EntityManagerInterface $entityManager): Response 
    { 
        $article = new Article();
        $str_now = date('Y-m-d H:i:s', time()); 
        $article->setTitre('Titre aleatoire #'.$str_now); 
        $content = file_get_contents('http://loripsum.net/api'); 
        $article->setTexte($content); 
        $article->setPublie(true); 
        $article->setDate(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $str_now));
        $entityManager->persist($article);  
        $entityManager->flush();
        return new Response('Saved new article with id '.$article->getId());
    }
    #[Route('/article/list', name: 'list_article')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager
            ->getRepository(Article::class)
            ->findAll();

            return $this->render('article/list.html.twig', [
                'articles' => $article, 
            ]);
    }

    #[Route('/article/show/{id}', name: 'article_show')]
    public function show(EntityManagerInterface $entityManager, string $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find((int)$id);
        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id ' . $id
            );
        }

        $this->addFlash('success', 'Article loaded!');

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/new', name: 'article_new')]
    public function new(): Response
    {
        // Crée un objet Article et initialise quelques données pour cet exemple
        $article = new Article();
        $article->setTitre('Which Title ?');
        $article->setTexte('And which content ?');

        // Définit la date actuelle en format DateTimeImmutable
        $now = new \DateTimeImmutable();

        // Assigne la date à l'article
        $article->setDate($now);

        // Crée le formulaire associé à l'article
        $form = $this->createForm(ArticleType::class, $article);

        // Rend la vue en passant le formulaire et l'article
        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/article/edit/{id}', name: 'edit_article', requirements: ['id' => '\d+'])]
    public function edit(EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        // Recherche l'article dans la base de données
        $article = $entityManager->getRepository(Article::class)->find($id);

        // Vérifie si l'article existe
        if (!$article) {
            throw $this->createNotFoundException('L\'article avec l\'ID '.$id.' n\'existe pas.');
        }

        // Crée le formulaire associé à l'article
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, enregistre les modifications
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return new Response('Article mis à jour avec succès : '.$article->getTitre());
        }

        // Rend la vue avec le formulaire
        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/article/delete/{id}', name: 'delete_article', requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        // Recherche l'article dans la base de données
        $article = $entityManager->getRepository(Article::class)->find($id);

        // Si l'article n'existe pas, retourne un message clair
        if (!$article) {
            return new Response('L\'article avec l\'ID '.$id.' n\'existe pas.', Response::HTTP_NOT_FOUND);
        }

        // Supprime l'article
        $entityManager->remove($article);
        $entityManager->flush();

        // Retourne une confirmation simple
        return new Response('Article supprimé avec succès : '.$id);
    }
}