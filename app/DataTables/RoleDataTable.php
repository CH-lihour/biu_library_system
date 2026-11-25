<?php

namespace App\DataTables;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RoleDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<Role>  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('description', fn ($role) => $role->description ?? '-')
            ->addColumn('access', fn ($role) => $role->is_super ? status_badge('Full access', true) : $role->permissions_count.' '.str('permission')->plural($role->permissions_count))
            ->addColumn('action', function ($role) {
                if ($role->is_super) {
                    return '<span class="text-muted">Built in</span>';
                }

                return edit_button(route('roles.edit', $role->id), 'roles.edit').' '.
                    delete_button(route('roles.destroy', $role->id), 'roles.destroy');
            })
            ->rawColumns(['access', 'action']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Role>
     */
    public function query(Role $model): QueryBuilder
    {
        return $model->newQuery()->withCount(['permissions', 'users']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('role-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('No')
                ->exportable(false)
                ->printable(false)
                ->width(40)
                ->addClass('text-center'),
            Column::make('name')->title('Role'),
            Column::make('description')->title('Description'),
            Column::computed('access')->title('Access'),
            Column::make('users_count')->title('Staff')->searchable(false),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Role_'.date('YmdHis');
    }
}
