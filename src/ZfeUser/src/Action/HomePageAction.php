<?php

namespace ZfeUser\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Expressive\Plates\PlatesRenderer;
use Zend\Expressive\Twig\TwigRenderer;
use Zend\Expressive\ZendView\ZendViewRenderer;
use ZfeUser\Adapter\Auth\Social\FacebookAuthAdapter;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Helper\ServerUrlHelper;

class HomePageAction implements ServerMiddlewareInterface
{

    private $router;
    private $template;
    private $fbAuthAdapter;
    private $urlHelper;
    private $serverHelper;

    public function __construct(Router\RouterInterface $router, FacebookAuthAdapter $fbAuthAdapter, UrlHelper $urlHelper, ServerUrlHelper $serverHelper, Template\TemplateRendererInterface $template = null)
    {
        $this->router = $router;
        $this->template = $template;
        $this->fbAuthAdapter = $fbAuthAdapter;

        $this->urlHelper = $urlHelper;
        $this->serverHelper = $serverHelper;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if (!$this->template)
        {
            return new JsonResponse([
                'welcome' => 'Congratulations! You have installed the zend-expressive skeleton application.',
                'docsUrl' => 'https://docs.zendframework.com/zend-expressive/',
            ]);
        }

        $data = [];

        if ($this->router instanceof Router\AuraRouter)
        {
            $data['routerName'] = 'Aura.Router';
            $data['routerDocs'] = 'http://auraphp.com/packages/2.x/Router.html';
        } elseif ($this->router instanceof Router\FastRouteRouter)
        {
            $data['routerName'] = 'FastRoute';
            $data['routerDocs'] = 'https://github.com/nikic/FastRoute';
        } elseif ($this->router instanceof Router\ZendRouter)
        {
            $data['routerName'] = 'Zend Router';
            $data['routerDocs'] = 'https://docs.zendframework.com/zend-router/';
        }

        if ($this->template instanceof PlatesRenderer)
        {
            $data['templateName'] = 'Plates';
            $data['templateDocs'] = 'http://platesphp.com/';
        } elseif ($this->template instanceof TwigRenderer)
        {
            $data['templateName'] = 'Twig';
            $data['templateDocs'] = 'http://twig.sensiolabs.org/documentation';
        } elseif ($this->template instanceof ZendViewRenderer)
        {
            $data['templateName'] = 'Zend View';
            $data['templateDocs'] = 'https://docs.zendframework.com/zend-view/';
        }

        $fbHelper = $this->fbAuthAdapter->getHandler()->getRedirectLoginHelper();
        $data['fblink'] = $fbHelper->getLoginUrl($this->serverHelper->generate($this->urlHelper->generate('user-social-login')), ['email','name']);
        return new HtmlResponse($this->template->render('app::home-page', $data));
    }

}
