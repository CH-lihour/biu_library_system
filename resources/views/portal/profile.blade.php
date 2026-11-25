@extends('layouts.portal')

@section('title', 'Profile')
@section('content')
    <div class="portal-head">
        <div>
            <h1>Profile</h1>
            <p>Member {{ $member->member_code }} {!! status_label($member->status) !!}</p>
        </div>
    </div>

    <form action="{{ route('portal.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <section class="portal-card">
            <h2>Your details</h2>
            <div class="portal-form">
                <div>
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" maxlength="50" required value="{{ old('name', $member->name) }}">
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                    <span class="portal-muted">Ask the library desk to change your email.</span>
                </div>
                <div>
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" class="form-control" maxlength="15" value="{{ old('phone', $member->phone) }}">
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                </div>
                <div>
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control" maxlength="255" value="{{ old('address', $member->address) }}">
                    <span class="text-danger">{{ $errors->first('address') }}</span>
                </div>
            </div>
        </section>

        <section class="portal-card">
            <h2>Change password</h2>
            <div class="portal-form">
                <div>
                    <label for="current_password">Current password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" autocomplete="current-password">
                    <span class="text-danger">{{ $errors->first('current_password') }}</span>
                </div>
                <div></div>
                <div>
                    <label for="password">New password</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="8" autocomplete="new-password">
                    <span class="text-danger">{{ $errors->first('password') }}</span>
                </div>
                <div>
                    <label for="password_confirmation">Confirm new password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                </div>
            </div>
        </section>

        <button type="submit" class="btn btn-primary">Save changes</button>
    </form>
@endsection
