<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Redirect the user to the API documentation.
     */
    #[Route(path: '/', name: 'index')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app.swagger_ui');
    }
}