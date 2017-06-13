<?php

namespace App\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\UserService;
use Zend\Expressive\Template\TemplateRendererInterface;

class UserLoginAction implements ServerMiddlewareInterface {

    private $userService;
    private $template;

    public function __construct(UserService $userService, TemplateRendererInterface $template = null) {
        $this->userService = $userService;
        $this->template = $template;
    }

    /**
     * @todo Verify response type and throw error
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return \App\Action\renderResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {
        
        
                
        $user = new \App\Model\User();
        $user->setEmail('gsarkar.dev@gmail.com');
        $user->setPassword('foobar');

        $this->userService->setAuthUser($user);
        $authResult = $this->userService->authenticate(); 
        $renderResponse = $this->userService->getOptions()->getResponseType();

        $messages = $authResult->getMessages();

        if ($renderResponse == \Zend\Diactoros\Response\HtmlResponse::class) {
            return new $renderResponse($this->template->render('app::home-page', ['user' => implode(', ', $messages)]));
        }

        return new $renderResponse(['user' => implode(', ', $messages)]);
    }

}
