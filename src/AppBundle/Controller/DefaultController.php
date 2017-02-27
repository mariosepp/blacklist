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
        $message = "";
    	$form = $this->createForm(NameCheckType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
        	$name = str_replace(array('.', ','), '', $form->get('name')->getData());
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
            
            $blacklist = $this->getList('blacklist');
            $noiselist = $this->getList('noiselist');
            
            $name = $this->get('app.name_checker')->checkName($name, $blacklist, $noiselist);
            
            if ($name->getResult() === 0) {
                $message = ucwords($name->getFullName())." is on the blacklist!";
            } elseif ($name->getResult() < 4) {
                $message = $form->get('name')->getData()." is a similar name with ".ucwords($name->getFullName()).", who is on the blacklist!";
            } else {
                $message = $form->get('name')->getData()." is okay!";
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
}
