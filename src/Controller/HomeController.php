<?php

namespace App\Controller;

use App\Repository\FiguresRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{


    #[Route('/', name: 'home')]
    /**
     * Display the home page
     *
     * @param Request $request
     * @param FiguresRepository $figures
     * @return Response
     */
    public function index(Request $request, FiguresRepository $figures): Response
    {

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $figures->findPaginated($offset);
        // Get truncated description with specified width.
        foreach ($paginator as $value) {
            $shortDesc[] = [$value->getName() => mb_strimwidth($value->getDescription(),0,75,' ...')];
        }

        return $this->render('home/index.html.twig',['short_desc' => $shortDesc, 'messages' => $paginator, 'previous' => $offset - FiguresRepository::PAGINATOR_PER_PAGE, 'next' => min(count($paginator), $offset + FiguresRepository::PAGINATOR_PER_PAGE)]);

    }


}
