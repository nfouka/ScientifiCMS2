<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Comment;
use AppBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Notifies post's author about new comments.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class CommentNotificationSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    
    /**
     * @var ContainerInterface
     */
    private $container  ; 
    
    /**
     * The kernel in which this event was thrown.
     *
     * @var HttpKernelInterface
     */
    private $kernel;
    
    /**
     * The current response object.
     *
     * @var Response
     */
    private $response;
    
    /**
     * @var string
     */
    private $sender;
    

    
    public function __construct(
        $sender , 
        \Swift_Mailer $mailer,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        ContainerInterface $container ,
        HttpKernelInterface $kernel
        )
    {
        
        $this->sender = $sender;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->container = $container ;
        $this->kernel = $kernel;

        
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::COMMENT_CREATED => 'onCommentCreated'
        ];
    }
    

    /**
     * @param GenericEvent $event
     */
    public function onCommentCreated(GenericEvent $event )
    {
        /** @var Comment $comment */
        $comment = $event->getSubject();
        $post = $comment->getPost();

        $linkToPost = $this->urlGenerator->generate('blog_post', [
            'slug' => $post->getSlug(),
            '_fragment' => 'comment_'.$comment->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $subject = $this->translator->trans('notification.comment_created');
        $body = $this->translator->trans('notification.comment_created.description', [
            '%title%' => $post->getTitle(),
            '%link%' => $linkToPost,
        ]) . ' Content us '.$post->getContent() ;

        // Symfony uses a library called SwiftMailer to send emails. That's why
        // email messages are created instantiating a Swift_Message class.
        // See https://symfony.com/doc/current/email.html#sending-emails
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($post->getAuthor()->getEmail())
            ->setFrom($this->sender)
            ->setBody($body, 'text/html')
        ;


            
        // In app/config/config_dev.yml the 'disable_delivery' option is set to 'true'.
        // That's why in the development environment you won't actually receive any email.
        // However, you can inspect the contents of those unsent emails using the debug toolbar.
        // See https://symfony.com/doc/current/email/dev_environment.html#viewing-from-the-web-debug-toolbar
        $this->mailer->send($message);
    }
    
    
    
    /**
     * Returns the current response object.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * Sets a new response object.
     *
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
    
}
