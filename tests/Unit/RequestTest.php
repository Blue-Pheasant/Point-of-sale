<?php

declare(strict_types=1);

namespace Tests\Unit;

use app\Core\Request;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@see Request} (roadmap T08): method spoofing, route params,
 * the getParams() rename + getPrams() alias, and wantsJson().
 */
final class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        $_GET = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        unset($_SERVER['CONTENT_TYPE'], $_SERVER['HTTP_ACCEPT']);
    }

    public function testGetMethodIsLowercased(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertSame('post', (new Request())->getMethod());
    }

    public function testMethodSpoofingPromotesPostToDelete(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'DELETE';
        $this->assertSame('delete', (new Request())->getMethod());
    }

    public function testMethodSpoofingIgnoresInvalidVerb(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'CONNECT';
        $this->assertSame('post', (new Request())->getMethod());
    }

    public function testMethodSpoofingOnlyAppliesToPost(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST['_method'] = 'DELETE';
        $this->assertSame('get', (new Request())->getMethod());
    }

    public function testRouteParamsAreExposedByName(): void
    {
        $request = new Request();
        $request->setRouteParams(['id' => '42']);

        $this->assertSame('42', $request->routeParam('id'));
        $this->assertSame(['id' => '42'], $request->routeParams());
        $this->assertNull($request->routeParam('missing'));
        $this->assertSame('fallback', $request->routeParam('missing', 'fallback'));
    }

    public function testGetPramsAliasReturnsSameAsGetParams(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['name' => 'Latte', 'price' => '50000'];

        $request = new Request();
        $this->assertSame($request->getParams(), $request->getPrams());
        $this->assertSame(['name' => 'Latte', 'price' => '50000'], $request->getParams());
    }

    public function testBodyIsNotSanitized(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['bio' => '<b>O\'Brien</b> & "co"'];

        // Values are returned verbatim — escaping happens at output time (T11).
        $this->assertSame('<b>O\'Brien</b> & "co"', (new Request())->getBody()['bio']);
    }

    public function testWantsJsonFromContentType(): void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->assertTrue((new Request())->wantsJson());
    }

    public function testWantsJsonFromAcceptHeader(): void
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json, text/plain';
        $this->assertTrue((new Request())->wantsJson());
    }

    public function testDoesNotWantJsonForHtml(): void
    {
        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        $this->assertFalse((new Request())->wantsJson());
    }
}
