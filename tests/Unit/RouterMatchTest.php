<?php

declare(strict_types=1);

namespace Tests\Unit;

use app\Core\Request;
use app\Core\Response;
use app\Core\Router;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Unit tests for the Router path/parameter matching (roadmap T07).
 *
 * These exercise the pure pattern-matching algorithm in isolation via
 * reflection; full request resolution is covered by integration tests (T18).
 */
final class RouterMatchTest extends TestCase
{
    private ReflectionMethod $matchPath;
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router(new Request(), new Response());
        $this->matchPath = new ReflectionMethod(Router::class, 'matchPath');
        $this->matchPath->setAccessible(true);
    }

    /**
     * @return array<string, string>|null
     */
    private function match(string $pattern, string $path): ?array
    {
        return $this->matchPath->invoke($this->router, $pattern, $path);
    }

    public function testStaticPathMatchesExactly(): void
    {
        $this->assertSame([], $this->match('/products', '/products'));
    }

    public function testStaticPathDoesNotMatchDifferentPath(): void
    {
        $this->assertNull($this->match('/products', '/categories'));
    }

    public function testTrailingSlashIsIgnored(): void
    {
        $this->assertSame([], $this->match('/products/', '/products'));
        $this->assertSame([], $this->match('/products', '/products/'));
    }

    public function testSingleParameterIsExtractedByName(): void
    {
        $this->assertSame(['id' => '42'], $this->match('/products/{id}', '/products/42'));
    }

    public function testMultipleParametersAreExtractedByNameInOrder(): void
    {
        $this->assertSame(
            ['categoryId' => '7', 'productId' => '99'],
            $this->match('/categories/{categoryId}/products/{productId}', '/categories/7/products/99')
        );
    }

    public function testParameterRouteDoesNotMatchMissingSegment(): void
    {
        $this->assertNull($this->match('/products/{id}', '/products'));
    }

    public function testParameterDoesNotMatchAcrossSlashes(): void
    {
        // `{id}` matches a single segment only, not `42/extra`.
        $this->assertNull($this->match('/products/{id}', '/products/42/extra'));
    }

    public function testStaticPrefixMustMatchBeforeParameter(): void
    {
        $this->assertNull($this->match('/products/{id}', '/orders/42'));
    }
}
