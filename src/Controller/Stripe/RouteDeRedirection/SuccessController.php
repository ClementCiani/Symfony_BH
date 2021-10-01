<?php

namespace App\Controller\Stripe\RouteDeRedirection;


use App\Classe\Cart;
use Doctrine\ORM\EntityManagerInterface;
use App\MesServices\Panier\PanierService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\MesServices\Facture\CreerFactureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SuccessController extends AbstractController
{
    /**
     * @Route("/success", name="payment_success")
     */
    public function success(Cart $cart)
    {


        //Je voudrais vider le panier
        $cart->remove();

        //Je voudrais rediriger vers une page
        return $this->redirectToRoute('remerciement');
    }
}
