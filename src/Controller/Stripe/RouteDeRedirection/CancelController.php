<?php

namespace App\Controller\Stripe\RouteDeRedirection;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CancelController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/cancel/{stripeSessionId}", name="payment_cancel")
     */
    public function cancel($stripeSessionId)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // dd("ca a pas marché. Tu as pas d'argent.");
        return $this->render('cancel.html.twig', [
            'order' => $order
        ]);
    }
}
