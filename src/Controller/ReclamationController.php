<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Client;
use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReclamationType;
use App\Form\ReponseType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

class ReclamationController extends AbstractController
{
    /*****************************
     *****BACK OFFICE************
     ****************************/


    /**
     * @Route("/admin/reclamations", name="reclamation_admin", methods={"GET"})
     */
    public function showReclamationsToAdmin(Request $request, PaginatorInterface $paginator, ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $this->getDoctrine()->getRepository(Reclamation::class)
            ->createQueryBuilder('r')
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
        $reclamations = $paginator->paginate(
            $reclamations,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('reclamation/admin/index.html.twig', [
            'reclamations' => $reclamations,
            "filter"=>""
        ]);
    }

    /**
     * @Route("/admin/reclamations/statut/{statut}", name="reclamation_by_statut_admin", methods={"GET"})
     */
    public function showReclamationsToAdminByStatut(Request $request, PaginatorInterface $paginator, $statut, ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $this->getDoctrine()->getRepository(Reclamation::class)
            ->createQueryBuilder('r')
            ->Where('r.statut=?1')
            ->setParameter(1, $statut)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
        $reclamations = $paginator->paginate(
            $reclamations,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('reclamation/admin/index.html.twig', [
            'reclamations' => $reclamations,
            "filter"=>$statut
        ]);
    }


    /**
     * @Route("/admin/reclamations/{id}", name="reclamation_show_admin", methods={"GET","POST"})
     */
    public function showReclamationToAdmin(Request $request, Reclamation $reclamation): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);
        $reponses = $this->getDoctrine()->getRepository(Reponse::class)
            ->createQueryBuilder('r')
            ->andWhere('r.reclamation=?1')
            ->setParameter(1, $reclamation)
            ->getQuery()
            ->getResult();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->getRepository(Reponse::class);
            $reponse->setAuthor($this->getUser());
            $date = new \DateTime();
            $reponse->setDate($date);
            $reponse->setHeure($date);
            $reponse->setReclamation($reclamation);
            $em->persist($reponse);
            $em->flush();
            $this->addFlash("success","Réponse ajouté avec succès!");
            return $this->redirectToRoute('reclamation_show_admin', ['id' => $reclamation->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('reclamation/admin/show.html.twig', [
            'reclamation' => $reclamation,
            'reponses' => $reponses,
            'f' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/reclamations/{id}/delete", name="reclamation_delete_admin", methods={"POST"})
     */
    public function deleteReclamtionByAdmin(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rec = $em->getRepository(Reclamation::class)->find($id);
        $em->remove($rec);
        $em->flush();
        $this->addFlash("success","Réclamation supprimée avec succès!");
        return $this->redirectToRoute('reclamation_admin', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/admin/reclamations/{cid}/close", name="admin-close-reclamation" , methods={"POST"})
     */
    //change the status of a reservation
    public function closeReclamation($cid): Response
    {
        $rep = $this->getDoctrine()->getRepository(Reclamation::class);
        $reclamation = $rep->find($cid);
        //check if the parameters are correct
        if ($reclamation == null) {
            return $this->redirectToRoute("erreur-back");
        }
        $reclamation->setStatut("Fermé");
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash("success","Réclamation fermée avec succès!");
        return $this->redirectToRoute('reclamation_show_admin', ['id' => $reclamation->getId()]);
    }

    /**
     * @Route("/admin/reclamations/{cid}/reopen", name="admin-reopen-reclamation" , methods={"POST"})
     */
    //change the status of a reservation
    public function reopenReclamation($cid): Response
    {
        $rep = $this->getDoctrine()->getRepository(Reclamation::class);
        $reclamation = $rep->find($cid);
        //check if the parameters are correct
        if ($reclamation == null) {
            return $this->redirectToRoute("erreur-back");
        }
        $reclamation->setStatut("Ouvert");
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash("success","Réclamation ouverte avec succès!");
        return $this->redirectToRoute('reclamation_show_admin', ['id' => $reclamation->getId()]);
    }

    /*****************************
     *****FRONT OFFICE************
     ****************************/


    /**
     * @Route("/reclamations", name="reclamation_index", methods={"GET"})
     */
    public function reclamationsClient(Request $request): Response
    {
        $clientId=$this->getUser()->getId();
        $client = $this->getDoctrine()->getRepository(Client::class)->find($clientId);
        $reclamations = $this->getDoctrine()->getRepository(Reclamation::class)
            ->createQueryBuilder('r')
            ->Where('r.clientId=?1')
            ->setParameter(1, $client->getId())
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'filter'=>""
        ]);
    }


    /**
     * @Route("/reclamations/statut/{statut}", name="reclamation_by_statut", methods={"GET"})
     */
    public function reclamationsClientByStatut(Request $request,$statut): Response
    {
        $clientId=$this->getUser()->getId();
        $client = $this->getDoctrine()->getRepository(Client::class)->find($clientId);
        $reclamations = $this->getDoctrine()->getRepository(Reclamation::class)
            ->createQueryBuilder('r')
            ->Where('r.clientId=?1')
            ->setParameter(1, $client->getId())
            ->AndWhere('r.statut=?2')
            ->setParameter(2, $statut)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            "filter"=>$statut
        ]);
    }


    /**
     * @Route("/reclamations/new", name="reclamation_new", methods={"GET", "POST"})
     */
    public function newReclmation(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setStatut("Ouvert");
            $clientId=$this->getUser()->getId();
            $client = $this->getDoctrine()->getRepository(Client::class)->find($clientId);
            $reclamation->setClientId($client);
            $date = new \DateTime();
            $reclamation->setDate($date);
            $reclamation->setHeure($date);
            $entityManager->persist($reclamation);
            $entityManager->flush();
            $this->addFlash("success","Réclamation ajoutée avec succès!");
            return $this->redirectToRoute('reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'f' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reclamations/{id}", name="reclamation_show", methods={"GET","POST"})
     */
    public function show(Request $request, Reclamation $reclamation): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);
        $reponses = $this->getDoctrine()->getRepository(Reponse::class)
            ->createQueryBuilder('r')
            ->andWhere('r.reclamation=?1')
            ->setParameter(1, $reclamation)
            ->getQuery()
            ->getResult();
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->getRepository(Reponse::class);
            $client = $this->getDoctrine()->getRepository(Client::class)->find($this->getUser()->getId());
            $reponse->setAuthor($client);
            $date = new \DateTime();
            $reponse->setDate($date);
            $reponse->setHeure($date);
            $reponse->setReclamation($reclamation);
            $em->persist($reponse);
            $em->flush();
            $this->addFlash("success","Réponse ajouté avec succès!");
            return $this->redirectToRoute('reclamation_show', ['id' => $reclamation->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
            'reponses' => $reponses,
            'f' => $form->createView(),
        ]);


    }

    /**
     * @Route("/reclamations/{id}/delete", name="reclamation_delete", methods={"POST"})
     */
    public function delete(Request $request, $id, EntityManagerInterface $em): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rec = $em->getRepository(Reclamation::class)->find($id);
        $em->remove($rec);
        $em->flush();
        $this->addFlash("success","Réclamation supprimée avec succès!");
        return $this->redirectToRoute('reclamation_index', [], Response::HTTP_SEE_OTHER);
    }


    /////////////////////////Mobile Services//////////////////////
    /**
     * @Route("/mobile/recs/", name="mobile_recs", methods={"GET"})
     */
    public function mobileRecs(ReclamationRepository $rep,request $request)
    {
        $res = $rep->findAll();

        $response = array();

        for ($i = 0; $i < sizeof($res); $i++) {
            $data = array(
                'id' => $res[$i]->getId(),
                'title' => $res[$i]->getSujet(),
                'date'=>$res[$i]->getDate(),
                'contenu' => $res[$i]->getContenu(),
                'aid' => $res[$i]->getClientId()->getId(),
                'aname'=> $res[$i]->getClientId()->getNom()." ".$res[$i]->getClientId()->getPrenom(),
                'statut' => $res[$i]->getStatut(),

            );
            $response[$i] = $data;
        }

        return $this->json(array('error' => false, 'res' => $response));


    }


    /**
     * @Route("/mobile/rec/add/", name="mobile_recc_add", methods={"POST"})
     */
    public function MobileAddRec(Request $request)
    {

        $sujet = $request->get('sujet');
        $contenu = $request->get('contenu');
        $aid = $request->get("author");
        if ($sujet && $contenu && $aid) {
            try {
                $OP = new Reclamation();
                $date = new \DateTime();
                $OP->setDate($date);
                $OP->setClientId($this->getDoctrine()->getRepository(Client::class)->find($aid));
                $OP->setStatut("Ouvert");
                $OP->setSujet($sujet);
                $OP->setContenu($contenu);
                $OP->setHeure($date);
                $em = $this->getDoctrine()->getManager();
                $em->persist($OP);
                $em->flush();
                return $this->json(array('error' => false, 'adsID' => $OP->getId()));
            } catch (Exception $e) {
                return $this->json(array('error' => true));
            }

        } else {
            return $this->json(array('error' => true));
        }
    }

    /**
     * @Route("/mobile/rec/delete/{id}", name="mobile_rec_delete", methods={"POST"})
     */
    public function MobileDeleteRec($id, Request $request,EntityManagerInterface $entityManager)
    {
        try {
            $rec = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);

            foreach ($rec->getReponses() as $c){
                $entityManager->remove($c);
            }
            $entityManager->remove($rec);
            $entityManager->flush();

            return $this->json(array('error' => false));
        } catch (Exception $e) {
            return $this->json(array('error' => true));
        }
    }

    /**
     * @Route("/mobile/rep/{bid}", name="mobile_reponses", methods={"GET"})
     */
    public function mobileReponses(Request $request,$bid,ReclamationRepository $rep)
    {
        $c = $rep->find($bid);
        $res=$c->getReponses();
        $response = array();

        for ($i = 0; $i < sizeof($res); $i++) {
            $data = array(
                'id' => $res[$i]->getId(),
                'date' => $res[$i]->getDate(),
                'contenu' => $res[$i]->getContenu(),
                'aid' => $res[$i]->getAuthor()->getId(),
                'aname' => $res[$i]->getAuthor()->getNom() . " " . $res[$i]->getAuthor()->getPrenom(),
            );
            array_push($response,$data);
        }

        return $this->json(array('error' => false, 'res' => $response));


    }

    /**
     * @Route("/mobile/rep/add/", name="mobile_rep_add", methods={"POST"})
     */
    public function MobileAddRep(Request $request)
    {

        $recid = $request->get('recid');
        $contenu = $request->get('contenu');
        $aid = $request->get("author");
        if ($recid && $contenu && $aid) {
            try {
                $OP = new Reponse();
                $date = new \DateTime();
                $OP->setDate($date);
                $OP->setAuthor($this->getDoctrine()->getRepository(Client::class)->find($aid));
                $OP->setReclamation($this->getDoctrine()->getRepository(Reclamation::class)->find($recid));
                $OP->setContenu($contenu);
                $OP->setHeure($date);
                $em = $this->getDoctrine()->getManager();
                $em->persist($OP);
                $em->flush();
                return $this->json(array('error' => false, 'adsID' => $OP->getId()));
            } catch (Exception $e) {
                return $this->json(array('error' => true));
            }

        } else {
            return $this->json(array('error' => true));
        }
    }

    /**
     * @Route("/mobile/rec/change/", name="menu_rec_change_statut", methods={"POST"})
     */
    public function MobileChangeStatutRec(Request $request): Response
    {
        try {
            $id = $request->get("id");
            $statut = $request->get('statut');

            $rep = $this->getDoctrine()->getRepository(Reclamation::class);
            $rev = $rep->find($id);
            $rev->setStatut($statut);
            $em = $this->getDoctrine()->getManager();
            $em->flush();


            return $this->json(array('error' => false));
        } catch (Exception $e) {
            print($e);
            return $this->json(array('error' => true));
        }
    }


}
