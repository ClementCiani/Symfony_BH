<?php

namespace App\MesServices\Stripe;

use Stripe\Stripe;
use App\Classe\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Symfony\Component\Security\Core\Security;

class CreerSessionService
{
    protected $keySecret;

    protected $cart;

    protected $security;

    protected $entityManager;

    public function __construct($keySecret, EntityManagerInterface $entityManager, Cart $cart, Security $security)
    {
        $this->keySecret = $keySecret;
        $this->cart = $cart;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function getDomain()
    {
        return 'https://localhost:8000';
    }

    public function getItems($order)
    {
        $products_stripe = [];

        $products = $this->cart->getFull();

        foreach ($products as $item) {
            $products_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $item['product']->getPrice(),
                    'product_data' => [
                        'name' => $item['product']->getName()
                    ]
                ],
                'quantity' => $item['quantity']
            ];
        }

        $products_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName()
                ]
            ],
            'quantity' => 1,
        ];

        return $products_stripe;
    }

    public function create($order)
    {
        Stripe::setApiKey($this->keySecret);

        $checkout_session = Session::create([
            'customer_email' => $this->security->getUser()->getEmail(),
            'line_items' => [
                $this->getItems($order)
            ],
            'payment_method_types' => [
                'card',
            ],
            'mode' => 'payment',
            'success_url' => $this->getDomain() . '/success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->getDomain() . '/cancel/{CHECKOUT_SESSION_ID}',
        ]);
        $order->setStripeSessionId($checkout_session->id);
        $this->entityManager->flush();
        return $checkout_session;
    }
}
