<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{   
    #[Route('/cart', name: 'cart')]
    public function index(RequestStack $rs, ProductRepository $repo)
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);

        //je vais créer un nouveau tableau qui contiendra des objets Product et les quantités de chaque objet
        $cartWithData = [];

        //Pour chaque id qui se trouve dans le tableau $cart, on ajoute une case (tableau) dans cartWithData, qui est un tableau multidimensionnel
        foreach ($cart as $id => $quantity)
        {
            $cartWithData[] = [
                'product' => $repo->find($id),
                'quantity' => $quantity
            ];
        }
    }

    #[Route('/cart/add/{id}', name: "cart_add")]
    //quelle classe nous permet d'accéder à la session ? Request | mais aussi dans le controller avec certaines méthodes
    public function add($id, RequestStack $rs)              
    {
        //Nous allons récupérer une session grâce à la classe RequestStack
        $session = $rs->getSession();

        //je récupère l'attribut de session 'cart' s'il existe ou un tableau vide
        //paramètre 1: 'cart' récupère la partie réservée au panier
        //paramètre 2: s'il n'y a pas de panier, en créer un vide dans un tableau
        $cart = $session->get('cart', []);

        //on vérifie que notre tableau à l'indice du produit qu'il y a qqcho dedans
        //si le produit existe déjà j'incrémente sa quantité sinon j'initialise à 1
        if(!empty($cart[$id]))
        {
            $cart[$id]++;
        }else{

            $cart[$id] = 1;
        }
        //dans la nouvelle (en indice l'Id du produit, en valeur sa quantité)
        //dans mon tableau $cart, à la case $id je donne la valeur 1
        //indice => valeur | idProduit => QuantiteDuProduitDansLePanier

        //on sauvegarde l'état du panier en session, on va set notre nouveau tableau avec cet élément
        $session->set('cart', $cart);
        //je sauvegarde l'état de mon panier en session à l'attribut de session "cart"

        dd($session->get('cart'));
        
    }
}
