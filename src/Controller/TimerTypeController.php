<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

use App\Entity\TimerType;
use Doctrine\Bundle\DoctrineBundle\Repository\TimerTypeEntityRepository;

class TimerTypeController extends AbstractController
{
    /**
     * @return JsonResponse
     *
     * @Route("/api/v1/timersTypes", methods={"get"})
     *
     * @SWG\Get(
     *      description="Recupera tutti i tipi di timer",
     *      @SWG\Response(
     *          response=200,
     *          description="Tipi di timer trovati",
     *          @SWG\Schema(ref=@Model(type=TimerType::class))
     *     ),
     *      @SWG\Response(
     *          response=400,
     *          description="Tipi di timer non trovati",
     *     )
     * )
     * @SWG\Tag(name="Timers")
     *
     * */
    public function getAllTimerType(Request $request)
    {
        $repository=$this->getDoctrine()->getRepository(TimerType::class);
        $result=$repository->findAll();

        if(!$result)
            return new JsonResponse([],204);

        return new Response($this->get('serializer')->serialize($result,'json'),200);
    }
}
