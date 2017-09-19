<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



namespace AppBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    
    private $templating;
    
    public function __construct(ContainerInterface $container)
    {
        $this->templating = $container->get('templating');
    }
    
    
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {

        return new Response( $this->templating->render('denied/forbiden.html.twig') , 403) ; 

        
    }
    
}