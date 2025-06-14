<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanelPrincipalController extends AbstractController
{
    #[Route('/panel', name: 'panel_principal')]
    public function index(): Response
    {
        return $this->render('panel/moduloPrincipal.html.twig');
    }
}
