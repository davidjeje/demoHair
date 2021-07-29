<?php

namespace App\Controller; 

//require '../vendor/autoload.php';

use App\Entity\Event; 
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Entity\EventNotValidated;
use App\Repository\EventNotValidatedRepository;
use App\Entity\Date;
use App\Repository\DateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ImageRepository;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Entity\Haircut;
use App\Form\HaircutType;
use App\Repository\HaircutRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;

class EventController extends AbstractController
{
    /**
     * @Route("/user/event/give/data", name="event_give_data", methods={"GET|POST"})
     */
    public function dataGive(EventRepository $eventRepository, ImageRepository $imageRepository)
    { 
        $start = htmlspecialchars($_GET['start']);
        $end = htmlspecialchars($_GET['end']);
        
        if(isset($start) && !empty($start) && isset($end) && !empty($end)){
            $dateStart = new \dateTime($start);
        
            $dateEnd =  new \dateTime($end);

            $event = $eventRepository->findOneBy(['start' => $dateStart, 'end' => $dateEnd, 'member' => $this->getUser()->getId()]);
            
            if(isset($event) && !empty($event) ){
                $dates = new Date();

                $date = $dates->setStart($event->getStart());
                $date = $dates->setEnd($event->getEnd());
                $date = $dates->setMember($this->getUser());
                //dd($date);
                $orm = $this->getDoctrine()->getManager();
                $orm->persist($date);
                $orm->flush();
            }
        }
    }

    /**
     * @Route("/user/event/data/to/delete", name="event_data_to_delete", methods={"GET|POST"})
     */
    public function eventDataToDelete(EventRepository $eventRepository, ImageRepository $imageRepository,Request $request)
    { 
        $startDateEvent = new \dateTime($_GET['start']);
        
        $endDateEvent =  new \dateTime($_GET['end']);

        $event = $eventRepository->findOneBy([
            'member' => $this->getUser()->getId(),
            'start' => $startDateEvent,
            'end' => $endDateEvent
        ]);

        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($event);
        $entityManager->flush();
        
        return $this->redirectToRoute('home_page');
    }

    /**
     * @Route("/user/event/success/delete", name="event_success_delete", methods={"GET|POST"})
     */
    public function eventSuccessDelete(EventRepository $eventRepository, ImageRepository $imageRepository)
    {
        $this->addFlash('success', ' La suppression de votre RDV est réussite !!! ');

        return $this->redirectToRoute('home_page');
    }

    /**
     * @Route("/user/event/change", name="event_change", methods={"GET|POST"})
     */
    public function change(EventRepository $eventRepository, DateRepository $dateRepository,ImageRepository $imageRepository)
    {
        $dateLast = $dateRepository->findOneBy(['member' => $this->getUser()->getId()], ['id' => 'desc']);
        $dateFirst = $dateRepository->findOneBy(['member' => $this->getUser()->getId()], ['id' => 'asc']); 
        
        if(isset($dateLast) && !empty($dateLast) && isset($dateFirst) && !empty($dateFirst)){
            if($dateLast === $dateFirst){

                $last = $dateLast->getStart();
                $end = $dateLast->getEnd();

                $event = $eventRepository->findOneBy(['member' => $this->getUser(), 'start' => $last, 'end' => $end]);
                
                $startDate = $event->getStart();
                $startDate = $startDate->format("d-m-Y H:i:s");
        
                return $this->render('event/changeBooking.html.twig', [
                'event' => $event, 'startDate' => $startDate, 'picture' => $imageRepository->findOneBySomeField(1)]);
            }else{
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($dateFirst);
                $entityManager->flush();

                $event = $eventRepository->findOneBy(['start' => $dateLast->getStart(), 'end' => $dateLast->getEnd(), 'member' => $this->getUser()->getId()]);
    
                $startDate = $event->getStart();
                $startDate = $startDate->format("d-m-Y H:i:s");
        
                return $this->render('event/changeBooking.html.twig', [
                'event' => $event, 'startDate' => $startDate, 'picture' => $imageRepository->findOneBySomeField(1)]);
            }
            
        }else{
            $this->addFlash('error', ' Une erreur est survenue, nous nous excusons pour la gêne occasionnée. Nous vous invitons à sélectionner de nouveau la date à modifier.');

            return $this->redirectToRoute('event_planning_update');
            
        }
    }

    /**
     * @Route("/user/modify/reservation/date/{idBookingOld}", name="event_update", methods={"GET|POST"})
     */
    public function modifyReservationDate(EventRepository $eventRepository, ImageRepository $imageRepository, Event $idBookingOld)
    {
        if(isset($idBookingOld) && !empty($idBookingOld)){
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

            return $this->render('event/modifyReservationDate.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'idBookingOld' => $idBookingOld->getId(), 'data' => $data]);
        }
        $this->addFlash('error', ' Une erreur est survenue, nous nous excusons pour la gêne occasionnée. Nous vous invitons à cliquer de nouveau sur le bouton suivant.');

        return $this->redirectToRoute('event_change');
    }

    /**
     * @Route("/user/event/modification/validate/{idBookingOld}", name="event_modification_validate", methods={"GET|POST"})
     */
    public function eventModificationValidate(EventRepository $eventRepository, ImageRepository $imageRepository, Event $idBookingOld, DateRepository $dateRepository)
    {
        $start = htmlspecialchars($_GET['start']);
        $end = htmlspecialchars($_GET['end']);
        if(isset($idBookingOld) && !empty($idBookingOld)){
            if(isset($start) && !empty($start) && isset($end) && !empty($end)){
                
                $eventOld = $idBookingOld;
                $startDateEvent = new \dateTime($start);
                $EndDateEvent =  new \dateTime($end);

                $eventNew = $eventOld->setStart($startDateEvent);
                $eventNew = $eventOld->setEnd($EndDateEvent);

                $orm = $this->getDoctrine()->getManager();
                $orm->persist($eventNew);
                $orm->flush();

                $dateLast = $dateRepository->findOneBy(['member' => $this->getUser()->getId()], ['id' => 'desc']);

                $orm = $this->getDoctrine()->getManager();
                $orm->remove($dateLast);
                $orm->flush();
            }
        }
    }

    /**
     * @Route("/user/reservation/date/modification/success/message", name="event_success_update", methods={"GET|POST"})
     */
    public function reservationDateModificationSuccessMessage(EventRepository $eventRepository, ImageRepository $imageRepository)
    {
        $this->addFlash('success', ' La modification de votre RDV est réussite !!! ');

        return $this->redirectToRoute('home_page');
    }
 
    /**
     * @Route("/user/select/booking/date/modify", name="event_planning_update", methods={"GET"})
     */
    public function selectTheBookingDateToModify(EventRepository $eventRepository, ImageRepository $imageRepository)
    {
        $user = $this->getUser()->getId();
        
        $rdvMembers = $eventRepository->findBy(['member' => $user]);
        
        $rdvs = [];

        foreach ($rdvMembers as $rdvMember ) {
            $rdvs[] = [
                
                'title' => $rdvMember->getTitle(),
                'start' => $rdvMember->getStart()->format("Y-m-d H:i:s"),
                'end' => $rdvMember->getEnd()->format("Y-m-d H:i:s")
            ];
        }
 
        $data = json_encode($rdvs);

        return $this->render('event/selectTheBookingDateToModify.html.twig', [
            'datas' => $data,'picture' => $imageRepository->findOneBySomeField(1) 
        ]);
    } 

    /** 
     * @Route("/user/deletion/reservation/date", name="event_planning_delete", methods={"GET"})
     */
    public function deletionOfTheReservationDate(EventRepository $eventRepository, ImageRepository $imageRepository)
    {
        $user = $this->getUser()->getId();
        
        $rdvMembers = $eventRepository->findBy(['member' => $user]);
        
        $rdvs = [];

        foreach ($rdvMembers as $rdvMember ) {
            $rdvs[] = [
                
                'title' => $rdvMember->getTitle(),
                'start' => $rdvMember->getStart()->format("Y-m-d H:i:s"),
                'end' => $rdvMember->getEnd()->format("Y-m-d H:i:s")
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('event/deletionOfTheReservationDate.html.twig', [
            'datas' => $data,'picture' => $imageRepository->findOneBySomeField(1) 
        ]);
    } 

    /**
     * @Route("/user/order/summary", name="event_order_summary", methods={"GET"})
     */ 
    public function order(EventNotValidatedRepository $eventNotValidatedRepository,ImageRepository $imageRepository): Response
    {  
        $lastUserEvent = $eventNotValidatedRepository->findOneBy(['customer' => $this->getUser()->getId()], ['id' => 'desc']);
        $firstUserEvent = $eventNotValidatedRepository->findOneBy(['customer' => $this->getUser()->getId()], ['id' => 'asc']);

        if(!empty($lastUserEvent) && $firstUserEvent === $lastUserEvent){
            $start = $lastUserEvent->getStart();
            
            $start = $start->format("d-m-Y H:i:s");
             
            return $this->render('event/orderSummary.html.twig', [
            'event' => $lastUserEvent, 'start' =>$start,'picture' => $imageRepository->findOneBySomeField(1) 
            ]);    
        }elseif(!empty($lastUserEvent) && $firstUserEvent !== $lastUserEvent){

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($firstUserEvent);
            $entityManager->flush();

            $start = $lastUserEvent->getStart();
            
            $start = $start->format("d-m-Y H:i:s");
             
            return $this->render('event/orderSummary.html.twig', [
            'event' => $lastUserEvent, 'start' =>$start,'picture' => $imageRepository->findOneBySomeField(1) 
            ]);    
        }else{
            $this->addFlash('error', ' Une erreur s\'est produite, sélectionner de nouveau la coupe de cheveux que vous souhaitez avec un créneau horaire dans les heures ou jours à venir. Merci.');

            return $this->redirectToRoute('home_page');
        }    
    }

    /**
     * @Route("/user/event/new/{haircutId}", name="event_new", methods={"GET"}) 
     */
    public function new(Request $request, ImageRepository $imageRepository, HaircutRepository $haircutRepository, Haircut $haircutId)
    { 
        $title = htmlspecialchars($_GET['title']);
        $start = htmlspecialchars($_GET['start']);
        $end = htmlspecialchars($_GET['end']);
        
        if(isset($haircutId) && !empty($haircutId)){
            if(isset($title) && !empty($title)){
                if(isset($start) && !empty($start)){
                    if(isset($end) && !empty($end)){
                        $startDateEvent = new \dateTime($start);
                        $EndDateEvent =  new \dateTime($end);

                        if($startDateEvent->format("Y") >= date("Y") && $startDateEvent->format("m") >= date("m") ){

                            if($startDateEvent->format("d") === date("d") && $startDateEvent->format("H") >= date("H") && $startDateEvent->format("i") >= date("i")){
                                $eventNotValidated = new EventNotValidated();

                                $eventNotValidated->setTitle($title);
                                $eventNotValidated->setStart($startDateEvent);
                                $eventNotValidated->setEnd($EndDateEvent);
                                $eventNotValidated->setCustomer($this->getUser());
                                $eventNotValidated->setHaircut($haircutId);

                                $entityManager = $this->getDoctrine()->getManager();
                                $entityManager->persist($eventNotValidated);
                                $entityManager->flush();
                            }
                            elseif($startDateEvent->format("d") > date("d") ){
                                $eventNotValidated = new EventNotValidated();

                                $eventNotValidated->setTitle($title);
                                $eventNotValidated->setStart($startDateEvent);
                                $eventNotValidated->setEnd($EndDateEvent);
                                $eventNotValidated->setCustomer($this->getUser());
                                $eventNotValidated->setHaircut($haircutId);

                                $entityManager = $this->getDoctrine()->getManager();
                                $entityManager->persist($eventNotValidated);
                                $entityManager->flush();

                                //return $this->redirectToRoute('event_order_summary');
                            }else{
                                throw $this->createNotFoundException('Une erreur s\'est produite');
                            }
                        }else{
                           
                            throw $this->createNotFoundException('start date not found');
                        }         
                    }else{
                        throw $this->createNotFoundException('End date not found');
                    }   
                }else{
                    throw $this->createNotFoundException('Start date not found');
                }   
            }else{
                throw $this->createNotFoundException('Title not found');
            }   
        }else{
            throw $this->createNotFoundException('Haircut not found');
        }      
    } 

    /**
     * @Route("/user/online/payment", name="event_payment", methods={"GET"}) 
     */
    public function payment(Request $request, ImageRepository $imageRepository, HaircutRepository $haircutRepository, EventNotValidatedRepository $eventNotValidatedRepository)
    {
        $lastUserEvent = $eventNotValidatedRepository->findOneBy(['customer' => $this->getUser()->getId()], ['id' => 'desc']);
         
        if(isset($lastUserEvent) && !empty($lastUserEvent)){

            $haircutPrice = $lastUserEvent->getHaircut()->getPrice();
        
            require_once('../vendor/autoload.php'); 

            \Stripe\Stripe::setApiKey('sk_test_51IUKvkAez1VhFKwvtERCpj2IulQqmxVUCMx9sps8QNf0AhUoYZuoErBOClKD0hLhMJaDC6Quu67B6j8JGUEdf2tk00nVCIAkAY');

            $intent = \Stripe\PaymentIntent::create([
                'amount' => $haircutPrice*100,
                'currency' => 'eur'
            ]);

            return $this->render('event/onlinePayment.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'intent' => $intent]);   
        }else{
            return $this->redirectToRoute('event_order_summary');
        }                   
    } 

    /**
     * @Route("/user/event/add", name="event_add", methods={"GET"}) 
     */
    public function eventAdd(Request $request, EventRepository $eventRepository, EventNotValidatedRepository $eventNotValidatedRepository, ImageRepository $imageRepository): Response
    {
        $lastUserEvent = $eventNotValidatedRepository->findOneBy(['customer' => $this->getUser()->getId()], ['id' => 'desc']);

        if(isset($lastUserEvent) && !empty($lastUserEvent)){

            $event = new Event();
            $event->setTitle($lastUserEvent->getTitle());
            $event->setStart($lastUserEvent->getStart());
            $event->setEnd($lastUserEvent->getEnd());
            $event->setMember($lastUserEvent->getCustomer());
            $event->setHaircut($lastUserEvent->getHaircut());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            $eventNewRegistrer = $eventRepository->findOneBy(['member' => $this->getUser()->getId()], ['id' => 'desc']);
            

            if($lastUserEvent->getStart() === $eventNewRegistrer->getStart() && $lastUserEvent->getEnd() === $eventNewRegistrer->getEnd()){
                $entityManager->remove($lastUserEvent);
                $entityManager->flush();

                $dateOfDay = date("d-m-Y");
                
                return $this->render('bill.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'eventNewRegistrer' => $eventNewRegistrer,'dateOfDay' => $dateOfDay]);
            }           
        }
        elseif(empty($lastUserEvent)){
            $eventNewRegistrer = $eventRepository->findOneBy(['member' => $this->getUser()->getId()], ['id' => 'desc']);
            $dateOfDay = date("d-m-Y");
            return $this->render('bill.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'eventNewRegistrer' => $eventNewRegistrer,'dateOfDay' => $dateOfDay]);
        }
    }

    /**
     * @Route("/user/bill", name="user_bill_upload", methods={"GET"}) 
     */
    public function billUpload(Request $request, EventRepository $eventRepository, ImageRepository $imageRepository, Pdf $knpSnappyPdf): Response
    {

        $eventNewRegistrer = $eventRepository->findOneBy(['member' => $this->getUser()->getId()], ['id' => 'desc']);
        $dateOfDay = date("d-m-Y");
         

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('uploadBill.html.twig', [
            'eventNewRegistrer' => $eventNewRegistrer,'dateOfDay' => $dateOfDay
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html); 
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        return new Response($dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]));
        /*
        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'Facture.pdf'
        );*/
    }
}

 