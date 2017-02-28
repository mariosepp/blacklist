<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\NameCheckType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $message = array();
    	$form = $this->createForm(NameCheckType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
        	$nameString = str_replace(array('.', ','), '', $form->get('name')->getData());
            
            $this->handleUploadedFiles($form);
            
            $blacklist = $this->getList('blacklist');
            $noiselist = $this->getList('noiselist');
            
            if (strlen($nameString) >= 4) {
                $name = $this->get('app.name_checker')->checkName($nameString, $blacklist, $noiselist);
                
                if ($name->getResult() === 0) {
                    $message['message'] = ucwords($name->getFullName())." is on the blacklist.";
                    $message['type'] = 'alert-danger';
                } elseif ($name->getResult() < 5) {
                    $message['message'] = $form->get('name')->getData()." is a similar name with ".ucwords($name->getFullName()).", who is on the blacklist.";
                    $message['type'] = 'alert-danger';
                } else {
                    $message['message'] = $form->get('name')->getData()." is okay.";
                    $message['type'] = 'alert-success';
                }
            } else {
                $message['message'] = "Name has to be atleast 4 characters long.";
                $message['type'] = 'alert-danger';
            }
        }
        
        $params = array(
            'form' => $form->createView(),
            'message' => $message
        );
        
        return $this->render('default/index.html.twig', $params);
    }
    
    private function getList($listname)
    {
    	$list = array();
    	$dir = $this->getParameter('uploads_directory');
    	$file = $dir."/".$listname.".txt";
    	
    	if (file_exists($file)) {
    		$stream = fopen($file, 'r');
    		
    		while(($line = fgets($stream)) !== false) {
    			$list[] = strtolower(trim($line));
    		}
    	}
    	
    	return $list;
    }
    
    private function handleUploadedFiles($form) 
    {
        $blacklistFile = $form->get('blacklist_file')->getData();
        $noiselistFile = $form->get('noise_file')->getData();
        $dir = $this->getParameter('uploads_directory');
        
        if ($blacklistFile) {
            $blacklistFile->move(
                $dir,
                'blacklist.'.$blacklistFile->guessExtension()
            );
        }
        
        if ($noiselistFile) {
            $noiselistFile->move(
                $dir,
                'noiselist.'.$noiselistFile->guessExtension()
            );
        }
    }
}
