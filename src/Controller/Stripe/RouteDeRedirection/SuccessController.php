<?php

namespace App\Controller\Stripe\RouteDeRedirection;


use App\Classe\Cart;
use App\Classe\Mail;
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

            //Je voudrais vider le panier
            $cart->remove();

            $order->setIsPaid(1);
            $this->entityManager->flush();

            $mail = new Mail();
            $content = "Bonjour" . $order->getUser()->getFirstName() . "<br>Merci pour votre commande.<br>";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Votre commande sur Banlieue\'s Heart est validée.', $content);
        }



        //Je voudrais rediriger vers une page
        return $this->render('remerciement.html.twig', [
            'order' => $order
        ]);
    }
}
