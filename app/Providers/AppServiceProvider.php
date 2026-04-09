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
use App\Repositories\Interfaces\PublisherRepositoryInterface;
use App\Repositories\MemberPlanRepository;
use App\Repositories\MemberRepository;
use App\Repositories\PublisherRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
