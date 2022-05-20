<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Repository\PersonneRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class WebServiceSController extends AbstractController
{
    /**
     * @Route("/web/service/s", name="app_web_service_s", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        return new JsonResponse(['personne'=>'zineb']);
    }

    /**
     * @Route("/getdata", name="getdata" , methods={"GET"})
     */
    public function afficherData(Request $request,PersonneRepository $personneRepository): JsonResponse
    {   

        
        
        $personnes = $personneRepository->findBy(array('supprimer' => false));
        if (!$personnes) {
            throw $this->createNotFoundException(
                'No personne founded'
            );
        }
        
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $JsonContent=$serializer->serialize($personnes,'json');
        return JsonResponse:: fromJsonString($JsonContent);                  
           
    }


    /**
     * @Route("/updating/{id}", name="Update",methods={"POST"})
     */
    public function UpdatePersonne(ManagerRegistry $doctrine,PersonneRepository $personneRepository,$id): JsonResponse
    {   
        $request = Request::createFromGlobals();
        $JsonContent=$request->getContent();
        $list=json_decode($JsonContent,true);
        $exception;
        $entityManager = $doctrine->getManager();
        $personne = $personneRepository->find($id); ; 
        $personne1 = $personneRepository->findOneBy(['email' => $list['email']]);
      
        if ($personne1 && $personne->getEmail() != $list['email'] ) 
        {   
            //throw $this->createNotFoundException('This email is already taken..Try another one');
            $exception="This email is already taken..Try another one \n ";
            return new JsonResponse(['exception'=>$exception]);
        }

        else {
       
        $personne->setName($list['name']) ;
        $personne->setFamilyName($list['familyName']);
        $personne->setEmail($list['email']);
        $personne->setBirthyear($list['BirthYear']);
        $personne->setVille($list['ville']);
        $personne->setNumTel($list['numTel']);
        $date1=date_create(date('Y-m-d'));
        $date2=date_create($personne->getBirthyear());
        $diff=date_diff($date1 , $date2);
        $personne->setAge($diff->format(" %y years , %m months and %d days"));
        $entityManager->flush();
        return new JsonResponse(['message'=>'les modifications sont bien enregistées']);
        }

    } 
}
