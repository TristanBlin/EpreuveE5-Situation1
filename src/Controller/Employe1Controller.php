<?php

namespace App\Controller;
use App\Form\LoginType;
use App\Entity\Employe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\FormationRepository;
use App\Repository\InscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class Employe1Controller extends AbstractController
{
    /**
    * @Route("/employe1", name="employe1")
    */
    public function index(): Response
    {
        return $this->render('employe1/index.html.twig', [
            'controller_name' => 'Employe1Controller',
        ]);
    }
    /**
    * @Route("/", name="authentification")
    */
    public function loginDure(Request $request, FormationRepository $formationRepository, InscriptionRepository $inscriptionRepository)
    {  
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $login = $form->get('login')->getViewData();
            // mise en variable de session de l'employé
            $session = new Session();
            $mdp = $form->get('mdp')->getViewData();
            $mdp = md5($mdp);
            $user = $this->getDoctrine()->getRepository(Employe::class)->FindBy(
                [
                    'login' => $login,
                    'mdp' => $mdp
                ],
                [],
                1
            );
            if($user != null){
                $session->set("employeId", $user[0]->getId());
                if($user[0]->getStatut()==0){
                    //login DRH     
                   return $this->redirectToRoute('inscription_index');  
                }
                else{
                    return $this->render('formation/employe.html.twig', [
                        'formations' => $formationRepository->findAll(),
                        'inscriptions' =>$inscriptionRepository->findByExampleField($user[0]->getId()),
                    ]);
                    }
                }
        }
       return $this->render('employe1/loginDure.html.twig', array('form'=>$form->createView()));
    }

    /**
     * @Route("/new", name="employe_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $employe = new Employe();
        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($employe);
            $entityManager->flush();
            return $this->redirectToRoute('employe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('employe/new.html.twig', [
            'employe' => $employe,
            'form' => $form,
        ]);
    }

}
