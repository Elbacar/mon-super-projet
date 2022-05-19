<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Personne;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Repository\PersonneRepository;

class HelloController extends AbstractController
{
    /**
     * @Route("/hello-word", name="app_hello")
     */
    public function index(): Response
    {
        return $this->render('hello/index.html.twig', [
            'controller_name' => 'Word',
        ]);
    }

    /**
     * @Route("/hello-word/{name}", name="hello-name")
     */
    public function ShowName($name): Response
    {
        return $this->render('hello/index.html.twig', [
            'controller_name' => $name,
        ]);
    }
   
    /**
     * @Route("/formulaire", name="formulaire")
     */
    public function ShowFormulaire(): Response
    {   
        $exception='';
        return $this->render('hello/formulaire.html.twig',['exception'=> $exception ]);
    }


    /**
     * @Route("/getInfos", name="SelfInfos" , methods={"GET"})
     */
    public function CalculAge(Request $request): Response
    {   
        $personne = new Personne();
       // $request = Request::createFromGlobals(); 
        $personne->setName($request->get('name')) ;
        $personne->setFamilyName($request->get('familyname'));
        $personne->setEmail($request->get('email'));
        $personne->setBirthyear($request->get('Birthyear'));
        $personne->setVille(($request->get('ville')));
        $personne->setNumTel(($request->get('numtel')));
        $date1=date_create(date('Y-m-d'));
        $date2=date_create($personne->getBirthyear());
        $diff=date_diff($date1 , $date2);
        $personne->setAge($diff->format(" %y years , %m months and %d days"));
        return $this->render('hello/index.html.twig', [ 'personne'=>$personne]); 
        
    }   
    
    
    /**
     * @Route("/insert", name="InsertInfos",methods={"GET"})
     */
    public function createPersonne(ManagerRegistry $doctrine,PersonneRepository $personneRepository): Response
    {   
        $exception;
        $entityManager = $doctrine->getManager();
        $request = Request::createFromGlobals(); 
        $personne1 = $personneRepository->findOneBy(['email' => $request->get('email')]);
        if ($personne1) 
        {   
            //throw $this->createNotFoundException('This email is already taken..Try another one');
            $exception="This email is already taken..Try another one \n ";
            return $this->render('hello/formulaire.html.twig',['exception'=> $exception ]);
        }

        else {
            $personne = new Personne();
            $personne->setName($request->get('name')) ;
            $personne->setFamilyName($request->get('familyname'));
            $personne->setEmail($request->get('email'));
            $personne->setBirthyear($request->get('Birthyear'));
            $personne->setVille(($request->get('ville')));
            $personne->setNumTel(($request->get('numtel')));
            $date1=date_create(date('Y-m-d'));
            $date2=date_create($personne->getBirthyear());
            $diff=date_diff($date1 , $date2);
            $personne->setAge($diff->format(" %y years , %m months and %d days"));
            $entityManager->persist($personne);
            $entityManager->flush();
            return new Response('Saved new personne with id '.$personne->getId());
        }

    } 
     
                                                            
     /**
     * @Route("/getData", name="getData" , methods={"GET"})
     */
    public function afficherData(Request $request,PersonneRepository $personneRepository): Response
    {   
        $personnes = $personneRepository->findBy(array('supprimer' => false));
        if (!$personnes) {
            throw $this->createNotFoundException(
                'No personne founded'
            );
        }
        
    

        return $this->render('Table.html.twig', ['personnes'=> $personnes]);                  
           
    }   


    /**
     * @Route("/showRow/{id}", name="showRow" , methods={"GET"})
     */
    
    public function ShowRow(Request $request,PersonneRepository $personneRepository,$id): Response
    {   
        $personnes = $personneRepository->find($id);
        
        return $this->render('hello/row.html.twig', ['personne'=> $personnes]);                  
           
    }    
    
    /**
     * @Route("/formulaire/{id}", name="formulaireUptade", methods={"GET"})
     */
    public function ShowFormulair(Request $request,PersonneRepository $personneRepository,$id): Response
    {   
      
        $personnes = $personneRepository->find($id);
        return $this->render('hello/UpdatingFormulaire.html.twig',['exception'=>'','personne'=> $personnes]);
    }

    /**
     * @Route("/update/{id}", name="Update",methods={"GET"})
     */
    public function UpdatePersonne(ManagerRegistry $doctrine,PersonneRepository $personneRepository,$id): Response
    {   
        $exception;
        $entityManager = $doctrine->getManager();
        $request = Request::createFromGlobals();
        $personne = $personneRepository->find($id); ; 
        $personne1 = $personneRepository->findOneBy(['email' => $request->get('email')]);
      
        if ($personne1 && $personne->getEmail() != $request->get('email') ) 
        {   
            //throw $this->createNotFoundException('This email is already taken..Try another one');
            $exception="This email is already taken..Try another one \n ";
            return $this->render('hello/UpdatingFormulaire.html.twig',['exception'=> $exception ,'personne'=>$personne ]);
        }

        else {
       
        $personne->setName($request->get('name')) ;
        $personne->setFamilyName($request->get('familyname'));
        $personne->setEmail($request->get('email'));
        $personne->setBirthyear($request->get('Birthyear'));
        $personne->setVille(($request->get('ville')));
        $personne->setNumTel(($request->get('numtel')));
        $date1=date_create(date('Y-m-d'));
        $date2=date_create($personne->getBirthyear());
        $diff=date_diff($date1 , $date2);
        $personne->setAge($diff->format(" %y years , %m months and %d days"));
        $entityManager->flush();
        return $this->redirectToRoute('getData');
        }

    } 
     
    
     /**
     * @Route("/remove/{id}", name="remove", methods={"GET"})
     */
    public function remove(ManagerRegistry $doctrine,Request $request,PersonneRepository $personneRepository,$id): Response
    {   
        $entityManager = $doctrine->getManager();
        $personne = $personneRepository->find($id);
        $entityManager->remove($personne);
        $entityManager->flush();
        return $this->redirectToRoute('getData');
    }

    /**
     * @Route("/hide/{id}", name="hide", methods={"GET"})
     */
    public function hide(ManagerRegistry $doctrine,Request $request,PersonneRepository $personneRepository,$id): Response
    {   
        $entityManager = $doctrine->getManager();
        $personne = $personneRepository->find($id);
        $personne->setSupprimer(true) ;
        $entityManager->flush();
        return $this->redirectToRoute('getData');
    }

}


 