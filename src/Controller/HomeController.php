<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Service\Random;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Random $rnd, SessionInterface $session): Response
    {
        // affectation d'une valeur si la clé n'existe pas dans la variable d'environnement
        if (!$session->has('foo')) {
            $session->set('foo', 123);
        }

        // récupération de la valeur associée à la clé
        $foo = $session->get('foo');

        // exemple d'utilisation d'un service
        $number = $rnd->getInt();
        $projects = $rnd->getProjects();
        $meteoUrl = $rnd->getMeteoUrl();

        $session->getFlashBag()->add(
            'info',
            'lorem ipsum'
        );

        // affichage d'un template statique
        return $this->render('home/index.html.twig', [
            'foo' => $foo,
        ]);

        // ou redirection vers une autre route de l'appli
        // return $this->redirectToRoute('project_new');
    }

    /**
     * @Route("/session1/{name}")
     */
    public function session1(SessionInterface $session, string $name)
    {
        $session->set('name', $name);
        dump($name);
        exit();
    }

    /**
     * @Route("/session2")
     */
    public function session2(SessionInterface $session)
    {
        $name = $session->get('name');
        dump($name);
        exit();
    }

    /**
     * @Route("/caddie")
     */
    public function caddie(SessionInterface $session, ProjectRepository $repository)
    {
        if (!$session->has('caddie')) {
            $session->set('caddie', []);
        }

        // voir version alternative ci-dessous
        $projects = [];

        foreach ($session->get('caddie') as $id) {
            $project = $repository->find($id);
            $projects[] = $project;
        }

        // fait la même chose que la boucle foreach précédente
        $projects = array_map(function($id) use ($repository) {
            return $repository->find($id);
        }, $session->get('caddie'));

        // normalement ceci est à faire dans twig
        foreach ($projects as $project) {
            dump($project->getName());
        }

        exit();
    }

    /**
     * @Route("/caddie/add/{id}")
     */
    public function caddieAdd(SessionInterface $session, Project $project)
    {
        if (!$session->has('caddie')) {
            $session->set('caddie', []);
        }

        $caddie = $session->get('caddie');
        $caddie[] = $project->getId();
        $session->set('caddie', $caddie);

        exit();
    }

    /**
     * @Route("/caddie/remove/{id}")
     */
    public function caddieRemove(SessionInterface $session, int $id)
    {
        if (!$session->has('caddie')) {
            $session->set('caddie', []);
        }

        $caddie = $session->get('caddie');

        // voir version alternative avec array_filter() ci-dessous
        $caddieTmp = [];

        foreach ($caddie as $projectId) {
            if ($projectId != $id) {
                $caddieTmp[] = $projectId;
            }
        }

        $caddie = $caddieTmp;

        // fait la même chose que la boucle foreach précédente
        // $caddie = array_filter($caddie, function($projectId) use ($id) {
        //     if ($projectId == $id) {
        //         return false;
        //     }

        //     return true;
        // });

        $session->set('caddie', $caddie);

        dump($caddie);

        exit();
    }
}
