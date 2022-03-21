<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Formation;
use App\Entity\Employe;
use App\Form\InscriptionType;
use App\Form\InscriptionTypeAdmin;
use App\Form\InscriptionTypeAdminAccepter;
use App\Repository\InscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/inscription")
 */
class InscriptionController extends AbstractController
{
    /**
     * @Route("/", name="inscription_index", methods={"GET"})
     */
    public function index(InscriptionRepository $inscriptionRepository): Response
    {
        return $this->render('inscription/index.html.twig', [
            'inscriptions' => $inscriptionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="inscription_new", methods={"GET", "POST"})
     */
    public function new(FormationRepository $formationRepository, InscriptionRepository $inscriptionRepository, $id): Response
    {
        $inscription = new Inscription();

        $idEmp = $this->get('session')->get('employeId');

        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($id);
        $employe = $this->getDoctrine()->getRepository(Employe::class)->find($idEmp);
        $exist = $this->getDoctrine()->getRepository(Inscription::class)->findBy(
            [
                'employe' => $employe,
                'formation' => $formation
            ],
            [],
            1
        );
        if ($exist == null) {
            $inscription = new Inscription();
            $inscription->setStatut('E');
            $inscription->setEmploye($employe);
            $inscription->setFormation($formation);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($inscription);
            $manager->flush();
        } else {
            echo 'Vous ètes déjà inscrit à cette formation';
        }
        return $this->render('formation/employe.html.twig', [
            'formations' => $formationRepository->findAll(),
            'inscriptions' =>$inscriptionRepository->findByExampleField($employe),
        ]);
    }

    /**
     * @Route("/{id}/accept", name="inscription_accept", methods={"GET",  "POST"})
     */
    public function show(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InscriptionTypeAdminAccepter::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('inscription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('inscription/edit.html.twig', [
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/refuse", name="inscription_refuse", methods={"GET", "POST"})
     */
    public function edit(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InscriptionTypeAdmin::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('inscription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('inscription/edit.html.twig', [
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="inscription_delete", methods={"POST"})
     */
    public function delete(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inscription->getId(), $request->request->get('_token'))) {
            $entityManager->remove($inscription);
            $entityManager->flush();
        }

        return $this->redirectToRoute('inscription_index', [], Response::HTTP_SEE_OTHER);
    }
}
