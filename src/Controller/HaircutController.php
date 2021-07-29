<?php

namespace App\Controller;

use App\Entity\Haircut;
use App\Form\HaircutType;
use App\Repository\HaircutRepository;
use App\Repository\ImageRepository;
use App\Entity\Paginator;
use App\Form\PaginatorType;
use App\Repository\PaginatorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use App\Form\UseType;
use App\Form\UsType;
use App\Form\UsersType;
use App\Form\ProfilType;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Validator\Validator\ValidatorInterface;
 
class HaircutController extends AbstractController 
{ 
    /**
     * @Route("/", name="home_page", methods={"GET"})
     */
    public function home(HaircutRepository $haircutRepository, ImageRepository $imageRepository): Response
    {
        
        return $this->render('haircut/home.html.twig', [
            'haircuts' => $haircutRepository->findAll(), 'picture' => $imageRepository->findOneBySomeField(1)
        ]);
    } 

    /**
     * @Route("/about/of/company", name="about_of_company", methods={"GET"})
     */
    public function aboutOfCompany(ImageRepository $imageRepository): Response
    {
        return $this->render('haircut/aboutOfCompany.html.twig', ['picture' => $imageRepository->findOneBySomeField(1)]);
    }
  
    /**
     * @Route("/user/online/booking/{id}", name="online_booking", methods={"GET"})
     */
    public function onlineBooking(ImageRepository $imageRepository, EventRepository $eventRepository, $id): Response
    {
        $events = $eventRepository->findAll();

        $rdvs = [];

        foreach ($events as $event ) {
            $rdvs[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'start' => $event->getStart()->format("Y-m-d H:i:s"),
                'end' => $event->getEnd()->format("Y-m-d H:i:s")
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('haircut/onlineBooking.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'haircutId' => $id, 'data' => $data]);
    }


    /**
     * @Route("/user/update/password", name="update_password", methods={"GET|POST"}) 
     */
    public function updatePassword(ImageRepository $imageRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils, MailerInterface $mailer): Response
    {
        $user = $this->getUser(); 
        
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())  {
            
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);  
            
            $orm = $this->getDoctrine()->getManager();
            $orm->persist($user);
            $orm->flush();

            $this->addFlash('success', ' La modification de votre mot de passe est réussite !!! Dorénavant connectez-vous avec ce mot de passe.');

            return $this->redirectToRoute('home_page');
        } 

        return $this->render('haircut/updatePassword.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'user' => $user,
            'form' => $form->createView()]); 
    } 

    /**
     * @Route("/user/update/email", name="update_email_user", methods={"GET|POST"}) 
     */
    public function updateEmailUser(ImageRepository $imageRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        
        $form = $this->createForm(UseType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())  {
            
            $email = $user->getEmail();
            //dd($email);
            $user->setEmail($email);  
            
            $orm = $this->getDoctrine()->getManager();
            $orm->persist($user);
            $orm->flush();

            $this->addFlash('success', ' La modification de votre Email est réussite !!! ');

            return $this->redirectToRoute('home_page');
        }

        return $this->render('haircut/updateEmailUser.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'user' => $user,
            'form' => $form->createView()]); 
    }

    /**
     * @Route("/user/update/number", name="update_number_user", methods={"GET|POST"}) 
     */
    public function updateNumberUser(ImageRepository $imageRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        
        $form = $this->createForm(UsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())  {
            
            $number = $user->getNumber();
            
            $user->setEmail($number);  
            
            $orm = $this->getDoctrine()->getManager();
            $orm->persist($user);
            $orm->flush();

            $this->addFlash('success', ' La modification de votre numéro de téléphone est réussite !!! ');

            return $this->redirectToRoute('home_page');
        }

        return $this->render('haircut/updateNumberUser.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'user' => $user,
            'form' => $form->createView()]); 
    }

    /**
     * @Route("/signUp", name="sign_up", methods={"GET|POST"}) 
     */
    public function signUp(ImageRepository $imageRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer, ValidatorInterface $validator): Response
    {
        $user = new User(); 
        $form = $this->createForm(UserType::class, $user); 
        $form->handleRequest($request);

        //dd($form->isSubmitted(), $form->isValid(), $validator->validate($form->getData()));
        if ($form->isSubmitted() && $form->isValid()){
            
            $submittedToken = $request->request->get('token');
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            //dd($user->getEmail(), $form->getData(), $submittedToken, $form->isValid());
            $user->setPassword($password);  
            $user->setIsActive(false);
            //$user->setRoles($roles);
            $user->setToken($submittedToken);
            
            $orm = $this->getDoctrine()->getManager();
            $orm->persist($user);
            $orm->flush();

            $emailUser = $user->getEmail();

            $email = (new TemplatedEmail())
            ->from('dada.pepe.alal@gmail.com')
            ->to(new Address($emailUser))
            ->subject('Valider votre inscription')

            // path of the Twig template to render
            ->htmlTemplate('haircut/mail.html.twig')

            // pass variables (name => value) to the template
            ->context([
            'submittedToken' => $submittedToken,
            ]);
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                //'error message or try to resend the message'
            };
            
            $this->addFlash('success', ' Félicitations ! Bienvenu parmi nous ! Votre compte a bien été enregistré. Pour finaliser l\'inscription, rendez-vous sur votre boîte mail et clicker sur le lien. À bientôt !!');

            return $this->redirectToRoute('home_page');
        }
        
        return $this->render('haircut/signUp.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'user' => $user,
            'form' => $form->createView()]); 
    }
 
    /**
     * @Route("/validate/account/{submittedToken}", name="validate_account", methods="GET|POST")
     */
    public function validateAccount(UserRepository $userRepository, Request $request, $submittedToken)
    {
        
        $user = $userRepository->findOneBySomeField($submittedToken);
        //Son compte utilisateur est actif.
        $user->setIsActive(true);
        $roles = ['ROLE_USER'];
        $user->setRoles($roles);
        $orm = $this->getDoctrine()->getManager();
        $orm->persist($user);
        $orm->flush();
        
        return $this->redirectToRoute('home_page');
    }

    /**
     * @Route("/connection/link", name="connectionLink", methods="GET|POST")
     */
    public function connectionLink(ImageRepository $imageRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils, MailerInterface $mailer)
    {

        return $this->render('haircut/connectionLink.html.twig', ['picture' => $imageRepository->findOneBySomeField(1)]);
    }

    /**
     * @Route("/singIn", name="sing_in", methods="GET|POST")
     */
    public function signIn(Request $request, ImageRepository $imageRepository, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils):Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();    

        return $this->render('haircut/signIn.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/services/{page}", name="services", methods={"GET"})
     */
    public function services(HaircutRepository $haircutRepository, $page, ImageRepository $imageRepository): Response
    {
        $nombreMaxParPage = 6;
        $nombreMax = 6;
        $firstResult = ($page-1) * $nombreMaxParPage;

        $serviceNumber = $haircutRepository->haircutNumber($firstResult, $nombreMax);

        $findAllPage = $haircutRepository->findAllPage($page, $nombreMaxParPage);
        
        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($findAllPage) / $nombreMaxParPage),
            'nomRoute' => 'services',
            'paramsRoute' => array()
        );
        return $this->render('haircut/service.html.twig', [
            'serviceNumber' => $serviceNumber, 'pagination' => $pagination, 'picture' => $imageRepository->findOneBySomeField(1)]);
    }

    /** 
     * @Route("/logout/user", name="logout", methods="GET|POST")
     */
    public function logout(): Response
    {
        
        return $this->render(
            'haircut/home.html.twig',
            [
                    'mainNavLogin' => false,
                    'title' => 'Deconnexion',
                    'error' => null,
            ]
        );
    }

    /**
     * @Route("/details/haircut/{id}", name="details_haircut", methods={"GET"})
     */
    public function detailsHaircut(Haircut $haircut, ImageRepository $imageRepository): Response
    {
        return $this->render('haircut/detailsHaircut.html.twig', [
            'haircut' => $haircut, 'picture' => $imageRepository->findOneBySomeField(1)
        ]);
    }
}
