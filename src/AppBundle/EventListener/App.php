<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class App
{
    
    
    // Méthode pour ajouter le "beta" à une réponse
    protected function displayBeta(Response $response)
    {
        $content = $response->getContent();
        
        // Code à rajouter
        $html = '<span style="color: red; font-size: 0.6em;"> - The Event Kernel.Response  !</span>';
        
        // Insertion du code dans la page, dans le <h1> du header
        $content = preg_replace('#<span></span>#',
            '<h1>$1'.$html.'</h1>',
            $content,
            1);
        
        // Modification du contenu dans la réponse
        $response->setContent($content);
        
        
        return $response;
    }
    
    public function processBeta(FilterResponseEvent $event)
    {
            
            $response = $event->getResponse();
            $response = $this->displayBeta($event->getResponse());
            $event->setResponse($response);
    }
    
   
}

