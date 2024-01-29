<?php

namespace App\Controller;


use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $rdvs = [];
        foreach($events as $event){
            $rdvs = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'categories' => $event->getCategories(),
            ];
        }
        $data = json_encode($rdvs);
        return $this->render('user/index.html.twig', 
            compact('data'),
        
        );
    
    }

}

