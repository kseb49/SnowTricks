<?php

namespace App\Controller;

use App\Repository\FiguresRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(FiguresRepository $figures): Response
    {

        $res = $figures->findBy([],['creation_date' => 'desc']);
        // Get truncated description with specified width
        foreach ($res as $key => $value) {
            $shortDesc[] = [$value->getName() => mb_strimwidth($value->getDescription(),0,75,' ...')];
        }
        return $this->render('home/index.html.twig',['result' => $figures->findBy([],['creation_date' => 'desc']), 'short_desc' => $shortDesc]);

    }


}
