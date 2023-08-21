<?php

namespace App\Controller;

use App\Entity\Figures;
use App\Form\FigureForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FigureController extends AbstractController 
{

    #[Route('/creation-figure', name:'creation')]
    public function create (Request $request) :Response
    {
        $figure = new Figures();
        $form = $this->createForm(FigureForm::class, $figure);
        return $this->render('edition/new_figure.html.twig', [
            'figure_form' => $form]);

    }

}