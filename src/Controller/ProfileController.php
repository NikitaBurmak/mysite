<?php

namespace App\Controller;

use App\Controller\Trait\AuthenticationTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    use AuthenticationTrait;

    #[Route('/profile', name: 'profile')]
    public function profilePage(): Response
    {
        $redirect = $this->requireAuthOrRedirect('home');
        if ($redirect instanceof Response) {
            return $redirect;
        }

        return $this->render('profile/profile.html.twig');
    }
}
