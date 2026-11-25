<form action="{{ $user ? route('users.update', $user->id) : route('users.store') }}" method="post">
    @csrf
    @if ($user)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-6 form-group">
            <label for="firstname">First Name <span class="text-danger">*</span></label>
            <input type="text" name="firstname" class="form-control" id="firstname" value="{{ old('firstname', $user?->staff?->firstname ?? $user?->name) }}" maxlength="24" />
            <span class="text-danger">{{ $errors->first('firstname') }}</span>
        </div>
        <div class="col-md-6 form-group">
            <label for="lastname">Last Name</label>
            <input type="text" name="lastname" class="form-control" id="lastname" value="{{ old('lastname', $user?->staff?->lastname) }}" maxlength="24" />
            <span class="text-danger">{{ $errors->first('lastname') }}</span>
        </div>
        <div class="col-md-6 form-group">
            <label for="email">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" id="email" value="{{ old('email', $user?->email) }}" maxlength="100" />
            <span class="text-danger">{{ $errors->first('email') }}</span>
        </div>
        <div class="col-md-6 form-group">
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone', $user?->staff?->phone) }}" maxlength="15" />
            <span class="text-danger">{{ $errors->first('phone') }}</span>
        </div>
        <div class="col-md-6 form-group">
            <label for="position">Position <span class="text-danger">*</span></label>
            <input type="text" name="position" class="form-control" id="position" value="{{ old('position', $user?->staff?->position) }}" maxlength="100" placeholder="For example: Circulation librarian" />
            <span class="text-danger">{{ $errors->first('position') }}</span>
        </div>
        <div class="col-md-3 form-group">
            <label for="role_id">Role <span class="text-danger">*</span></label>
            <select name="role_id" id="role_id" class="form-select">
                <option value="">Select role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected(old('role_id', $user?->role_id) == $role->id)>{{ $role->name }}</option>
                @endforeach
            </select>
            <span class="text-danger">{{ $errors->first('role_id') }}</span>
        </div>
        <div class="col-md-3 form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select">
                @foreach (['active' => 'Active', 'on_leave' => 'On leave', 'terminated' => 'Terminated (cannot sign in)'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $user?->staff?->status ?? 'active') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <span class="text-danger">{{ $errors->first('status') }}</span>
        </div>
        <div class="col-md-6 form-group">
            <label for="password">{{ $user ? 'New Password (leave empty to keep)' : 'Password' }}</label>
            <input type="password" name="password" class="form-control" id="password" autocomplete="new-password" minlength="8" />
            <span class="text-danger">{{ $errors->first('password') }}</span>
        </div>
        <div class="col-md-6 form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" autocomplete="new-password" />
            <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
        </div>
    </div>
    <div class="card-action w-100">
        <button type="submit" class="btn btn-success">Submit</button>
        <a href="{{ route('users.index') }}" class="btn btn-danger">Cancel</a>
    </div>
</form>
