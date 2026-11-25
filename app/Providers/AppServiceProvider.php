<?php

namespace App\Providers;

use App\Repositories\AuthorRepository;
use App\Repositories\AuthRepository;
use App\Repositories\BookCopyRepository;
use App\Repositories\BookRepository;
use App\Repositories\BorrowRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Interfaces\BookCopyRepositoryInterface;
use App\Repositories\Interfaces\BookRepositoryInterface;
use App\Repositories\Interfaces\BorrowRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\MemberPlanRepositoryInterface;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Repositories\Interfaces\PortalRepositoryInterface;
use App\Repositories\Interfaces\PublisherRepositoryInterface;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\MemberPlanRepository;
use App\Repositories\MemberRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\PortalRepository;
use App\Repositories\PublisherRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\PermissionSyncService;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Database\Events\NoPendingMigrations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class
        );

        $this->app->bind(
            BookRepositoryInterface::class,
            BookRepository::class,
        );

        $this->app->bind(
            BookCopyRepositoryInterface::class,
            BookCopyRepository::class
        );

        $this->app->bind(
            AuthorRepositoryInterface::class,
            AuthorRepository::class
        );

        $this->app->bind(
            PublisherRepositoryInterface::class,
            PublisherRepository::class
        );

        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );

        $this->app->bind(
            MemberRepositoryInterface::class,
            MemberRepository::class
        );

        $this->app->bind(
            MemberPlanRepositoryInterface::class,
            MemberPlanRepository::class
        );

        $this->app->bind(
            BorrowRepositoryInterface::class,
            BorrowRepository::class
        );

        $this->app->bind(
            RoleRepositoryInterface::class,
            RoleRepository::class
        );

        $this->app->bind(
            PermissionRepositoryInterface::class,
            PermissionRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            PortalRepositoryInterface::class,
            PortalRepository::class
        );

        $this->app->bind(
            ReservationRepositoryInterface::class,
            ReservationRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Adding a named admin route and running migrate is all a new permission needs.
        Event::listen([MigrationsEnded::class, NoPendingMigrations::class], function ($event) {
            $isDryRun = $event->options['pretend'] ?? false;

            if ($event->method === 'up' && ! $isDryRun && Schema::hasTable('permissions')) {
                app(PermissionSyncService::class)->sync();
            }
        });
    }
}
