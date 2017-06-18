<?php

namespace ZfeUser\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use ZfeUser\Service\UserService;
use Zend\Expressive\Template\TemplateRendererInterface;
use ZfeUser\Hateoas\Jsonapi\Document;
use ZfeUser\Hateoas\Jsonapi\Transformer;
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Request\Request;
use Zend\Diactoros\Response;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use ZfeUser\Hateoas\Jsonapi\Hydrator\UserHydrator;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use Zend\Authentication\Result;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Error;

class UserLoginAction implements ServerMiddlewareInterface {

    private $userService;
    private $template;
    private $userHydrator;

    public function __construct(UserService $userService, UserHydrator $userHydrator, TemplateRendererInterface $template = null) {
        $this->userService = $userService;
        $this->template = $template;
        $this->userHydrator = $userHydrator;
    }

    /**
     * @todo Verify response type and throw error
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return \App\Action\renderResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {

        $user = new \ZfeUser\Model\User();
        $defaultExpFactory = new DefaultExceptionFactory();
        $jsonapi = new JsonApi(new Request($request, $defaultExpFactory), new Response(), $defaultExpFactory);

        $jsonapi->hydrate($this->userHydrator, $user);

        $this->userService->setAuthUser($user);
        $authResult = $this->userService->authenticate();
        $renderResponse = $this->userService->getOptions()->getResponseType();

        $messages = $authResult->getMessages();

        if ($authResult->getIdentity() != null) {

            return $jsonapi->respond()->ok(new Document\User(new Transformer\User())
                            , $authResult->getIdentity());
        } else {

            $errorDoc = new ErrorDocument();
            $errorDoc->setJsonApi(new JsonApiObject("1.0"));
            $errors = [];
            foreach ($authResult->getMessages() as $errorMessage) {
                $error = new Error();
                $error->setTitle($errorMessage);
                $errors[] = $error;
            }

            if ($authResult->getCode() == Result::FAILURE_IDENTITY_NOT_FOUND) {
                return $jsonapi->respond()->notFound($errorDoc, $errors);
            } else {
                //$erroDoc->setLinks(Links::createWithoutBaseUri()->setSelf("http://example.com/api/errors/404")));
                return $jsonapi->respond()->genericError($errorDoc, $errors, 500);
            }
        }
    }

}
