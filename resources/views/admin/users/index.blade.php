<x-app-layout>
    <x-slot name="header">Users Management</x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-people me-1"></i>All Users</h6>
            <div class="d-flex gap-2">
                <button id="btn-export-filtered" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-funnel me-1"></i>Export Filtered
                </button>
                <button id="btn-export-all" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export All
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="users-table" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Meem Code</th>
                            <th>Meem ID</th>
                            <th>Full Name</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                            <th>Device</th>
                            <th>Registered</th>
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
        var deleteUserId = null;
        var exportUrl    = '{{ route('admin.users.export') }}';

        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.users.datatable') }}',
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
                { data: 'profile_picture', name: 'profile_picture', orderable: false, searchable: false, className: 'text-center', width: '60px' },
                { data: 'meem_code',    name: 'meem_code' },
                { data: 'meem_id',      name: 'meem_id', defaultContent: '-' },
                { data: 'fullname',     name: 'fullname' },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'email',        name: 'email', defaultContent: '-' },
                { data: 'device_name',        name: 'device_name', defaultContent: '-' },
                { data: 'created_at',   name: 'created_at' },
                { data: 'action',       name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[2, 'asc']],
            pageLength: 25,
            language: { processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...' }
        });

        // Export filtered
        $('#btn-export-filtered').on('click', function () {
            var search = table.search();
            window.location.href = exportUrl + '?search=' + encodeURIComponent(search);
        });

        // Export all
        $('#btn-export-all').on('click', function () {
            window.location.href = exportUrl;
        });

        // Delete button
        $(document).on('click', '.btn-delete', function () {
            deleteUserId = $(this).data('id');
            $('#delete-name').text($(this).data('name'));
            $('#deleteModal').modal('show');
        });

        // Confirm delete
        $('#btn-confirm-delete').on('click', function () {
            $.ajax({
                url: '/admin/users/' + deleteUserId,
                method: 'POST',
                data: { _method: 'DELETE' },
                success: function () {
                    $('#deleteModal').modal('hide');
                    table.ajax.reload(null, false);
                },
                error: function () {
                    alert('Could not delete user. Please try again.');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
