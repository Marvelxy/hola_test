<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PageController extends AbstractController
{
    public function index(): Response
    {
        if ($this->getUser())
        {
            $user = $this->getUser();
            $roles = $user->getRoles();
            return $this->render('page/index.html.twig', [
                'number' => $user->getRoles(),
            ]);
        }
        else{
            return $this->redirectToRoute('app_login', [], 302);
        }
    }

    public function page1(): Response
    {
        if ($this->getUser())
        {
            $user = $this->getUser();
            $roles = $user->getRoles();
            if (in_array("ADMIN", $roles) || (in_array("PAGE_1", $roles)))
            {
                return $this->render('page/page1.html.twig', [
                    'number' => $user->getRoles(),
                ]);
            }
            else{
                // return $this->redirectToRoute('app_login', [], 403);
                $this->denyAccessUnlessGranted($roles);
            }
        }
        else{
            return $this->redirectToRoute('app_login', [], 302);
        }
    }
}
