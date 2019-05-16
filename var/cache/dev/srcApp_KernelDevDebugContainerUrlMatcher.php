<?php

use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherTrait;
use Symfony\Component\Routing\RequestContext;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class srcApp_KernelDevDebugContainerUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    use PhpMatcherTrait;

    public function __construct(RequestContext $context)
    {
        $this->context = $context;
        $this->staticRoutes = [
            '/user' => [[['_route' => 'user_index', '_controller' => 'App\\Controller\\UserController::getUsersAction'], null, ['GET' => 0], null, true, false, null]],
            '/user/new' => [[['_route' => 'user_new', '_controller' => 'App\\Controller\\UserController::addUserAction'], null, ['POST' => 0], null, false, false, null]],
        ];
        $this->regexpList = [
            0 => '{^(?'
                    .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                    .'|/user/([^/]++)(?'
                        .'|(*:59)'
                        .'|/edit(*:71)'
                        .'|(*:78)'
                    .')'
                .')/?$}sDu',
        ];
        $this->dynamicRoutes = [
            35 => [[['_route' => '_twig_error_test', '_controller' => 'twig.controller.preview_error::previewErrorPageAction', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
            59 => [[['_route' => 'user_show', '_controller' => 'App\\Controller\\UserController::getUserAction'], ['id'], ['GET' => 0], null, false, true, null]],
            71 => [[['_route' => 'user_edit', '_controller' => 'App\\Controller\\UserController::editUserAction'], ['id'], ['PUT' => 0], null, false, false, null]],
            78 => [[['_route' => 'user_delete', '_controller' => 'App\\Controller\\UserController::deleteUserAction'], ['id'], ['DELETE' => 0], null, false, true, null]],
        ];
    }
}
