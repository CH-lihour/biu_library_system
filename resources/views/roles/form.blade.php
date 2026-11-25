<form action="{{ $role ? route('roles.update', $role->id) : route('roles.store') }}" method="post">
    @csrf
    @if ($role)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-6 form-group">
            <label for="name">Role Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $role?->name) }}"
                maxlength="50" placeholder="For example: Cataloguer" />
            <span class="text-danger">{{ $errors->first('name') }}</span>
        </div>
        <div class="col-md-6 form-group">
            <label for="description">Description</label>
            <input type="text" name="description" class="form-control" id="description"
                value="{{ old('description', $role?->description) }}" maxlength="255" />
            <span class="text-danger">{{ $errors->first('description') }}</span>
        </div>
    </div>

    @php
        $selected = array_map('intval', old('permissions', session()->hasOldInput() ? [] : $role?->permissions->modelKeys() ?? []));
    @endphp

    <div class="form-group" id="role-permissions">
        <label>Permissions</label>
        <p class="text-muted mb-2">Tick what people with this role may do. You can only give access that you have
            yourself.</p>
        <span class="text-danger">{{ $errors->first('permissions') ?: $errors->first('permissions.*') }}</span>

        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
            <div class="form-check p-0 mb-0">
                <input class="form-check-input js-all ms-0 me-2" type="checkbox" id="select-all">
                <label class="form-check-label fw-bold" for="select-all">Select all permissions</label>
            </div>
            <span class="text-muted js-total" aria-live="polite"></span>
            <div class="ms-auto d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary js-expand">Expand all</button>
                <button type="button" class="btn btn-sm btn-outline-secondary js-collapse">Collapse all</button>
            </div>
        </div>

        {{-- three separate columns, so a collapsed group leaves no gap beside an open one --}}
        <div class="row">
            @for ($column = 0; $column < 3; $column++)
                <div class="col-lg-4">
                    @foreach ($groups as $group => $permissions)
                        @php $index = $loop->index; @endphp
                        @continue($index % 3 !== $column)
                        <fieldset class="permission-group border rounded mb-4 js-group">
                            <legend class="float-none w-auto fs-6 mb-0 ms-2">
                                <button type="button" class="permission-toggle" data-bs-toggle="collapse"
                                    data-bs-target="#group-{{ $index }}" aria-expanded="true"
                                    aria-controls="group-{{ $index }}">
                                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                    <span class="fw-bold">{{ $group }}</span>
                                    <span class="text-muted js-count"></span>
                                </button>
                            </legend>
                            <div class="collapse show" id="group-{{ $index }}">
                                <div class="px-3 pt-2 pb-3">
                                    <div class="form-check p-0 mb-2">
                                        <input class="form-check-input js-group-all ms-0 me-2" type="checkbox"
                                            id="all-{{ $index }}">
                                        <label class="form-check-label text-muted" for="all-{{ $index }}">Select all</label>
                                    </div>
                                    @foreach ($permissions as $permission)
                                        <div class="form-check p-0 mb-1">
                                            <input class="form-check-input js-permission ms-0 me-2" type="checkbox"
                                                name="permissions[]" value="{{ $permission->id }}"
                                                id="permission-{{ $permission->id }}" @checked(in_array($permission->id, $selected, true)) @disabled(!can_access_all($permission->routes))>
                                            <label class="form-check-label"
                                                for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </fieldset>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>

    <div class="card-action w-100">
        <button type="submit" class="btn btn-success">Submit</button>
        <a href="{{ route('roles.index') }}" class="btn btn-danger">Cancel</a>
    </div>
</form>

@push('styles')
    <style>
        .permission-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 2px 8px;
            border: 0;
            background: none;
            color: inherit;
        }

        .permission-toggle i {
            font-size: 11px;
            transition: transform 0.2s ease;
        }

        .permission-toggle[aria-expanded="false"] i {
            transform: rotate(-90deg);
        }

        .permission-group {
            min-height: 34px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function () {
            const root = $('#role-permissions');
            const all = root.find('.js-permission');

            // a "select all" box shows and controls the boxes it stands for
            const link = (box, boxes) => {
                const open = boxes.not(':disabled');
                box.prop('disabled', open.length === 0)
                    .on('change', () => { open.prop('checked', box.prop('checked')); refresh(); });

                return () => {
                    const ticked = open.filter(':checked').length;
                    box.prop({ checked: open.length > 0 && ticked === open.length, indeterminate: ticked > 0 && ticked < open.length });
                };
            };

            const updates = [link(root.find('.js-all'), all)];
            updates.push(() => root.find('.js-total').text(all.filter(':checked').length + ' of ' + all.length + ' selected'));

            root.find('.js-group').each(function () {
                const boxes = $(this).find('.js-permission');
                const count = $(this).find('.js-count');
                updates.push(link($(this).find('.js-group-all'), boxes));
                updates.push(() => count.text(boxes.filter(':checked').length + '/' + boxes.length));
            });

            const refresh = () => updates.forEach((update) => update());
            all.on('change', refresh);
            refresh();

            const setAll = (action) => root.find('.js-group .collapse').each(function () {
                bootstrap.Collapse.getOrCreateInstance(this, { toggle: false })[action]();
            });
            root.find('.js-expand').on('click', () => setAll('show'));
            root.find('.js-collapse').on('click', () => setAll('hide'));
        });
    </script>
@endpush
