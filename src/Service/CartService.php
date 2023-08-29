<?php

//définir l'espace de travail
namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $repo;
    private $rs;

    public function __construct(ProductRepository $repo, RequestStack $rs)
    {
        $this->rs = $rs;
        $this-> repo = $repo;
    }

    //on va encapsuler le code des méthodes pour pouvoir le ré-utiliser 
    public function add($id)
    {
        //Nous allons récupérer une session grâce à la classe RequestStack
        $session = $this->rs->getSession();

        //je récupère l'attribut de session 'cart' s'il existe ou un tableau vide
        //paramètre 1: 'cart' récupère la partie réservée au panier
        //paramètre 2: s'il n'y a pas de panier, en créer un vide dans un tableau
        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);

        //on vérifie que notre tableau à l'indice du produit qu'il y a qqcho dedans
        //si le produit existe déjà j'incrémente sa quantité sinon j'initialise à 1
        if(!empty($cart[$id]))
        {
            $cart[$id]++;
            $qt++;  
        }else{
            
            $qt++;
            $cart[$id] = 1;
        }
        //dans la nouvelle (en indice l'Id du produit, en valeur sa quantité)
        //dans mon tableau $cart, à la case $id je donne la valeur 1
        //indice => valeur | idProduit => QuantiteDuProduitDansLePanier

        //on sauvegarde l'état du panier en session, on va set notre nouveau tableau avec cet élément
        $session->set('cart', $cart);
        $session->set('qt', $qt);
        //je sauvegarde l'état de mon panier en session à l'attribut de session "cart" 
    }

    public function remove($id)
    {
        $session = $this->rs->getSession();       //je récupère ma session | C'est le $rs de la classe qu'on utilise
        $cart = $session->get('cart', []) ;                  // dans la session on va chercher celui qui a comme attribut "cart"
        $qt = $session->get('qt', 0);
        //s'il n'y a pas de quantité on ne supprime rien dans ma carte à l'indice ID
        //!si l'id existe dans mon panier, je le supprime du tableau grâce à unset()
        if(!empty($cart[$id]))
        {
            $qt -= $cart[$id];
            unset($cart[$id]);

        }
        //gérer l'erreur possible négative
        if($qt < 0)
        {
            $qt = 0;
        }

        $session->set('qt', $qt);

        //une fois qu'on a supprimé qqch dans le session on doit la mettre à jour
        $session->set('cart', $cart);                 //on le renvoie dans la nouvelle session | ici cart c'est l'indice dans ma session
    }

    //cette fonction quoiqu'il arrive doit return un tableau
    //il va falloir le récupérer
    public function getCartWithData()
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);

        //je vais créer un nouveau tableau qui contiendra des objets Product et les quantités de chaque objet
        $cartWithData = [];

        //Pour chaque id qui se trouve dans le tableau $cart, on ajoute une case (tableau) dans cartWithData, qui est un tableau multidimensionnel
        foreach ($cart as $id => $quantity)
        {
            $cartWithData[] = [
                'product' => $this->repo->find($id),
                'quantity' => $quantity
            ];
        }
        //on rajoute le return: 
        return $cartWithData;
    }

    public function getTotal()
    {
        $total = 0;  //j'initalise mon total

        foreach($this->getCartWithData() as $item)
        {
            $sousTotal = $item['product']->getPrice() * $item['quantity'];
            $total += $sousTotal;  // à chaque tour de boucle je calcule le sous-total
        }
        return $total; //afin de pouvoir l'utiliser dans le controller ou autre part
    }
}