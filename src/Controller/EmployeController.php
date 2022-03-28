<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Produit;
use App\Form\EmployeType;
use App\Form\LoginType;


use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/employe")
 */
class EmployeController extends AbstractController
{
    /**
     * @Route("/", name="employe_index", methods={"GET"})
     */
    public function index(EmployeRepository $employeRepository): Response
    {
        return $this->render('employe/index.html.twig', [
            'employes' => $employeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/newEmp", name="employe_new", methods={"GET","POST"})
     */
    public function new(Request $request, EmployeRepository $employeRepository): Response
    {
        $utilisateur = new Employe();
        $form = $this->createForm(EmployeType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($employeRepository->findOneByPseudo($utilisateur->getLogin()) != null)
            {// s'il ne l'est pas, on renvoie vers la page de création d'utilisateur avec une notification
                return $this->render('employe/new.html.twig', [
                    'employe' => $utilisateur,
                    'form'        => $form->createView(),
                    'message'     => "Ce pseudo est déjà utilisé, merci d'en changer"
                ]);
            }
            $entityManager = $this->getDoctrine()->getManager();
            //Nous modifions l'utilisateur
            $pass = md5($utilisateur->getMdp());
            $utilisateur->setMdp($pass);//mot de passe crypté
            $utilisateur->setStatut(2);//par défaut un nouvel utilisateur aura un role classique
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('employe_index');//on ne veut pas que l'utilisateur ai accès à la liste de tous les autres utilisateurs
        }

        return $this->render('employe/new.html.twig', [
            'employe' => $utilisateur,
            'form' => $form->createView(),
        ]);
        }

    /**
     * @Route("/{id}", name="employe_show", methods={"GET"})
     */
    public function show(Employe $employe): Response
    {
        return $this->render('employe/show.html.twig', [
            'employe' => $employe,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="employe_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Employe $employe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('employe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('employe/edit.html.twig', [
            'employe' => $employe,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="employe_delete", methods={"POST"})
     */
    public function delete(Request $request, Employe $employe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($employe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('employe_index', [], Response::HTTP_SEE_OTHER);
    }

     /**
     * @Route("/loginDure", name="loginDure")
     */
    public function loginDure(Request $request)
    {  
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            // mise en variable de session de l'employé
            $session = new Session();
            $session->set('employeId', 1);
            $login = $form->get('login')->getViewData();
            $mdp = $form->get('mdp')->getViewData();
            $mdp = md5($mdp);
            $user = $this->getDoctrine()->getRepository(employe::class)->FindBy(
                [
                    'login' => $login,
                    'mdp' => $mdp
                ],
                [],
                1
            );
        }
       return $this->render('employe/loginDure.html.twig', array('form'=>$form->createView()));
    }
}
