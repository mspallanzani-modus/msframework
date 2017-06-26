<?php

namespace Mslib\Controller;

use Mslib\Exception\RenderException;
use Mslib\Model\EntityInterface;
use Mslib\Repository\MsRepository;
use Mslib\View\View;
use Psr\Log\LoggerInterface;
use Zend\Http\Response;

/**
 * Class MsController: General Controller class.
 *
 * @package Mslib\Controller
 */
abstract class MsController
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var MsRepository
     */
    protected $repository;

    /**
     * MsController constructor.
     *
     * @param LoggerInterface $logger The logger instance
     * @param MsRepository $repository The framework repository instance associated to this controller
     */
    public function __construct(LoggerInterface $logger, MsRepository $repository)
    {
        // Setting controller services
        $this->logger       = $logger;
        $this->repository   = $repository;
    }

    /**
     * Renders a template view for the given EntityInterface instance
     *
     * @param EntityInterface $entity The EntityInterface instance to render
     * @param null $template Template name with relative path
     *
     * @return string
     *
     * @throws RenderException
     */
    protected function renderEntityView(EntityInterface $entity, $template = null)
    {
        $view = new View($template);
        return $view->render(array("entity" => $entity));
    }

    /**
     * Returns am error Response object for given error message and status
     *
     * @param string $status The HTTP status code
     * @param string $message The error message
     *
     * @return Response
     */
    protected function returnErrorResponse($status, $message)
    {
        // Setting the headers and the status
        $response = new Response();
        $response->getHeaders()->addHeaderLine('Content-Type: application/json');
        $response->getHeaders()->addHeaderLine('Status: ' . $status);
        $response->setStatusCode($status);

        // Setting the response content from the general response view
        $view = new View("response.json.php");
        $content = $view->render(array(
            "status"    => "error",
            "code"      => "-1",
            "message"   => $message,
            "data"      => array()
        ));
        $response->setContent($content);

        // Returning the response
        return $response;
    }

    /**
     * Return a success Response object for the given rendered view, status and message.
     *
     * @param string $rendered Rendered template to create a response content
     * @param string $status The HTTP status code
     * @param string $message An additional success message
     *
     * @return Response
     */
    protected function returnSuccessResponse($rendered, $status = "200", $message = "")
    {
        // Setting the headers and the status
        $response = new Response();
        $response->getHeaders()->addHeaderLine('Content-Type: application/json');
        $response->getHeaders()->addHeaderLine('Status: ' . $status);
        $response->setStatusCode($status);

        // Setting the response content from the general response view
        $view = new View("response.json.php");

        // if rendered data is actually a json string, we decode it again so that it could be rendered not as a string but as JSON
        if (is_string($rendered)) {
            $renderedJson = json_decode($rendered);
            if ($renderedJson === false) {
                $data = $rendered;
            } else {
                $data = $renderedJson;
            }
        } else {
            $data = $rendered;
        }


        // we set the content now
        $content = $view->render(array(
            "status"    => "success",
            "code"      => "1",
            "message"   => $message,
            "data"      => $data
        ));
        $response->setContent($content);

        // Returning the response
        return $response;
    }

}