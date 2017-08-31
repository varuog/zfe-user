<?php

namespace ZfeUser\Action\Api\User;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use ZfeUser\Service\UserService;
use ZfeUser\Hateoas\Jsonapi\Document;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use ZfeUser\Hateoas\Jsonapi\Hydrator\UserHydrator;
use Zend\Authentication\Result;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Error;
use Zend\I18n\Translator\TranslatorInterface;
use ZfeUser\Middleware\JsonApiDispatcherMiddleware;

class UserLoginAction implements ServerMiddlewareInterface
{

    private $userService;
    private $translator;
    private $userHydrator;
    private $userDocument;

    public function __construct(UserService $userService, UserHydrator $userHydrator, Document\UserDocument $userDoc, TranslatorInterface $translator)
    {
        $this->userService = $userService;
        $this->translator = $translator;
        $this->userHydrator = $userHydrator;
        $this->userDocument = $userDoc;
    }

    /**
     * @todo Verify response type and throw error
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return \App\Action\renderResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {

         $jsonApi= $request->getAttribute(JsonApiDispatcherMiddleware::JSON_API_PROC);
        $user = new \ZfeUser\Model\User();

        $jsonApi->hydrate($this->userHydrator, $user);

        $this->userService->setAuthUser($user);
        $this->userService->setServerOptions($request->getServerParams());
        $authResult = $this->userService->authenticate();


        if ($authResult->getIdentity() != null)
        {
            $this->userDocument->setAccessToken($authResult->getIdentity()->getLastAccessToken());
            return $jsonApi->respond()->ok($this->userDocument, $authResult->getIdentity());
        } else
        {
            $errorDoc = new ErrorDocument();
            $errorDoc->setJsonApi(new JsonApiObject("1.0"));
            $errors = [];
            /*
             * Get all messages from auth results
             */
            foreach ($authResult->getMessages() as $errorMessage) {
                $error = new Error();
                $error->setTitle($errorMessage);
                $errors[] = $error;
            }

            if ($authResult->getCode() == Result::FAILURE_IDENTITY_NOT_FOUND)
            {
                return $jsonApi->respond()->notFound($errorDoc, $errors);
            } else
            {
                return $jsonApi->respond()->genericError($errorDoc, $errors, 500);
            }
        }
    }

}
