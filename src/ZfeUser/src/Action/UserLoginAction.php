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

class UserLoginAction implements ServerMiddlewareInterface {

    private $userService;
    private $template;

    public function __construct(UserService $userService, TemplateRendererInterface $template = null) {
        $this->userService = $userService;
        $this->template = $template;
    }

    /**
     * @todo Verify response type and throw error
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return \App\Action\renderResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {



        $user = new \ZfeUser\Model\User();
        $user->setEmail('gsarkar.dev@gmail.com');
        $user->setPassword('foobar');

        $this->userService->setAuthUser($user);
        $authResult = $this->userService->authenticate();
        $renderResponse = $this->userService->getOptions()->getResponseType();

        $messages = $authResult->getMessages();
        
        if ($renderResponse == \Zend\Diactoros\Response\HtmlResponse::class) {
            return new $renderResponse($this->template->render('app::home-page', ['user' => implode(', ', $messages)]));
        } else if ($renderResponse == \WoohooLabs\Yin\JsonApi\JsonApi::class) {
            $defaultExpFactory = new DefaultExceptionFactory();
            $jsonapi = new JsonApi(new Request($request, $defaultExpFactory), new Response(), $defaultExpFactory);

            return $jsonapi->respond()->ok(new Document\User(new Transformer\User())
                            , $authResult->getIdentity());
        }

        return new $renderResponse(['user' => implode(', ', $messages)]);
    }

}
