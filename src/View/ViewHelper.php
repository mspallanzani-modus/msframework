<?php

namespace Mslib\View;
use Mslib\Container\Container;
use Mslib\Exception\ConfigException;
use Mslib\Exception\RenderException;

/**
 * Class ViewHelper: View helper methods
 *
 * @package Mslib\View
 */
class ViewHelper
{
    /**
     * Returns a View object for the given template path (relative or absolute)
     *
     * @param Container $container The container instance (services, config keys, etc)
     * @param string $template The relative or absolute template path
     * @param bool $relativePath If true, the default template path will be used (configuration key 'template-folder')
     *
     * @return View|null
     *
     * @throws RenderException
     */
    public static function getViewForTemplate(Container $container, $template, $relativePath = true)
    {
        $view = null;
        if ($relativePath) {
            try {
                $basePath = rtrim($container->getConfigValue('template-folder'), "/");
                $view = new View($basePath . "/" . trim($template, "/"));
            } catch (ConfigException $configException) {
                throw new RenderException(
                    "You tried to render a template with a relative path '$template' " .
                    "but there is no base template path configured in the configuratin file " .
                    "(key missing 'template-folder')"
                );
            }
        } else {
            $view = new View($template);
        }

        return $view;
    }
}