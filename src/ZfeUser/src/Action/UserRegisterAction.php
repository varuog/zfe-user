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
use ZfeUser\Hateoas\Jsonapi\Document;
use ZfeUser\Hateoas\Jsonapi\Transformer;
use ZfeUser\Model\User;
use ZfeUser\Hateoas\Jsonapi\Document\UserDocument;

class UserRegisterAction implements ServerMiddlewareInterface {

    private $userService;
    private $userHydrator;
    private $userDocuemnt;

    public function __construct(UserService $userService, UserHydrator $userHydrator, UserDocument $userDoc) {
        $this->userService = $userService;
        $this->userHydrator = $userHydrator;
        $this->userDocuemnt = $userDoc;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {

        $user = new User();
        $defaultExpFactory = new DefaultExceptionFactory();
        $jsonapiRequest = new JsonApiRequest($request, $defaultExpFactory);
        $jsonApi = new \WoohooLabs\Yin\JsonApi\JsonApi($jsonapiRequest, new \Zend\Diactoros\Response(), $defaultExpFactory, null);
        $jsonApi->hydrate($this->userHydrator, $user);

        try {

            $this->userService->register($user);
        } catch (\Zend\Mail\Transport\Exception\RuntimeException $ex) {
            //Ignore mail transport exception. can be logged or notify
        }

        return $jsonApi->respond()->ok($this->userDocuemnt, $user);
    }

}
