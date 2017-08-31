<?php

namespace ZfeUser\Action\Role;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use ZfeUser\Service\RoleService;
use ZfeUser\Hateoas\Jsonapi\Document;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use ZfeUser\Hateoas\Jsonapi\Hydrator\RoleHydrator;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Error;
use ZfeUser\Model\Role;
use ZfeUser\Middleware\JsonApiDispatcherMiddleware;
use Zend\I18n\Translator\TranslatorInterface;

class RoleAddAction implements ServerMiddlewareInterface
{

    private $rolService;
    private $translator;
    private $roleHydrator;
    private $roleDocument;

    public function __construct( RoleService $rolService, RoleHydrator $roleHydrator, Document\RoleDocument $roleDoc, TranslatorInterface $translator
    )
    {
        $this->rolService = $rolService;
        $this->translator = $translator;
        $this->roleHydrator = $roleHydrator;
        $this->roleDocument = $roleDoc;
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
        $role = new Role(null);
       $jsonApi->hydrate($this->roleHydrator, $role);
        //$this->rolService->addRole( $role );


        try {
            $this->rolService->add($role);
        } catch (\MongoDuplicateKeyException $mongoEx) {
            $errorDoc = new ErrorDocument();
            $errorDoc->setJsonApi(new JsonApiObject("1.0"));
            $errors = [];

            $error = new Error();
            $error->setTitle($this->translator->translate('error-user-conflict', 'zfe-user'));

            return $jsonApi->respond()->conflict($errorDoc);
        }

        return $jsonApi->respond()->ok($this->roleDocument, $role);
    }

}
