<?php

namespace ZfeUser\Action\User;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use ZfeUser\Service\UserService;
use ZfeUser\Hateoas\Jsonapi\Hydrator\UserHydrator;
use ZfeUser\Model\User;
use ZfeUser\Hateoas\Jsonapi\Document\UserDocument;
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use Zend\I18n\Translator\TranslatorInterface;

class UserAssignRoleAction implements ServerMiddlewareInterface
{

    private $userService;
    private $userHydrator;
    private $userDocuemnt;
    private $jsonApi;
    private $translator;

    public function __construct(JsonApi $jsonApi, UserService $userService, UserHydrator $userHydrator, UserDocument $userDoc, TranslatorInterface $translator)
    {
        $this->userService = $userService;
        $this->userHydrator = $userHydrator;
        $this->userDocuemnt = $userDoc;
        $this->jsonApi = $jsonApi;
        $this->translator = $translator;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {


        $this->jsonApi->setRequest($request);
        $user = new User();

        $user->setSlug($request->getAttribute('slug'));
        $user = $this->userService->manageRole($user, false);


        if ($user instanceof User)
        {
            return $this->jsonApi->respond()->ok($this->userDocuemnt, $user);
        }


        $errorDoc = new ErrorDocument();
        $errorDoc->setJsonApi(new JsonApiObject("1.0"));
        return $this->jsonApi->respond()->notFound($errorDoc);
    }

}
