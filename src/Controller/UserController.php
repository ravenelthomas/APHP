<?php

namespace App\Controller;


use App\Repository\CalendarRepository;
use App\Entity\Calendar;
use App\Form\CalendarType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/user')]
class UserController extends AbstractController
{

    // private $calendarRepository;

    // public function __construct(CalendarRepository $calendarRepository)
    // {
    //     $this->calendarRepository = $calendarRepository;
    // }

    public function __construct()
    {

    }
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(CalendarRepository $calendarRepository): Response
    {
        $events = $calendarRepository->findByIdUser($this->getUser()->getId());
        $name = $this->getUser()->getName();
        $surname = $this->getUser()->getSurname();
        $rdvs = [];
        if(sizeof($events) > 0){
            foreach ($events as $event) {
                $rdvs[] = [
                    'id' => $event->getId(),
                    'start' => $event->getStart()->format('Y-m-d H:i:s'),
                    'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                    'title' => $event->getTitle(),
                    'description' => $event->getDescription(),
                    'categories' => $event->getCategories(),
                    'name' => $name,
                    'surname' => $surname,
                ];
            }
        }
        else{
            $rdvs[] = [
                'id' => '',
                'start' => '',
                'end' => '',
                'title' => '',
                'description' => '',
                'categories' => '',
                'name' => $name,
                'surname' => $surname,
            ];
        }
            
        $data = $rdvs;
        return $this->render(
            'user/index.html.twig',
            compact('data'),
            

        );
}
#[Route('/calendarAdd', name: 'app_user_calendar_add', methods: ['GET', 'POST'])]
    public function addUserCalendar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar, [
            'isAdmin' => in_array('ROLE_ADMIN', $this->getUser()->getRoles()),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($calendar);
            $entityManager->flush();

            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/newUser.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
            'user' => $this->getUser(),
        ]);
    }
}

