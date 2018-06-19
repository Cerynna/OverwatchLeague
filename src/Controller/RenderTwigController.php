<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RenderTwigController extends Controller
{

    public function currentStage()
    {
        return $this->render('render_twig/index.html.twig', [
            'currentStage' => 'RenderTwigController',
        ]);
    }
}
