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
use ZfeUser\Hateoas\Jsonapi\Transformer;
use ZfeUser\Model\User;
use ZfeUser\Hateoas\Jsonapi\Document\UserDocument;
use Zend\I18n\Translator\TranslatorInterface;
use ZfeUser\Hateoas\Jsonapi\Document;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Error;

class UserRegisterAction implements ServerMiddlewareInterface
{

    private $userService;
    private $userHydrator;
    private $userDocuemnt;

    /**
     *
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        UserService $userService,
        UserHydrator $userHydrator,
        UserDocument $userDoc,
        TranslatorInterface $translator
    ) {
        $this->userService   = $userService;
        $this->userHydrator  = $userHydrator;
        $this->userDocuemnt  = $userDoc;
        $this->translator    = $translator;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {

        $user                = new User();
        $defaultExpFactory   = new DefaultExceptionFactory();
        $jsonapiRequest      = new JsonApiRequest($request, $defaultExpFactory);
        $jsonApi             = new \WoohooLabs\Yin\JsonApi\JsonApi($jsonapiRequest, new \Zend\Diactoros\Response(), $defaultExpFactory, null);
        $jsonApi->hydrate($this->userHydrator, $user);

        try {
            $this->userService->register($user);
        } catch (\Zend\Mail\Transport\Exception\RuntimeException $ex) {
            //Ignore mail transport exception. can be logged or notify
        } catch (\MongoDuplicateKeyException $mongoEx) {
            $errorDoc    = new ErrorDocument();
            $errorDoc->setJsonApi(new JsonApiObject("1.0"));
            $errors      = [];

            $error = new Error();
            $error->setTitle($this->translator->translate('error-user-conflict', 'zfe-user'));

            return $jsonApi->respond()->conflict($errorDoc);
        }

        return $jsonApi->respond()->ok($this->userDocuemnt, $user);
    }
}
