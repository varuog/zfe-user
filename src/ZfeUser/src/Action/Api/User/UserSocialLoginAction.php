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
use ZfeUser\Factory\Social\SocialAuthAdapterFactory;


class UserSocialLoginAction implements ServerMiddlewareInterface
{

    private $userService;
    private $translator;
    private $userDocument;
    /**
     *
     * @var ZfeUser\Adapter\Auth\Social\SocialAuthAdapterInterface 
     */
    private $authAdapter;
    private $authAdapterFactory;

    public function __construct(UserService $userService, Document\UserDocument $userDoc, TranslatorInterface $translator, SocialAuthAdapterFactory $authAdapterFactory)
    {
        $this->userService = $userService;
        $this->translator = $translator;
        $this->userDocument = $userDoc;
        $this->authAdapterFactory = $authAdapterFactory;
    }

    /**
     * @todo Verify response type and throw error
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return \App\Action\renderResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $providerName= $request->getAttribute('provider');
        $socialAccessToken= isset($request->getQueryParams()['accesstoken']) ? $request->getQueryParams()['accesstoken'] : '';
        
        $this->authAdapter= $this->authAdapterFactory->build($providerName);
        $this->authAdapter->setAccessToken($socialAccessToken);
        
        $jsonApi = $request->getAttribute(JsonApiDispatcherMiddleware::JSON_API_PROC);

        $user = new \ZfeUser\Model\User();
        $this->authAdapter->setAuthUser($user);
        $this->userService->setAuthAdapter($this->authAdapter);
        $this->userService->setServerOptions($request->getServerParams());
        $authResult = $this->userService->authenticate();


        if ($authResult->getCode() == Result::SUCCESS)
        {
            $this->userDocument->setAccessToken($authResult->getIdentity()->getLastAccessToken());
            return $jsonApi->respond()->ok($this->userDocument, $authResult->getIdentity());
        } else
        if ($authResult->getCode() == Result::FAILURE_IDENTITY_NOT_FOUND)
        {
            $newUser = $authResult->getIdentity();
            $this->userService->register($newUser);
            $this->userService->generateAuthToken($newUser);
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
