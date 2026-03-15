{{-- Tab 1: Maklumat Asas Pengguna --}}
<div class="row g-3">

    {{-- Users table fields (read-only) --}}
    <div class="col-12">
        <h6 class="fw-semibold text-muted mb-3">
            <i class="bi bi-person-fill me-1"></i>Maklumat Akaun
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
        <label class="form-label fw-semibold">Nama Penuh</label>
        <input type="text" class="form-control form-control-sm bg-light"
               value="{{ $user->fullname }}" disabled>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">No. Telefon</label>
        <input type="text" class="form-control form-control-sm bg-light"
               value="{{ $user->phone_number }}" disabled>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">E-mel</label>
        <input type="email" class="form-control form-control-sm bg-light"
               value="{{ $user->email ?? '' }}" disabled>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Kata Laluan</label>
        <input type="text" class="form-control form-control-sm bg-light"
               value="••••••••" disabled>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Gambar Profil</label>
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
            <input type="text" class="form-control form-control-sm bg-light" value="Tiada gambar" disabled>
        @endif
    </div>

    {{-- Divider --}}
    <div class="col-12 mt-2">
        <hr>
        <h6 class="fw-semibold text-muted mb-3">
            <i class="bi bi-geo-alt-fill me-1"></i>Maklumat Alamat (daripada rekod profil terkini)
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
            <label class="form-label fw-semibold">Alamat 1</label>
            <input type="text" class="form-control form-control-sm bg-light"
                   value="{{ $address1 ?? '' }}" disabled>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Alamat 2</label>
            <input type="text" class="form-control form-control-sm bg-light"
                   value="{{ $address2 ?? '' }}" disabled>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Bandar</label>
            <input type="text" class="form-control form-control-sm bg-light"
                   value="{{ $city ?? '' }}" disabled>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Poskod</label>
            <input type="text" class="form-control form-control-sm bg-light"
                   value="{{ $postcode ?? '' }}" disabled>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">ID Negeri</label>
            <input type="text" class="form-control form-control-sm bg-light"
                   value="{{ $stateId ?? '' }}" disabled>
        </div>
    @else
        <div class="col-12">
            <div class="alert alert-light border small mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Tiada data alamat dalam rekod log profil terkini.
            </div>
        </div>
    @endif

    <div class="col-12 mt-2">
        <small class="text-muted">
            <i class="bi bi-lock-fill me-1"></i>
            Maklumat ini adalah paparan sahaja dan tidak boleh diedit di sini.
        </small>
    </div>
</div>
