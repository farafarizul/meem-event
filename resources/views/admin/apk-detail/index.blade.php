<x-app-layout>
    <style>
        .wrap-pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            word-break: break-all;
        }
    </style>
    <x-slot name="header">APK File Management</x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-android2 me-1"></i>All APK Files</h6>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.apk-detail.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-cloud-upload me-1"></i>Upload APK
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="apk-table" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Filename</th>

                            <th>Uploaded Date</th>
                            <th>Description</th>
                            <th>Download Link</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Delete Confirm Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="delete-name"></strong>?
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-delete">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        var deleteApkId = null;

        var table = $('#apk-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.apk-detail.datatable') }}',
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex',       name: 'DT_RowIndex',       orderable: false, searchable: false, width: '50px' },
                { data: 'original_filename', name: 'original_filename' },
                { data: 'uploaded_date',     name: 'uploaded_date' },
                { data: 'description',       name: 'description', className: 'wrap-pre' },
                { data: 'download_link',     name: 'download_link',     orderable: false, searchable: false },
                { data: 'action',            name: 'action',            orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[3, 'desc']],
            pageLength: 25,
            language: { processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...' }
        });

        // Delete button
        $(document).on('click', '.btn-delete', function () {
            deleteApkId = $(this).data('id');
            $('#delete-name').text($(this).data('name'));
            $('#deleteModal').modal('show');
        });

        // Confirm delete
        $('#btn-confirm-delete').on('click', function () {
            $.ajax({
                url: '/admin/apk-detail/' + deleteApkId,
                method: 'POST',
                data: { _method: 'DELETE' },
                success: function () {
                    $('#deleteModal').modal('hide');
                    table.ajax.reload(null, false);
                },
                error: function () {
                    alert('Could not delete APK. Please try again.');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
