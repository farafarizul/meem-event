<x-app-layout>
    <x-slot name="header">Create Branch</x-slot>

    <div class="nk-block">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title">
                                <h6 class="title"><em class="icon ni ni-plus-circle me-1"></em>New Branch</h6>
                            </div>
                        </div>
                        <div class="card-inner">
                            <form action="{{ route('admin.branches.store') }}" method="POST">
                                @csrf

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Branch Name <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="branch_name"
                                                    class="form-control @error('branch_name') is-invalid @enderror"
                                                    value="{{ old('branch_name') }}" required>
                                                @error('branch_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Branch Code <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="branch_code"
                                                    class="form-control @error('branch_code') is-invalid @enderror"
                                                    value="{{ old('branch_code') }}" required>
                                                @error('branch_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Branch Type <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
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
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Branch Phone</label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="branch_phone"
                                                    class="form-control @error('branch_phone') is-invalid @enderror"
                                                    value="{{ old('branch_phone') }}">
                                                @error('branch_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Branch Address</label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="branch_address"
                                                    class="form-control @error('branch_address') is-invalid @enderror"
                                                    value="{{ old('branch_address') }}">
                                                @error('branch_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Postcode</label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="postcode"
                                                    class="form-control @error('postcode') is-invalid @enderror"
                                                    value="{{ old('postcode') }}">
                                                @error('postcode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">State</label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="state"
                                                    class="form-control @error('state') is-invalid @enderror"
                                                    value="{{ old('state') }}">
                                                @error('state')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Area</label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="area"
                                                    class="form-control @error('area') is-invalid @enderror"
                                                    value="{{ old('area') }}">
                                                @error('area')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Person In Charge Name</label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="person_in_charge_name"
                                                    class="form-control @error('person_in_charge_name') is-invalid @enderror"
                                                    value="{{ old('person_in_charge_name') }}">
                                                @error('person_in_charge_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Person In Charge Phone</label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="person_in_charge_phone"
                                                    class="form-control @error('person_in_charge_phone') is-invalid @enderror"
                                                    value="{{ old('person_in_charge_phone') }}">
                                                @error('person_in_charge_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <em class="icon ni ni-save me-1"></em>Create Branch
                                    </button>
                                    <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">
                                        <em class="icon ni ni-arrow-left me-1"></em>Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
