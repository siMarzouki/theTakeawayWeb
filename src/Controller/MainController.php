<?php

namespace App\Controller;

use App\Entity\BlogClient;
use App\Entity\Commande;
use App\Entity\Reclamation;
use App\Entity\Reservation;
use App\Entity\Restaurant;
use App\Entity\RestaurantFavoris;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(): Response
    {

        return $this->render('main/index.html.twig', [
        ]);
    }

    /**
     * @Route("/error", name="erreur-front")
     */
    public function errorFront(): Response
    {
        return $this->render('front-erreur.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/error", name="erreur-back")
     */
    public function errorBack(): Response
    {
        return $this->render('back-erreur.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/", name="dashboard")
     */
    public function dashboard(): Response
    {
        $restaurants = $this->getDoctrine()->getRepository(Restaurant::class)->findAll();
        $reservations = $this->getDoctrine()->getRepository(Reservation::class)->findAll();
        $fav=  $this->getDoctrine()->getRepository(RestaurantFavoris::class)->findAll();
        $commandes = $this->getDoctrine()->getRepository(Commande::class)->findAll();
        //shortcuts data
        $reservationsEnAttente=$this->getDoctrine()->getRepository(Reservation::class)->findBy(["statut"=>"En Attente"]);
        $commandesEnAttente=$this->getDoctrine()->getRepository(Commande::class)->findBy(["statut"=>"En attente"]);
        $BlogEnAttente = $this->getDoctrine()->getRepository(BlogClient::class)->findBy(["statut"=>"En Attente"]);
        $reclamationsOuvertes=$this->getDoctrine()->getRepository(Reclamation::class)->findBy(["statut"=>"Ouvert"]);
        $s1=count($commandesEnAttente);
        $s2 =count($reservationsEnAttente);
        $s3=count($reclamationsOuvertes);
        $s4=count($BlogEnAttente);;

        //first chart
        $data1 = array();
        foreach ($restaurants as $r) {
            $data1[$r->getNom()] = 0;
        }
        foreach ($reservations as $r) {
            $data1[$r->getRestaurant()->getNom()]++;
        }
        //second chart
        $data2 = array();
        $data2["15"] = 0;
        $data2["30"] = 0;
        $data2["45"] = 0;
        foreach ($reservations as $r) {
            $data2[strval(date_diff($r->getHeureDepart(), $r->getHeureArrive())->i)]++;
        }

        //third chart
        $data3 = array();
        foreach ($restaurants as $r) {
            $data3[$r->getNom()] = 0;
        }
        foreach ($fav as $r) {
            $data3[$r->getRestaurant()->getNom()]++;
        }
        //fourth chart
        $data4 = array();
        foreach ($restaurants as $r) {
            $data4[$r->getNom()] = 0;
        }
        foreach ($commandes as $c) {
            $data4[$c->getRestaurant()->getNom()]++;
        }

        return $this->render('main/admin/dashboard.html.twig', [
            "s1"=>$s1,
            's2'=>$s2,
            "s3"=>$s3,
            "s4"=>$s4,
            "maxCom"=>count($commandes),
            "cmdData"=>array_values($data4),
            "resNames" => array_keys($data1),
            "revData" => array_values($data1),
            "maxRev" => count($reservations),
            "timeData" => array_values($data2),
            "resX"=>array_keys($data3),
            "favX"=> array_values($data3),
        ]);
    }
}
