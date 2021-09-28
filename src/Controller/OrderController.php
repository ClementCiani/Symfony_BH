<?php

namespace App\Controller;

use DateTime;
use App\Classe\Cart;
use App\Entity\Order;
use DateTimeInterface;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/commande', name: 'order')]
    public function index(Cart $cart, Request $request): Response
    {
        if (!$this->getUser()->getAdresses()->getValues()) {
            return $this->redirectToRoute('add_account_adress');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'orderForm' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'order_recap', methods: "POST")]
    public function add(Cart $cart, Request $request): Response
    {

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('adresses')->getData();
            $deliveryContent = $delivery->getFirstName() . ' ' . $delivery->getLastName();
            $deliveryContent .= '<br>' . $delivery->getPhone();

            if ($delivery->getCompany()) {
                $deliveryContent .= '<br>' . $delivery->getCompany();
            }

            $deliveryContent .= '<br>' . $delivery->getAdress();
            $deliveryContent .= '<br>' . $delivery->getPostal() . ' ' . $delivery->getCity();
            $deliveryContent .= '<br>' . $delivery->getCountry();

            $order = new Order();
            $order->setUser($this->getUser());
            $order->setOrderDate($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($deliveryContent);
            $order->setIsPaid(0);

            $this->entityManager->persist($order);



            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotalPrice($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);
            }

            // $this->entityManager->flush();


            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $deliveryContent
            ]);
        }

        return $this->redirectToRoute('cart');
    }
}
