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
        $result = $figures->findForHome();
        // dd($result);
        return $this->render('home/index.html.twig',['result' => $result]);
    }
}
