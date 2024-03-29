<?php

namespace App\Controller;
use App\Entity\Client;
use App\Form\EditUserType;
use App\Repository\ClientRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin/users", name="utulisatuer")
     */
    public function userList(ClientRepository $users){
        return $this->render("admin/users.html.twig",[
            'users'=> $users->findAll()
        ]);

    }
    /**
     * @Route("/admin/users/{roless}", name="rolesg")
     */
    public function affichee(Request $request,$roless)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(Client::class)->findByRole($roless);


        return $this->render("admin/Employer.html.twig",[
            'users' =>$users
        ]);

    }
    /**
     * @Route("/admin/users/modifier/{id}", name="modifier_utulisateur")
     */
    public function edituser(Client $user,Request $request){
        $form = $this->createForm(EditUserType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('message','Utulisateur Modifier Avec succes');
            return $this->redirectToRoute('utulisatuer');
        }
        return  $this->render('admin/edituser.html.twig',[
            'f' => $form->createView()
        ]);


    }

}
