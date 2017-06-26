<?php

namespace Mslib\View;

use Mslib\Exception\RenderException;

/**
 * Class View
 *
 * @package Mslib\View
 */
class View
{
    /**
     * @var string
     */
    protected $template;

    /**
     * View constructor.
     *
     * @param string $template The template relative path (all templates should be stored in the Resources/views folder).
     *
     * @throws RenderException
     */
    public function __construct($template)
    {
        $file = 'src/Resources/views/' . $template;
        if (file_exists($file)) {
            $this->template = $file;
        } else {
            throw new RenderException("Template '$file' not found!");
        }
    }

    /**
     * Renders and returns the template content.
     *
     * @param array $data Data made available to the view.
     *
     * @return string The rendered template.
     *
     * @throws RenderException
     */
    public function render(array $data)
    {
        try {
            extract($data);
            ob_start();
            include($this->template);
            $content = ob_get_contents();
            ob_end_clean();
//TODO this is just a workaround to remove all \n from the rendered template: there should be a better way of doing this
            $content = str_replace(array("\r", "\n"), '', $content);
            $content = stripslashes($content);
            return $content;
        } catch (\Exception $exception) {
            throw new RenderException(
                "Impossible to render the template '$this->template'. Error message is:  $exception->getMessage()"
            );
        }
    }
}