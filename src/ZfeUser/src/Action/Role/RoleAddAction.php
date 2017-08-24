<?php

namespace ZfeUser\Action\Role;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use ZfeUser\Service\RoleService;
use Zend\Expressive\Template\TemplateRendererInterface;
use ZfeUser\Hateoas\Jsonapi\Document;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use ZfeUser\Hateoas\Jsonapi\Transformer;
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Request\Request;
use Zend\Diactoros\Response;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use ZfeUser\Hateoas\Jsonapi\Hydrator\RoleHydrator;
use Zend\Authentication\Result;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Error;
use ZfeUser\Middleware\JsonApiResponseMiddleware;
use Zend\Permissions\Rbac\Role;

class RoleAddAction implements ServerMiddlewareInterface
{

    private $rolService;
    private $template;
    private $roleHydrator;
    private $roleDocument;

    public function __construct(
        RoleService $rolService,
        RoleHydrator $roleHydrator,
        Document\RoleDocument $roleDoc,
        TemplateRendererInterface $template = null
    ) {
        $this->rolService    = $rolService;
        $this->template      = $template;
        $this->roleHydrator  = $roleHydrator;
        $this->roleDocument  = $roleDoc;
    }

    /**
     * @todo Verify response type and throw error
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return \App\Action\renderResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {

        $role                = new Role(null);
        $defaultExpFactory   = new DefaultExceptionFactory();
        $jsonapi             = new JsonApi(new Request($request, $defaultExpFactory), new Response(), $defaultExpFactory);

        $jsonapi->hydrate($this->roleHydrator, $role);
        //$this->rolService->addRole( $role );


        try {
            $this->rolService->add($role);
        } catch (\MongoDuplicateKeyException $mongoEx) {
            $errorDoc    = new ErrorDocument();
            $errorDoc->setJsonApi(new JsonApiObject("1.0"));
            $errors      = [];

            $error = new Error();
            $error->setTitle($this->translator->translate('error-user-conflict', 'zfe-user'));

            return $jsonapi->respond()->conflict($errorDoc);
        }

        return $jsonapi->respond()->ok($this->roleDocument, $role);
    }
}
