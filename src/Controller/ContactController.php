<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('notice', 'Merci de nous avoir contacté. Nous vous répondrons dans les plus bref délais');
            $firstName = $form->get('first_name')->getData();
            $lastName = $form->get('last_name')->getData();
            $email = $form->get('email')->getData();
            $objet = $form->get('objet')->getData();
            $content = $form->get('content')->getData();

            $mail = new Mail();
            $mail->send('kless93@gmail.com', 'Banlieue\'s Heart', $objet, 'De la part de ' . $lastName . ' ' . $firstName . ' depuis l\'adresse mail ' . $email . ' <br> <br> ' . $content);

            return $this->redirectToRoute('contact');
        }




        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
