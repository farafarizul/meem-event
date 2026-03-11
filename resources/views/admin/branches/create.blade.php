<x-app-layout>
    <x-slot name="header">Create Branch</x-slot>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-plus-circle me-1"></i>New Branch</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.branches.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Branch Name <span class="text-danger">*</span></label>
                                <input type="text" name="branch_name"
                                    class="form-control @error('branch_name') is-invalid @enderror"
                                    value="{{ old('branch_name') }}" required>
                                @error('branch_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Branch Code <span class="text-danger">*</span></label>
                                <input type="text" name="branch_code"
                                    class="form-control @error('branch_code') is-invalid @enderror"
                                    value="{{ old('branch_code') }}" required>
                                @error('branch_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Branch Type <span class="text-danger">*</span></label>
                                <select name="branch_type"
                                    class="form-select @error('branch_type') is-invalid @enderror" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="Branch" {{ old('branch_type') === 'Branch' ? 'selected' : '' }}>Branch</option>
                                    <option value="HQ" {{ old('branch_type') === 'HQ' ? 'selected' : '' }}>HQ</option>
                                </select>
                                @error('branch_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Branch Phone</label>
                                <input type="text" name="branch_phone"
                                    class="form-control @error('branch_phone') is-invalid @enderror"
                                    value="{{ old('branch_phone') }}">
                                @error('branch_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Branch Address</label>
                                <input type="text" name="branch_address"
                                    class="form-control @error('branch_address') is-invalid @enderror"
                                    value="{{ old('branch_address') }}">
                                @error('branch_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Postcode</label>
                                <input type="text" name="postcode"
                                    class="form-control @error('postcode') is-invalid @enderror"
                                    value="{{ old('postcode') }}">
                                @error('postcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">State</label>
                                <input type="text" name="state"
                                    class="form-control @error('state') is-invalid @enderror"
                                    value="{{ old('state') }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Area</label>
                                <input type="text" name="area"
                                    class="form-control @error('area') is-invalid @enderror"
                                    value="{{ old('area') }}">
                                @error('area')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Person In Charge Name</label>
                                <input type="text" name="person_in_charge_name"
                                    class="form-control @error('person_in_charge_name') is-invalid @enderror"
                                    value="{{ old('person_in_charge_name') }}">
                                @error('person_in_charge_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Person In Charge Phone</label>
                                <input type="text" name="person_in_charge_phone"
                                    class="form-control @error('person_in_charge_phone') is-invalid @enderror"
                                    value="{{ old('person_in_charge_phone') }}">
                                @error('person_in_charge_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Create Branch
                            </button>
                            <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
