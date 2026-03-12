<x-app-layout>
    <x-slot name="header">Users Management</x-slot>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner-group">
                <div class="card-inner">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title"><em class="icon ni ni-users me-1"></em>All Users</h6>
                        </div>
                        <div class="card-tools d-flex gap-2">
                            <button id="btn-export-filtered" class="btn btn-outline-secondary btn-sm">
                                <em class="icon ni ni-filter me-1"></em>Export Filtered
                            </button>
                            <button id="btn-export-all" class="btn btn-outline-success btn-sm">
                                <em class="icon ni ni-file-xls me-1"></em>Export All
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-inner p-0">
                    <div class="table-responsive">
                        <table id="users-table" class="table table-orders w-100">
                            <thead class="tb-odr-head">
                                <tr class="tb-odr-item">
                                    <th>#</th>
                                    <th>Photo</th>
                                    <th>Meem Code</th>
                                    <th>Meem ID</th>
                                    <th>Full Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Registered</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit User Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm">
                    @csrf
                    <input type="hidden" id="edit-id">
                    <div class="modal-header">
                        <h5 class="modal-title"><em class="icon ni ni-edit me-1"></em>Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="edit-errors" class="alert alert-danger d-none"></div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Meem Code <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="edit-meemcode" name="meem_code" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Meem ID</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="edit-meemid" name="meem_id">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="edit-fullname" name="fullname" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="edit-phone" name="phone_number" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <div class="form-control-wrap">
                                <input type="email" class="form-control" id="edit-email" name="email">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <em class="icon ni ni-save me-1"></em>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirm Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger"><em class="icon ni ni-alert-circle me-1"></em>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="delete-name"></strong>?
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-delete">
                        <em class="icon ni ni-trash me-1"></em>Delete
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

        // Edit button
        $(document).on('click', '.btn-edit', function () {
            var btn = $(this);
            $('#edit-id').val(btn.data('id'));
            $('#edit-meemcode').val(btn.data('meemcode'));
            $('#edit-meemid').val(btn.data('meemid'));
            $('#edit-fullname').val(btn.data('fullname'));
            $('#edit-phone').val(btn.data('phone'));
            $('#edit-email').val(btn.data('email'));
            $('#edit-errors').addClass('d-none').html('');
            $('#editModal').modal('show');
        });

        // Submit edit form via AJAX
        $('#editForm').on('submit', function (e) {
            e.preventDefault();
            var id   = $('#edit-id').val();
            var data = $(this).serialize() + '&_method=PUT';
            $('#edit-errors').addClass('d-none').html('');

            $.ajax({
                url: '/admin/users/' + id,
                method: 'POST',
                data: data,
                success: function (res) {
                    $('#editModal').modal('hide');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON && xhr.responseJSON.errors
                        ? Object.values(xhr.responseJSON.errors).flat().join('<br>')
                        : 'An error occurred. Please try again.';
                    $('#edit-errors').removeClass('d-none').html(errors);
                }
            });
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
