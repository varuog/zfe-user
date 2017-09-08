<?php

namespace ZfeUser\Action\Api\User;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use ZfeUser\Service\UserService;
use ZfeUser\Hateoas\Jsonapi\Hydrator\UserHydrator;
use ZfeUser\Model\User;
use ZfeUser\Hateoas\Jsonapi\Document\UserDocument;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use Zend\I18n\Translator\TranslatorInterface;
use ZfeUser\Middleware\JsonApiDispatcherMiddleware;

class UserAssignRoleAction implements ServerMiddlewareInterface
{

    private $userService;
    private $userHydrator;
    private $userDocuemnt;
    private $translator;

    public function __construct(UserService $userService, UserHydrator $userHydrator, UserDocument $userDoc, TranslatorInterface $translator)
    {
        $this->userService = $userService;
        $this->userHydrator = $userHydrator;
        $this->userDocuemnt = $userDoc;
        $this->translator = $translator;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {


         $jsonApi= $request->getAttribute(JsonApiDispatcherMiddleware::JSON_API_PROC);
        $user = new User();

        $jsonApi->hydrate($this->userHydrator, $user);
        $user->setSlug($request->getAttribute('slug'));
        $user = $this->userService->manageRole($user, false);


        if ($user instanceof User) {
            return $jsonApi->respond()->ok($this->userDocuemnt, $user);
        }


        $errorDoc = new ErrorDocument();
        $errorDoc->setJsonApi(new JsonApiObject("1.0"));
        return $jsonApi->respond()->notFound($errorDoc);
    }
}
