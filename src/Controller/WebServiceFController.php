<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Personne;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Repository\PersonneRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;






class WebServiceFController extends AbstractController
{
    /**
     * @Route("/web/service/f", name="app_web_service_f", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        return new JsonResponse(['personne'=>'houssam'])  ;
    }


    
    /**
     * @Route("/GetPersonne/{id}", name="showRow" , methods={"GET"})
     */
    
    public function ShowRow(PersonneRepository $personneRepository,$id): JsonResponse
    {  
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $personnes = $personneRepository->find($id);
        $JsonContent = $serializer->serialize($personnes, 'json');
        return JsonResponse::fromJsonString($JsonContent);                
           
    }   
    

    
     /**
     * @Route("/insertJS", name="InsertJS",methods={"POST"})
     */
    public function createPersonne(Request $request, ManagerRegistry $doctrine,PersonneRepository $personneRepository): JsonResponse
    {   

        $jsContent=$request->getContent();
        $arraycontent=json_decode($jsContent, true);
        $exception;
        $entityManager = $doctrine->getManager();
        // $request = Request::createFromGlobals(); 
        $personne1 = $personneRepository->findOneBy(['email'=>$arraycontent['email']]);
        if ($personne1) 
        {   
            //throw $this->createNotFoundException('This email is already taken..Try another one');
            $exception="This email is already taken..Try another one \n ";
            return new JsonResponse(['exception'=> $exception ]);
        }

        else {
            $personne = new Personne();
            $personne->setName($arraycontent['name']) ;
            $personne->setFamilyName($arraycontent['familyname']);
            $personne->setEmail($arraycontent['email']);
            $personne->setBirthyear($arraycontent['Birthyear']);
            $personne->setVille($arraycontent['ville']);
            $personne->setNumTel($arraycontent['numtel']);
            $date1=date_create(date('Y-m-d'));
            $date2=date_create($personne->getBirthyear());
            $diff=date_diff($date1 , $date2);
            $personne->setAge($diff->format(" %y years , %m months and %d days"));
            $entityManager->persist($personne);
            $entityManager->flush();
            return new JsonResponse(['feedback'=>('Saved new personne with id '.$personne->getId())]);
        }

    } 
}
