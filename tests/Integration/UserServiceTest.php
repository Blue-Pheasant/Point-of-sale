<?php

declare(strict_types=1);

namespace Tests\Integration;

use app\Models\User;
use app\Services\UserService;

/**
 * Integration tests for {@see UserService} against a real (SQLite) database
 * (roadmap T18): lookups, create/update/soft-delete and counting.
 */
final class UserServiceTest extends IntegrationTestCase
{
    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

    public function testGetUserByIdReturnsModel(): void
    {
        $this->seedUser('user-1', 'a@example.com');

        $user = $this->service->getUserById('user-1');

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('a@example.com', $user->email);
    }

    public function testGetUserByIdReturnsNullWhenMissing(): void
    {
        $this->assertNull($this->service->getUserById('nope'));
    }

    public function testGetUserByEmailReturnsModel(): void
    {
        $this->seedUser('user-1', 'find@example.com');

        $user = $this->service->getUserByEmail('find@example.com');

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('user-1', $user->id);
    }

    public function testCreateUserPersistsRow(): void
    {
        $ok = $this->service->createUser([
            'id'           => 'new-user',
            'email'        => 'new@example.com',
            'firstname'    => 'New',
            'lastname'     => 'User',
            'phone_number' => '0900000000',
            'password'     => 'hashed',
            'role'         => 'user',
        ]);

        $this->assertTrue($ok);
        $this->assertSame('new@example.com', $this->service->getUserById('new-user')->email);
    }

    public function testUpdateUserChangesFields(): void
    {
        $this->seedUser('user-1', 'old@example.com');

        $this->assertTrue($this->service->updateUser('user-1', ['email' => 'updated@example.com']));
        $this->assertSame('updated@example.com', $this->service->getUserById('user-1')->email);
    }

    public function testSoftDeleteHidesUserFromLookups(): void
    {
        $this->seedUser('user-1', 'gone@example.com');

        $this->assertTrue($this->service->softDeleteUser('user-1'));
        // Soft-deleted users disappear from getUserById (deleted_at IS NULL filter)...
        $this->assertNull($this->service->getUserById('user-1'));
        // ...but the row is still physically present.
        $this->assertSame(1, (int) self::$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn());
    }

    public function testGetTotalUserNumberExcludesSoftDeleted(): void
    {
        $this->seedUser('user-1', 'a@example.com');
        $this->seedUser('user-2', 'b@example.com');
        $this->service->softDeleteUser('user-2');

        $this->assertSame(1, $this->service->getTotalUserNumber());
    }
}
