<?php

namespace app\Core\Container;

use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * A lightweight PSR-11 dependency-injection container with constructor
 * autowiring via reflection.
 *
 * Resolution rules (see {@see build()} / {@see resolveDependencies()}):
 *  - A Closure concrete is invoked with `($container, $parameters)`.
 *  - A class-string concrete is reflected and each constructor parameter is
 *    resolved by, in order: a same-named entry in `$parameters` (override) →
 *    its type-hinted class (resolved recursively) → its default value → null
 *    when the parameter is nullable. A required scalar that matches none of
 *    these raises a {@see ContainerException}.
 *  - Circular dependencies are detected and reported with the resolution chain.
 *
 * Bindings registered as shared (or via {@see instance()}) are cached and
 * returned on later lookups; an unbound but existing class is autowired
 * transiently (a fresh instance per resolution).
 *
 * @package app\Core\Container
 */
class Container implements ContainerInterface
{
    /** @var array<string, array{concrete: Closure|string, shared: bool}> */
    private array $bindings = [];

    /** @var array<string, mixed> */
    private array $instances = [];

    /** @var array<string, string> alias id => target id */
    private array $aliases = [];

    /** @var array<string, true> Concretes currently being built (circular-dependency guard). */
    private array $building = [];

    /**
     * Register a binding. A null concrete binds the id to itself.
     */
    public function bind(string $id, Closure|string|null $concrete = null, bool $shared = false): void
    {
        unset($this->instances[$id], $this->aliases[$id]);

        $this->bindings[$id] = [
            'concrete' => $concrete ?? $id,
            'shared'   => $shared,
        ];
    }

    /**
     * Register a shared (single-instance) binding.
     */
    public function singleton(string $id, Closure|string|null $concrete = null): void
    {
        $this->bind($id, $concrete, true);
    }

    /**
     * Register an already-built instance as a shared entry.
     *
     * Returns the same instance so it can be assigned in one expression, e.g.
     * `$this->db = $container->instance(Database::class, new Database($c));`.
     *
     * @template T
     * @param T $instance
     * @return T
     */
    public function instance(string $id, mixed $instance): mixed
    {
        unset($this->bindings[$id], $this->aliases[$id]);

        $this->instances[$id] = $instance;

        return $instance;
    }

    /**
     * Alias an id to another id (resolved transparently by get()/make()).
     */
    public function alias(string $alias, string $id): void
    {
        $this->aliases[$alias] = $id;
    }

    public function has(string $id): bool
    {
        $id = $this->aliases[$id] ?? $id;

        return isset($this->instances[$id])
            || isset($this->bindings[$id])
            || class_exists($id);
    }

    /**
     * {@inheritDoc}
     *
     * @template TGet of object
     * @param class-string<TGet>|string $id
     * @return ($id is class-string<TGet> ? TGet : mixed)
     *
     * @throws EntryNotFoundException When the id maps to no entry or class.
     * @throws ContainerException     When the entry cannot be built.
     */
    public function get(string $id): mixed
    {
        return $this->resolve($id, []);
    }

    /**
     * Resolve an entry, overriding constructor parameters by name.
     *
     * @template TMake of object
     * @param class-string<TMake>|string $id
     * @param array<string, mixed>       $parameters
     * @return ($id is class-string<TMake> ? TMake : mixed)
     *
     * @throws EntryNotFoundException When the id maps to no entry or class.
     * @throws ContainerException     When the entry cannot be built.
     */
    public function make(string $id, array $parameters = []): mixed
    {
        return $this->resolve($id, $parameters);
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @throws EntryNotFoundException
     * @throws ContainerException
     */
    private function resolve(string $id, array $parameters): mixed
    {
        $id = $this->aliases[$id] ?? $id;

        // A pre-built instance always wins and ignores parameter overrides.
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        $binding  = $this->bindings[$id] ?? null;
        $concrete = $binding['concrete'] ?? $id;

        if (!$concrete instanceof Closure && !class_exists($concrete)) {
            throw new EntryNotFoundException("No entry or class found for \"$id\".");
        }

        $object = $this->build($concrete, $parameters);

        if ($binding !== null && $binding['shared']) {
            $this->instances[$id] = $object;
        }

        return $object;
    }

    /**
     * Instantiate a concrete (closure or class-string), autowiring its
     * constructor dependencies.
     *
     * @param array<string, mixed> $parameters
     *
     * @throws ContainerException
     */
    private function build(Closure|string $concrete, array $parameters): mixed
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        if (isset($this->building[$concrete])) {
            $chain = implode(' -> ', [...array_keys($this->building), $concrete]);
            throw new ContainerException("Circular dependency detected while resolving: $chain");
        }

        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new ContainerException("Unable to reflect class \"$concrete\": {$e->getMessage()}", 0, $e);
        }

        if (!$reflector->isInstantiable()) {
            throw new ContainerException("Class \"$concrete\" is not instantiable.");
        }

        $constructor = $reflector->getConstructor();
        if ($constructor === null) {
            return new $concrete();
        }

        $this->building[$concrete] = true;
        try {
            $arguments = $this->resolveDependencies($constructor->getParameters(), $parameters, $concrete);
        } finally {
            unset($this->building[$concrete]);
        }

        return $reflector->newInstanceArgs($arguments);
    }

    /**
     * Resolve the ordered constructor arguments for a class.
     *
     * @param array<int, ReflectionParameter> $params
     * @param array<string, mixed>            $overrides
     * @return array<int, mixed>
     *
     * @throws ContainerException
     */
    private function resolveDependencies(array $params, array $overrides, string $concrete): array
    {
        $arguments = [];

        foreach ($params as $param) {
            $name = $param->getName();

            if (array_key_exists($name, $overrides)) {
                $arguments[] = $overrides[$name];
                continue;
            }

            $type = $param->getType();

            // A class/interface type is resolved recursively. When it cannot be
            // resolved (e.g. an unbound interface) the parameter is treated as
            // optional if it declares a default or is nullable.
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                try {
                    $arguments[] = $this->resolve($type->getName(), []);
                    continue;
                } catch (ContainerExceptionInterface $e) {
                    if ($param->isDefaultValueAvailable()) {
                        $arguments[] = $param->getDefaultValue();
                        continue;
                    }
                    if ($type->allowsNull()) {
                        $arguments[] = null;
                        continue;
                    }
                    throw $e;
                }
            }

            if ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
                continue;
            }

            if ($type !== null && $type->allowsNull()) {
                $arguments[] = null;
                continue;
            }

            throw new ContainerException(
                "Unable to resolve parameter \"\${$name}\" of \"{$concrete}::__construct()\"."
            );
        }

        return $arguments;
    }
}
