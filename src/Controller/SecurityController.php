<?php
 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\ImageRepository;
use App\Entity\User;
use App\Form\UserType;
use App\Form\ProfilType;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class SecurityController extends AbstractController
{
    /**
     * 
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, ImageRepository $imageRepository, Request $request, UserRepository $userRepository): Response
    {
        if ($this->getUser()){
            
            return $this->redirectToRoute('home_page'); 
        }else{  
            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('haircut/signIn.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'last_username' => $lastUsername, 'error' => $error]);
        }        
    }

    /**
     * @Route("/login2", name="secu_login")
     */  
    public function requestLoginLink(NotifierInterface $notifier, LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository, Request $request, MailerInterface $mailer)
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            $roles = ['ROLE_USER'];
            if($user->getRoles() === $roles){
                $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

                $email = (new TemplatedEmail())
                ->from('dada.pepe.alal@gmail.com')
                ->to(new Address($user->getEmail()))
                ->subject('Mot de passe oublié')

                // path of the Twig template to render
                ->htmlTemplate('haircut/link.html.twig')

                // pass variables (name => value) to the template
                ->context([
                'loginLinkDetails' => $loginLinkDetails,
                ]);
                try {
                    $mailer->send($email);
                } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                //'error message or try to resend the message'
                };

                $this->addFlash('success', ' Rendez-vous sur votre boîte mail et clicker sur le lien pour se connecter. À bientôt !!');
            }
            
            return $this->redirectToRoute('home_page');
        }
    }


    /**
     * @Route("/login_check", name="login_check")
     */
    public function check()
    {
        throw new \LogicException('This code should never be reached');
    }

    /**
     * @Route("/logout", name="app_logout", methods="GET|POST")
     */
    public function logout(): Response
    {
        
    }

    
}
