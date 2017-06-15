<?php

namespace ZfeUser\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Zend\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use ZfeUser\Service\UserService;
use ZfeUser\Hateoas\Jsonapi\Hydrator\UserHydrator;
use WoohooLabs\Yin\JsonApi\Request\Request as JsonApiRequest;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use ZfeUser\Model\User;

class UserRegisterAction implements ServerMiddlewareInterface {

    private $userService;
    private $userHydrator;

    public function __construct(UserService $userService, UserHydrator $userHydrator) {
        $this->userService = $userService;
        $this->userHydrator = $userHydrator;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {

        $user = new User();
        /**
        $user->setUsername('gsark');
        $user->setEmail('gsarkar.dev@gmail.com');
        $user->setPassword('foobar');
        $user->setFullName('gsarkar');
         * 
         */
        
        $defaultExpFactory = new DefaultExceptionFactory();
        $jsonapiRequest=new JsonApiRequest($request, $defaultExpFactory);
        
        $this->userHydrator->hydrate($jsonapiRequest, $defaultExpFactory, $user);
        $this->userService->register($user);

        $renderResponse = $this->userService->getOptions()->getResponseType();
        
        if ($renderResponse instanceof \Zend\Diactoros\Response\HtmlResponse) {

            return new $renderResponse($this->template->render('app::home-page', ['user' => $user]));
        }

        return new $renderResponse(['user' => $user]);
    }


}
