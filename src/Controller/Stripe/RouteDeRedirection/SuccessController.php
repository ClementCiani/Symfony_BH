<?php

namespace App\Controller\Stripe\RouteDeRedirection;


use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/success/{stripeSessionId}", name="payment_success")
     */
    public function success(Cart $cart, $stripeSessionId)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('remerciement');
        }

        if (!$order->getIsPaid()) {
            // dd($order);
            $order->setIsPaid(1);
            $this->entityManager->flush();
        }

        //Je voudrais vider le panier
        $cart->remove();

        //Je voudrais rediriger vers une page
        return $this->render('remerciement.html.twig', [
            'order' => $order
        ]);
    }
}
