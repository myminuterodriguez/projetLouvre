<?php

namespace ER\BilleterieBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ER\BilleterieBundle\Form\CommandeType;
use ER\BilleterieBundle\Form\ClientType;
use ER\BilleterieBundle\Entity\Client;
use ER\BilleterieBundle\Entity\Commande;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $commande = new Commande();
        $form = $this->get('form.factory')->create(CommandeType::class, $commande);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $session->set('Commande', $commande);
            
            return $this->redirectToRoute('er_billeterie_Client');
        }
        
        return $this->render('ERBilleterieBundle:Default:index.html.twig', array('form' => $form->createView(),
            ));
    }
 
    public function clientAction(Request $request)
    {
        $session = $request->getSession();
        $commande = $session->get('Commande');
        $nombre = $commande->getNombre();
        if ($nombre>0) {
             $clients = array();
        for ($i=0; $i<$nombre; $i++){
            $client[$i] = new Client();
            $commande->addClient($client[$i]);
            
        }
        $form = $this->createFormBuilder($clients);
        for ($i = 0; $i < $nombre; $i++) {
            $form->add($i, ClientType::class, array(
                'label' => 'Visiteur ' . ($i + 1) . ':'
            ));
        }
        $form->add('save',   SubmitType::class);
        $formClient = $form->getForm();
        if ($request->isMethod('POST') && $formClient->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commande);
            
            $em->flush();
            
            return $this->redirectToRoute('er_billeterie_homepage');
        }
       /* $formClient = $this->get('form.factory')->create(ClientType::class, $clients);*/
        return $this->render('ERBilleterieBundle:Default:client.html.twig', array('formClient' => $formClient->createView(),
            ));
        }
       return $this->redirectToRoute('er_billeterie_homepage');
     
    }
}
