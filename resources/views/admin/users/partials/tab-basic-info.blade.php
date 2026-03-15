{{-- Tab 1: Basic User Information --}}
<div class="row g-3">

    {{-- Users table fields (read-only) --}}
    <div class="col-12">
        <h6 class="fw-semibold text-muted mb-3">
            <i class="bi bi-person-fill me-1"></i>Account Information
        </h6>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Meem Code</label>
        <input type="text" class="form-control form-control-sm bg-light"
               value="{{ $user->meem_code ?? '' }}" disabled>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Meem ID</label>
        <input type="text" class="form-control form-control-sm bg-light"
               value="{{ $user->meem_id ?? '' }}" disabled>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Full Name</label>
        <input type="text" class="form-control form-control-sm bg-light"
               value="{{ $user->fullname }}" disabled>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Phone Number</label>
        <input type="text" class="form-control form-control-sm bg-light"
               value="{{ $user->phone_number }}" disabled>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" class="form-control form-control-sm bg-light"
               value="{{ $user->email ?? '' }}" disabled>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Password</label>
        <input type="text" class="form-control form-control-sm bg-light"
               value="••••••••" disabled>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Profile Picture</label>
        @if ($user->profile_picture)
            @php
                $picUrl = str_starts_with($user->profile_picture, 'http')
                    ? $user->profile_picture
                    : asset('storage/' . $user->profile_picture);
            @endphp
            <div>
                <img src="{{ $picUrl }}" alt="Profile"
                     class="rounded border"
                     style="height:60px;width:60px;object-fit:cover;">
            </div>
        @else
            <input type="text" class="form-control form-control-sm bg-light" value="No photo" disabled>
        @endif
    </div>

    {{-- Divider --}}
    <div class="col-12 mt-2">
        <hr>
        <h6 class="fw-semibold text-muted mb-3">
            <i class="bi bi-geo-alt-fill me-1"></i>Address Information (from latest profile record)
        </h6>
    </div>

    @php
        $address1  = $logData['address_line_1'] ?? ($logData['customer']['address_line_1'] ?? null);
        $address2  = $logData['address_line_2'] ?? ($logData['customer']['address_line_2'] ?? null);
        $city      = $logData['city']           ?? ($logData['customer']['city']           ?? null);
        $postcode  = $logData['postcode']        ?? ($logData['customer']['postcode']        ?? null);
        $stateId   = $logData['state_id']        ?? ($logData['customer']['state_id']        ?? null);
        $hasAddress = $address1 || $address2 || $city || $postcode || $stateId;
    @endphp

    @if ($hasAddress)
        <div class="col-md-6">
            <label class="form-label fw-semibold">Address Line 1</label>
            <input type="text" class="form-control form-control-sm bg-light"
                   value="{{ $address1 ?? '' }}" disabled>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Address Line 2</label>
            <input type="text" class="form-control form-control-sm bg-light"
                   value="{{ $address2 ?? '' }}" disabled>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">City</label>
            <input type="text" class="form-control form-control-sm bg-light"
                   value="{{ $city ?? '' }}" disabled>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Postcode</label>
            <input type="text" class="form-control form-control-sm bg-light"
                   value="{{ $postcode ?? '' }}" disabled>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">State ID</label>
            <input type="text" class="form-control form-control-sm bg-light"
                   value="{{ $stateId ?? '' }}" disabled>
        </div>
    @else
        <div class="col-12">
            <div class="alert alert-light border small mb-0">
                <i class="bi bi-info-circle me-1"></i>
                No address data found in the latest profile log.
            </div>
        </div>
    @endif

    <div class="col-12 mt-2">
        <small class="text-muted">
            <i class="bi bi-lock-fill me-1"></i>
            This information is read-only and cannot be edited here.
        </small>
    </div>
</div>
