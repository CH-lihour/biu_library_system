@extends('layouts.app')

@section('page-title', 'Review Registration')
@section('content')
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Submitted by the member</h4>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-4">Code</dt>
                        <dd class="col-8">{{ $member->member_code }}</dd>
                        <dt class="col-4">Name</dt>
                        <dd class="col-8">{{ $member->name }}</dd>
                        <dt class="col-4">Email</dt>
                        <dd class="col-8">{{ $member->email }}</dd>
                        <dt class="col-4">Phone</dt>
                        <dd class="col-8">{{ $member->phone ?? '-' }}</dd>
                        <dt class="col-4">Address</dt>
                        <dd class="col-8">{{ $member->address ?? '-' }}</dd>
                        <dt class="col-4">Registered</dt>
                        <dd class="col-8">{{ format_date_time($member->created_at, 'd-M-Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
            <div class="alert alert-warning">
                Check the person's ID before approving. The email address was typed by the member and is not verified.
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Decision</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('members.approve', $member->id) }}" method="post">
                        @csrf
                        @if ($matches->isNotEmpty())
                            <div class="form-group">
                                <label for="link_member_id">Existing card with the same email</label>
                                <select name="link_member_id" id="link_member_id" class="form-select">
                                    <option value="">Do not link, make this a new membership</option>
                                    @foreach ($matches as $match)
                                        <option value="{{ $match->id }}" @selected(old('link_member_id') == $match->id)>
                                            {{ $match->member_code }} - {{ $match->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Linking gives this login the loans and history of that card. The card keeps its own plan and dates.</small>
                                <span class="text-danger d-block">{{ $errors->first('link_member_id') }}</span>
                            </div>
                        @endif
                        <div class="row js-new-membership">
                            <div class="col-md-6 form-group">
                                <label for="plan_id">Plan <span class="text-danger">*</span></label>
                                <select name="plan_id" id="plan_id" class="form-select">
                                    <option value="">Select plan</option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>
                                            {{ $plan->name }} ({{ $plan->loan_duration_days }} days)
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('plan_id') }}</span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="expiry_date">Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" name="expiry_date" id="expiry_date" class="form-control"
                                    value="{{ old('expiry_date', today()->addYear()->format('Y-m-d')) }}">
                                <span class="text-danger">{{ $errors->first('expiry_date') }}</span>
                            </div>
                        </div>
                        <div class="card-action w-100 d-flex gap-2">
                            <button type="submit" class="btn btn-success">Approve</button>
                            @if (can_access('members.reject'))
                                <button type="submit" class="btn btn-danger" formnovalidate
                                    formaction="{{ route('members.reject', $member->id) }}"
                                    onclick="return confirm('Reject this registration?')">Reject</button>
                            @endif
                            <a href="{{ route('members.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#link_member_id').on('change', function () {
            $('.js-new-membership').toggle(! this.value);
        }).trigger('change');
    </script>
@endpush
