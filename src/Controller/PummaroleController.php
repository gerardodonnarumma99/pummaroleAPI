<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

use App\Entity\Timers;
use Doctrine\Bundle\DoctrineBundle\Repository\TimersEntityRepository;
use App\Entity\TimerType;
use Doctrine\Bundle\DoctrineBundle\Repository\TimerTypeEntityRepository;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\UserEntityRepository;

class PummaroleController extends AbstractController
{

    /**
     * @param
     * @return Response
     *
     * @Route("/api/v1/timer", methods={"post"})
     *
     * @SWG\Post(
     *      description="Crea un timer",
     *       @SWG\Parameter(
     *          name="body",
     *          description="Dati di un timer",
     *          in="body",
     *          required=true,
     *          @SWG\schema(
	 *     		type="array",
	 *          	@SWG\items(
	 *          		type="object",
	 *              	@SWG\Property(property="user_id", type="string"),
	 *              	@SWG\Property(property="start_date", type="string"),
     *                  @SWG\Property(property="end_date", type="string"),
     *                  @SWG\Property(property="status", type="string"),
     *                  @SWG\Property(property="timer_type", type="integer"),
     *                  @SWG\Property(property="title", type="string"),
     *                  @SWG\Property(property="description", type="string"),
     *                  @SWG\Property(property="first_cycle", type="string")
	 *          	),
	 *      )
     *     )
     *     ),
     * @SWG\Response(
     *          response=204,
     *          description="Timer creato",
     *     ),
     * @SWG\Response(
     *          response=400,
     *          description="Errore nella creazione del timer",
     *     )
     * )
     * @SWG\Tag(name="Timers")
     *
     * */
    public function addTimer(Request $request)
    {
        try{
            $timerRequest=json_decode($request->getContent(),1,512,JSON_THROW_ON_ERROR);

            $timer=new Timers();

            $timer->setUser($timerRequest['user_id']);

            //Setto le date
            $timer->setStartDate(new \DateTime($timerRequest['start_date']));
            if(empty($timerRequest['end_date']))
                $timer->setEndDate(null);
            else
                $timer->setEndDate(new \DateTime($timerRequest['end_date']));

            //Status
            $timer->setStatus($timerRequest['status']);

            //Trovo il TimerType
            $repository=$this->getDoctrine()->getRepository(TimerType::class);
            $timerType=$repository->find($timerRequest['timer_type']);
            $timer->setTimerType($timerType);

            //Title
            $timer->setTitle($timerRequest['title']);

            //Description
            $timer->setDescription($timerRequest['description']);

            //Se è il primo timer della giornata lo marco primo
            $repositoryTimers=$this->getDoctrine()->getRepository(Timers::class);
            if($repositoryTimers->getTimerFirstDay($timerRequest['user_id']))
                $timer->setFirstCycle('yes');
            else
                $timer->setFirstCycle($timerRequest['first_cycle']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($timer);
            $entityManager->flush();

            $result=[
                'id' => $timer->getId(),
                'user_id' =>$timer->getUser(),
                'startDate' => $timer->getStartDate(),
                'end_date' => $timer->getEndDate(),
                'timer_type' => $timer->getTimerType()->getId(),
                'first_cycle' => $timer->getFirstCycle()
            ];
            
            $jsonContent=$this->get('serializer')->serialize($result,'json');

            return new Response($jsonContent,200);
        }
        catch(\Exception  $exception) {
            return new Response($exception->getMessage(), 400);
        }

    }

    /**
     * @param $id
     * @return JsonResponse
     *
     * @Route("/api/v1/timer/{id}", methods={"get"})
     *
     * @SWG\Get(
     *      description="Recupera l'ultimo timer dato l'id di un utente",
     *      @SWG\Parameter(
     *          name="id",
     *          description="id dell'utente",
     *          in="path",
     *          type="integer",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Timer trovato",
     *          @SWG\Schema(
     *                  type="object",
	 *              	@SWG\Property(property="user_id", type="string"),
	 *              	@SWG\Property(property="start_date", type="string"),
     *                  @SWG\Property(property="end_date", type="string"),
     *                  @SWG\Property(property="status", type="string"),
     *                  @SWG\Property(property="timer_type", type="integer"),
     *                  @SWG\Property(property="type", type="string"),
     *                  @SWG\Property(property="title", type="string"),
     *                  @SWG\Property(property="description", type="string"),
     *                  @SWG\Property(property="duration", type="integer"),
     *                  @SWG\Property(property="first_cycle", type="string")
     *      )
     *     ),
     *      @SWG\Response(
     *          response=400,
     *          description="Timer non trovato",
     *     )
     * )
     * @SWG\Tag(name="Timers")
     *
     * */
    public function getTimer($id,Request $request)
    {
        $repository=$this->getDoctrine()->getRepository(Timers::class);
        $result=$repository->getTimersFromUserId($id);

        if(!$result)
           return new JsonResponse([],204);

        return new JsonResponse($result,200);
    }

    /**
     * @param $id
     * @return JsonResponse
     *
     * @Route("/api/v1/tomatos/{id}", methods={"get"})
     *
     * @SWG\Get(
     *      description="Recupera l'ultimo tomato dato l'id di un utente",
     *      @SWG\Parameter(
     *          name="id",
     *          description="id dell'utente",
     *          in="path",
     *          type="integer",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Tomato trovato",
     *          @SWG\Schema(
     *                  type="object",
     *              	@SWG\Property(property="user_id", type="string"),
     *              	@SWG\Property(property="start_date", type="string"),
     *                  @SWG\Property(property="end_date", type="string"),
     *                  @SWG\Property(property="status", type="string"),
     *                  @SWG\Property(property="timer_type", type="integer"),
     *                  @SWG\Property(property="type", type="string"),
     *                  @SWG\Property(property="title", type="string"),
     *                  @SWG\Property(property="description", type="string"),
     *                  @SWG\Property(property="duration", type="integer"),
     *                  @SWG\Property(property="first_cycle", type="string")
     *          )
     *     ),
     *      @SWG\Response(
     *          response=400,
     *          description="Tomato non trovato",
     *     )
     * )
     * @SWG\Tag(name="Timers")
     *
     * */
    public function getTomato($id,Request $request)
    {
        $repository=$this->getDoctrine()->getRepository(Timers::class);
        $result=$repository->getTomatosFromUserId($id);

        if(!$result)
            return new JsonResponse([],204);

        return new JsonResponse($result,200);
    }

    /**
     * @param
     * @return Response
     *
     * @Route("/api/v1/timer/{id}", methods={"put"})
     *
     * @SWG\Put(
     *      description="Modifica un timer dato l'id",
     *       @SWG\Parameter(
     *          name="id",
     *          description="id dell'utente",
     *          in="path",
     *          type="integer",
     *          required=true
     *     )
     *     ),
     * @SWG\Response(
     *          response=204,
     *          description="Timer modificato",
     *     ),
     * @SWG\Response(
     *          response=400,
     *          description="Errore nella modifica del timer",
     *     )
     * )
     * @SWG\Tag(name="Timers")
     *
     * */
    public function putTimer($id,Request $request)
    {
        try{
            $timerRequest=json_decode($request->getContent(),1,512,JSON_THROW_ON_ERROR);

            $entityManager=$this->getDoctrine()->getManager();
            $timer=$entityManager->getRepository(Timers::class)->find($id);
            if($timer==null)
                return new JsonResponse(null,400);

            if(empty($timerRequest['end_date']))
                $timer->setEndDate(null);
            else
                $timer->setEndDate(new \DateTime($timerRequest['end_date']));

            //Status
            $timer->setStatus($timerRequest['status']);

            $entityManager->persist($timer);
            $entityManager->flush();
            $jsonContent=$this->get('serializer')->serialize($timer,'json');

            return new Response($jsonContent,200);
        }
        catch(\Exception  $exception) {
            return new Response($exception->getMessage(), 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     *
     * @Route("/api/v1/nextTimer/{idUser}", methods={"get"})
     *
     * @SWG\Get(
     *      description="Ritorna il prossimo timer dello step di un dato utente, se calcolabile",
     *      @SWG\Parameter(
     *          name="idUser",
     *          description="id dell'utente",
     *          in="path",
     *          type="integer",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Prossimo timer",
     *          @SWG\Schema(
     *                  type="object",
     *              	@SWG\Property(property="type", type="string"),
     *              	@SWG\Property(property="duration", type="integer"),
     *      )
     *     ),
     *      @SWG\Response(
     *          response=204,
     *          description="Prossimo timer non calcolabile",
     *     )
     * )
     * @SWG\Tag(name="Timers")
     *
     * */
    public function getStepCycle($idUser)
    {
        $cycle=[
            ['type'=>'tomato',
                'duration'=>"2"],
            ['type'=>'pause',
                'duration'=>"1"],
            ['type'=>'tomato',
                'duration'=>"2"],
            ['type'=>'pause',
                'duration'=>"1"],
            ['type'=>'tomato',
                'duration'=>"2"],
            ['type'=>'pause',
                'duration'=>"3"],
        ];

        $match=[];
        $repository=$this->getDoctrine()->getRepository(Timers::class);
        $result=$repository->getTomatosCycle($idUser);
        $i=0;
       
        //Primo, ma broken
        if(!$result)
        {
            array_push($match,$cycle[0]);
            return new JsonResponse($match,200);    
        }

        foreach($result as $arrayResult)
        {
            if($i==count($cycle)-1)
            {
                $match=[];
                array_push($match,$cycle[0]);
                return new JsonResponse($match,200);
            }
            if( ($arrayResult['type']==$cycle[$i]['type'])&&($arrayResult['duration']==$cycle[$i]['duration']) )
            {
                $match=[];
                array_push($match,$cycle[$i+1]);
            }
            else{
                $match=[];
                break;
            }
            $i++;
        }

       if(count($match)==0)
           return new JsonResponse([],204);

        return new JsonResponse($match,200);
    }

    /**
     * @param $id
     * @return JsonResponse
     *
     * @Route("/api/v1/pomodoroCycle/{idUser}", methods={"get"})
     *
     * @SWG\Get(
     *      description="Controlla se un dato utente ha completato un ciclo",
     *      @SWG\Parameter(
     *          name="idUser",
     *          description="id dell'utente",
     *          in="path",
     *          type="integer",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Ciclo completato",
     *     ),
     *      @SWG\Response(
     *          response=204,
     *          description="Ciclo non completato",
     *     )
     * )
     * @SWG\Tag(name="Timers")
     *
     * */
    public function getPomodoroCycle($idUser)
    {
        $cycle=[
            ['type'=>'tomato',
                'duration'=>"2"],
            ['type'=>'pause',
                'duration'=>"1"],
            ['type'=>'tomato',
                'duration'=>"2"],
            ['type'=>'pause',
                'duration'=>"1"],
            ['type'=>'tomato',
                'duration'=>"2"],
            ['type'=>'pause',
                'duration'=>"3"],
        ];

        $repository=$this->getDoctrine()->getRepository(Timers::class);
        $result=$repository->getCycle($idUser);
        $flag=false;
        $i=0;
    
        if(count($result)!=6)
            return new JsonResponse($flag,204);

        foreach($result as $arrayResult)
        {
            if( ($arrayResult['type']==$cycle[$i]['type'])&&($arrayResult['duration']==$cycle[$i]['duration']) )
            {
                $flag=true;
            }
            else{
                $flag=false;
                break;
            }

            $i++;
        }

        if($flag) {
            return new JsonResponse($flag,200);
        }
        else {
            return new JsonResponse($flag,204);
        }
    }

    /**
     * @param $idUser
     * @param $dateSelected
     * @return JsonResponse
     *
     * @Route("/api/v1/lastEvent/{idUser}/{dateSelected}", methods={"get"})
     *
     * @SWG\Get(
     *      description="Ritorna gli ultimi task di un utente, di una data selezionata",
     *      @SWG\Parameter(
     *          name="idUser",
     *          description="id dell'utente",
     *          in="path",
     *          type="integer",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          name="dataSelected",
     *          description="data selezionata (Fomato: YYYY-MM-DD)",
     *          in="path",
     *          type="string",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Timers trovati",
     *          @SWG\Schema(
     *                  type="object",
     *              	@SWG\Property(property="start_date", type="string"),
     *              	@SWG\Property(property="duration", type="string"),
     *                  @SWG\Property(property="status", type="string"),
     *                  @SWG\Property(property="title", type="string"),
     *                  @SWG\Property(property="description", type="string"),
     *                  @SWG\Property(property="type", type="string"),
     *          )
     *     ),
     *      @SWG\Response(
     *          response=204,
     *          description="Timers non trovati",
     *     )
     * )
     * @SWG\Tag(name="Timers")
     *
     * */
    public function getLastTimer($idUser,$dateSelected)
    {
        try
        {
            $repository=$this->getDoctrine()->getRepository(Timers::class);
            $result=$repository->getLastEvent($idUser,$dateSelected);

            if(!$result)
                return new JsonResponse([],204);

            return new JsonResponse($result,200);
        }
        catch(\Exception  $exception) {
            return new Response($exception->getMessage(), 400);
        }
    }

    /**
     * @param $idUser
     * @param $dateSelected
     * @return JsonResponse
     *
     * @Route("/api/v1/timersByDateAndStatus/{idUser}/{startDate}/{status}", methods={"get"})
     *
     * @SWG\Get(
     *      description="Ritorna tutte le date di terminazione di un timer, di un determinato utente, a partire da una data",
     *      @SWG\Parameter(
     *          name="idUser",
     *          description="id dell'utente",
     *          in="path",
     *          type="integer",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          name="startDate",
     *          description="data di partenza (Formato: YYYY-MM-DD)",
     *          in="path",
     *          type="string",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          name="status",
     *          description="stato del timer (done o doing o broken)",
     *          in="path",
     *          type="string",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Timers trovati",
     *          @SWG\Schema(
     *                  type="object",
     *              	@SWG\Property(property="end_date", type="string"),
     *          )
     *     ),
     *      @SWG\Response(
     *          response=204,
     *          description="Timers non trovati",
     *     )
     * )
     * @SWG\Tag(name="Timers")
     *
     * */
    public function getTimersByStartDateAndStatus($idUser,$startDate,$status)
    {
        try
        {
            if($status!='done'&&$status!='done'&&$status!='broken')
                return new JsonResponse([],204);

            $repository=$this->getDoctrine()->getRepository(Timers::class);
            $result=$repository->getTimerByDateAndStatus($idUser,$startDate,$status);

            if(!$result)
                return new JsonResponse([],204);

            return new JsonResponse($result,200);
        }
        catch(\Exception  $exception) {
            return new Response($exception->getMessage(), 400);
        }
    }
}
