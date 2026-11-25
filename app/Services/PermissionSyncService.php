<?php

namespace App\Services;

use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Keeps the permission list in step with the admin routes, so adding a named
 * route is enough. A new permission is given to no role: someone has to tick it.
 */
class PermissionSyncService
{
    private const VERBS = [
        'index' => 'View', 'show' => 'View',
        'create' => 'Add', 'store' => 'Add',
        'edit' => 'Edit', 'update' => 'Edit',
        'destroy' => 'Delete',
    ];

    public function __construct(protected PermissionRepositoryInterface $permissionRepository) {}

    /**
     * @return array{added: array<int, string>, removed: array<int, string>}
     */
    public function sync(): array
    {
        $live = $this->adminRoutes();

        // Never delete everything because the routes did not load.
        if ($live === []) {
            return ['added' => [], 'removed' => []];
        }

        $result = DB::transaction(fn () => [
            'removed' => $this->removeOldRoutes($live),
            'added' => $this->addNewRoutes($live),
        ]);

        if ($result['removed']) {
            Log::warning('Permissions removed because their routes no longer exist.', $result['removed']);
        }

        return $result;
    }

    /**
     * Every named route behind the admin guard.
     *
     * @return array<int, string>
     */
    private function adminRoutes(): array
    {
        return collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => in_array('admin.access', $route->gatherMiddleware(), true))
            ->map(fn ($route) => $route->getName())
            ->filter()
            ->reject(fn ($name) => $name === 'dashboard')
            ->unique()->values()->all();
    }

    private function removeOldRoutes(array $live): array
    {
        $removed = [];

        foreach ($this->permissionRepository->all() as $permission) {
            $kept = array_values(array_intersect($permission->routes, $live));

            if ($kept === []) {
                $removed[] = $permission->name;
                $this->permissionRepository->delete($permission->id);
            } elseif ($kept !== $permission->routes) {
                $this->permissionRepository->update($permission->id, ['routes' => $kept]);
            }
        }

        return $removed;
    }

    private function addNewRoutes(array $live): array
    {
        $existing = $this->permissionRepository->all();
        $missing = array_diff($live, $existing->pluck('routes')->flatten()->all());
        $new = [];

        foreach ($missing as $route) {
            [$module, $action] = explode('.', $route, 2) + [1 => 'open'];
            $group = $this->groupFor($module, $existing);
            $verb = self::VERBS[$action] ?? $this->words($action);
            $name = $verb.' '.Str::lower($group);

            // Adding the route to a permission that roles already have would
            // give them new access without anyone ticking it.
            if ($existing->contains('name', $name)) {
                $name .= ' ('.Str::lower($this->words($action)).')';
            }

            $new[$name]['group'] = $group;
            $new[$name]['routes'][] = $route;
        }

        foreach ($new as $name => $data) {
            $this->permissionRepository->create(['name' => $name] + $data);
        }

        return array_keys($new);
    }

    /** The box this module already has, or a new one made from its name. */
    private function groupFor(string $module, $existing): string
    {
        $match = $existing->first(
            fn ($permission) => collect($permission->routes)->contains(fn ($route) => Str::startsWith($route, $module.'.'))
        );

        return $match->group ?? $this->words($module);
    }

    /** "export-pdf" or "exportPdf" becomes "Export pdf". */
    private function words(string $text): string
    {
        return Str::ucfirst(Str::lower(Str::headline(str_replace('.', ' ', $text))));
    }
}
