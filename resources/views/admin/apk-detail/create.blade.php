<x-app-layout>
    <x-slot name="header">Upload APK</x-slot>

    <div class="nk-block">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title">
                                <h6 class="title"><em class="icon ni ni-upload-cloud me-1"></em>Upload New APK File</h6>
                            </div>
                        </div>
                        <div class="card-inner">
                            <form action="{{ route('admin.apk-detail.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">APK File <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="file" name="apk_file" accept=".apk"
                                                    class="form-control @error('apk_file') is-invalid @enderror">
                                                <div class="form-note">Only <code>.apk</code> files are allowed. Maximum size: 200 MB.</div>
                                                @error('apk_file')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <textarea name="description" rows="5"
                                                    class="form-control @error('description') is-invalid @enderror"
                                                    placeholder="Describe the changes, fixes, or new features in this APK version...">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <em class="icon ni ni-upload-cloud me-1"></em>Upload APK
                                    </button>
                                    <a href="{{ route('admin.apk-detail.index') }}" class="btn btn-secondary">
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
