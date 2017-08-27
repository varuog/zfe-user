<?php

namespace ZfeUser\Action\User;

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
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Request\Request;
use Zend\Diactoros\Response;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;

class UserRevokeRoleAction implements ServerMiddlewareInterface
{

    private $userService;
    private $userHydrator;
    private $userDocuemnt;

    public function __construct(UserService $userService, UserHydrator $userHydrator, UserDocument $userDoc)
    {
        $this->userService = $userService;
        $this->userHydrator = $userHydrator;
        $this->userDocuemnt = $userDoc;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {

        $user = new User();
        $user->setSlug($request->getAttribute('slug'));
        $user = $this->userService->manageRole($user, true);

        $defaultExpFactory = new DefaultExceptionFactory();
        $jsonapi = new JsonApi(new Request($request, $defaultExpFactory), new Response(), $defaultExpFactory);

        if ($user instanceof User) {
            return $jsonapi->respond()->ok($this->userDocuemnt, $user);
        }


        $errorDoc = new ErrorDocument();
        $errorDoc->setJsonApi(new JsonApiObject("1.0"));
        return $jsonapi->respond()->notFound($errorDoc);
    }
}
