<x-app-layout>
    <x-slot name="header">Upload APK</x-slot>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-cloud-upload me-1"></i>Upload New APK File</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.apk-detail.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">APK File <span class="text-danger">*</span></label>
                                <input type="file" name="apk_file" accept=".apk"
                                    class="form-control @error('apk_file') is-invalid @enderror">
                                <div class="form-text">Only <code>.apk</code> files are allowed. Maximum size: 200 MB.</div>
                                @error('apk_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                                <textarea name="description" rows="5"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Describe the changes, fixes, or new features in this APK version...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cloud-upload me-1"></i>Upload APK
                            </button>
                            <a href="{{ route('admin.apk-detail.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
