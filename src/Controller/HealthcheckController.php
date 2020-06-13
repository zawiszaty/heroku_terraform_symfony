<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HealthcheckController extends AbstractController
{
    /**
     * @Route("/", name="healthcheck", methods={"GET"})
     */
    public function healthcheckAction(): Response
    {
        return new Response('health', Response::HTTP_OK);
    }
}
