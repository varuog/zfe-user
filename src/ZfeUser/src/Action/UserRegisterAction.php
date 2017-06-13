<?php

namespace ZfeUser\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Zend\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use ZfeUser\Service\UserService;

class UserRegisterAction implements ServerMiddlewareInterface {

    private $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {

        $user = new \ZfeUser\Model\User();
        $user->setUsername('gsark');
        $user->setUsername('gsark');
        $user->setEmail('gsarkar.dev@gmail.com');
        $user->setPassword('foobar');
        $user->setFullName('gsarkar');

        $this->userService->register($user);

        $renderResponse = $this->userService->getOptions()->getResponseType();
        
        if ($renderResponse instanceof \Zend\Diactoros\Response\HtmlResponse) {

            return new $renderResponse($this->template->render('app::home-page', ['user' => $user]));
        }

        return new $renderResponse(['user' => $user]);
    }


}
