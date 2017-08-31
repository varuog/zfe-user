<?php

namespace ZfeUser\Action\Role;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use ZfeUser\Service\RoleService;
use ZfeUser\Hateoas\Jsonapi\Document;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use WoohooLabs\Yin\JsonApi\JsonApi;
use ZfeUser\Hateoas\Jsonapi\Hydrator\RoleHydrator;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Error;
use ZfeUser\Model\Role;
use Zend\I18n\Translator\TranslatorInterface;

class RoleFetchAction implements ServerMiddlewareInterface
{

    private $rolService;
    private $translator;
    private $roleHydrator;
    private $roleDocument;
    private $jsonApi;

    public function __construct(
    JsonApi $jsonApi, RoleService $rolService, RoleHydrator $roleHydrator, Document\RoleDocument $roleDoc, TranslatorInterface $translator
    )
    {
        $this->rolService = $rolService;
        $this->translator = $translator;
        $this->roleHydrator = $roleHydrator;
        $this->roleDocument = $roleDoc;
        $this->jsonApi = $jsonApi;
    }

    /**
     * @todo Verify response type and throw error
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return \App\Action\renderResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {

        $this->jsonApi->setRequest($request);
        $role = new Role(null);
        $this->jsonApi->hydrate($this->roleHydrator, $role);
        //$this->rolService->addRole( $role );


        try {
            $this->rolService->add($role);
        } catch (\MongoDuplicateKeyException $mongoEx) {
            $errorDoc = new ErrorDocument();
            $errorDoc->setJsonApi(new JsonApiObject("1.0"));
            $errors = [];

            $error = new Error();
            $error->setTitle($this->translator->translate('error-user-conflict', 'zfe-user'));

            return $this->jsonApi->respond()->conflict($errorDoc);
        }

        return $this->jsonApi->respond()->ok($this->roleDocument, $role);
    }

}
