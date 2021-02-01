<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Form\ProjectForStudentType;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/project")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="project_index", methods={"GET"})
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        // filtrage des utilisateurs non autorisés
        // une négation de « ou »
        // if (!(
        //         $this->isGranted('ROLE_ADMIN')
        //         || $this->isGranted('ROLE_TEACHER')
        //         || $this->isGranted('ROLE_STUDENT')
        //         || $this->isGranted('ROLE_CLIENT')
        // )){
        //     throw new AccessDeniedException();
        // }

        // filtrage des utilisateurs non autorisés
        // des négations de « et »
        if (!$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_TEACHER')
            && !$this->isGranted('ROLE_STUDENT')
            && !$this->isGranted('ROLE_CLIENT')
        ) {
            throw new AccessDeniedException();
        }

        // affichage des projets possédés par l'utilisateur
        if ($this->isGranted('ROLE_STUDENT')
            || $this->isGranted('ROLE_CLIENT')
        ) {
            // projets de l'utilisateur
            $projects = $this->getUser()->getProjects();
        } else {
            // tous les projets
            $projects = $projectRepository->findAll();
        }

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/new", name="project_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        // filtrage des utilisateurs non autorisés
        // une négation de « ou »
        // if (!(
        //         $this->isGranted('ROLE_ADMIN')
        //         || $this->isGranted('ROLE_TEACHER')
        // )){
        //     throw new AccessDeniedException();
        // }

        // filtrage des utilisateurs non autorisés
        // des négations de « et »
        if (!$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_TEACHER')
        ) {
            throw new AccessDeniedException();
        }

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_show", methods={"GET"})
     */
    public function show(Project $project): Response
    {
        // filtrage des utilisateurs non autorisés
        // une négation de « ou »
        // if (!(
        //         $this->isGranted('ROLE_ADMIN')
        //         || $this->isGranted('ROLE_TEACHER')
        //         || $this->isGranted('ROLE_STUDENT')
        //         || $this->isGranted('ROLE_CLIENT')
        // )){
        //     throw new AccessDeniedException();
        // }

        // filtrage des utilisateurs non autorisés
        // des négations de « et »
        if (!$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_TEACHER')
            && !$this->isGranted('ROLE_STUDENT')
            && !$this->isGranted('ROLE_CLIENT')
        ) {
            throw new AccessDeniedException();
        }

        // affichage des projets possédés par l'utilisateur
        if ($this->isGranted('ROLE_STUDENT')
            || $this->isGranted('ROLE_CLIENT')
        ) {
            $projects = $this->getUser()->getProjects();

            if (!$projects->contains($project)) {
                throw new AccessDeniedException();
            }
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Project $project): Response
    {
        // filtrage des utilisateurs non autorisés
        // une négation de « ou »
        // if (!(
        //         $this->isGranted('ROLE_ADMIN')
        //         || $this->isGranted('ROLE_TEACHER')
        // )){
        //     throw new AccessDeniedException();
        // }

        // filtrage des utilisateurs non autorisés
        // des négations de « et »
        if (!$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_TEACHER')
        ) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit-for-student", name="project_edit_for_student", methods={"GET","POST"})
     */
    public function editForStudent(Request $request, Project $project): Response
    {
        // filtrage des utilisateurs non autorisés
        // une négation de « ou »
        // if (!(
        //         $this->isGranted('ROLE_ADMIN')
        //         || $this->isGranted('ROLE_TEACHER')
        //         || $this->isGranted('ROLE_STUDENT')
        // )){
        //     throw new AccessDeniedException();
        // }

        // filtrage des utilisateurs non autorisés
        // des négations de « et »
        if (!$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_TEACHER')
            && !$this->isGranted('ROLE_STUDENT')
        ) {
            throw new AccessDeniedException();
        }

        // affichage des projets possédés par l'utilisateur
        if ($this->isGranted('ROLE_STUDENT')) {
            $projects = $this->getUser()->getProjects();

            if (!$projects->contains($project)) {
                throw new AccessDeniedException();
            }
        }

        $form = $this->createForm(ProjectForStudentType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Project $project): Response
    {
        // filtrage des utilisateurs non autorisés
        // une négation de « ou »
        // if (!(
        //         $this->isGranted('ROLE_ADMIN')
        //         || $this->isGranted('ROLE_TEACHER')
        // )){
        //     throw new AccessDeniedException();
        // }

        // filtrage des utilisateurs non autorisés
        // des négations de « et »
        if (!$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_TEACHER')
        ) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }
}
