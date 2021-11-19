<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/mot-de-passe-oublie', name: 'reset_password')]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($request->get('email')) {

            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            if ($user) {
                //Enregistrement en base de la demande de reset de password
                //avec user, token, createdAt.
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTime());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                //Envoyer un mail avec un lien au user pour reset son mot de passe.
                $url = $this->generateUrl(
                    'update_password',
                    [
                        'token' => $reset_password->getToken(),

                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL //sert a faire apparaitre le lien du mot de passe dans le templaite de mail
                );

                $content = "Bonjour " . $user->getFirstName() . "<br/>Vous avez demandé à réinitialiser votre mot de passe sur le site Banlieue's Heart.<br/><br/>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='" . $url . "'>mettre à jour votre mot de passe</a>.";


                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName(), 'Réinitialiser votre mot de passe sur Banlieue\'s Hear', $content);

                $this->addFlash('notice', 'Vous allez recevoir dans quelques secondes un mail avec la procédure pour réinitialiser votre mot de passe.');
            } else {
                $this->addFlash('notice', 'Cette adresse email est inconnue.');
            }
        }
        return $this->render('reset_password/index.html.twig');
    }



    /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="update_password")
     */
    public function update(Request $request, $token, UserPasswordEncoderInterface $passwordEncoder)
    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$reset_password) {
            return $this->redirectToRoute('reset_password');
        }

        // Vérifier si le createdAt = maintenant - 30 minutes
        $now = new \DateTime();
        if ($now > $reset_password->getCreatedAt()->modify('+ 30 minutes')) {
            $this->addFlash('notice', 'Votre demande de mot de passe expiré. Merci de la renouveller.');
            return $this->redirectToRoute('reset_password');
        }

        //rendre une vue pour rentrer le nouveau mot de passe et sa confirmation.
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new_pwd = $form->get('new_password')->getData();
            //Encodage du mot de passe.
            $password = $passwordEncoder->encodePassword($reset_password->getUser(), $new_pwd);
            $reset_password->getUser()->setPassword($password);

            //Flush en base de donnée.
            $this->entityManager->flush();

            //Redirection de l'utilisateur 
            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('reset_password/update.html.twig', [
            'updateForm' => $form->createView()
        ]);
    }
}
