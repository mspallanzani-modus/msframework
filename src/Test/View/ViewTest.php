<?php

namespace Mslib\Test\View;

use Mslib\Exception\RenderException;
use Mslib\Test\MsTestCase;
use Mslib\View\View;
use Mslib\View\ViewHelper;

/**
 * Class ViewTest: all View tests
 *
 * @package Mslib\Test\View
 */
class ViewTest extends MsTestCase
{
    /**
     * We check that if a View class is created with a non-existing template,
     * then a RenderException is launched
     */
    public function testViewConstruct()
    {
        $mockedContainer = $this->getContainerWitMockedPDO();
        $view = ViewHelper::getViewForTemplate($mockedContainer, 'response.json.php');
        $this->assertInstanceOf(View::class, $view);

        $this->expectException(RenderException::class);
        ViewHelper::getViewForTemplate($mockedContainer, 'thistemplatedoesnotexist.json.php');
    }

    /**
     * Checks render() method: success and error cases
     */
    public function testRender()
    {
        $mockedContainer = $this->getContainerWitMockedPDO();
        $view = ViewHelper::getViewForTemplate($mockedContainer, 'response.json.php');
        $this->assertInstanceOf(View::class, $view);

        // Success rendering
        $data = array(
            "status" => "success",
            "code"   => "1",
            "message" => "",
            "data" => array()
        );
        $rendered = $view->render($data);
        $this->assertEquals(
            "{    \"status\"    : \"success\",    \"code\"      : \"1\",    \"message\"   : \"\",    \"data\"      : []}",
            $rendered
        );

        // Rendering exception: missing populate parameter
        $data = array(
            "code"   => "1",
            "message" => "",
            "data" => array()
        );
        $this->expectException(RenderException::class);
        $view->render($data);
    }
}
