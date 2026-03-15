<x-app-layout>
    <x-slot name="header">List of Industries</x-slot>

    <div id="sync-alert" class="alert alert-dismissible fade show d-none" role="alert">
        <span id="sync-alert-msg"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-building me-1"></i>All Industries</h6>
            <button id="btn-sync" class="btn btn-sm btn-success">
                <i class="bi bi-arrow-repeat me-1"></i>Sync Now
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="list-industries-table" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Industry ID</th>
                            <th>API ID</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        var table = $('#list-industries-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.settings.list-industries.datatable') }}',
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex',  name: 'DT_RowIndex',  orderable: false, searchable: false, width: '50px' },
                { data: 'industry_id',  name: 'industry_id',  width: '80px' },
                { data: 'id',           name: 'id',           width: '80px' },
                { data: 'name',         name: 'name' },
                { data: 'created_at',   name: 'created_at' },
                { data: 'updated_at',   name: 'updated_at' }
            ],
            order: [[1, 'asc']],
            pageLength: 25,
            language: {
                processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...'
            }
        });

        function showSyncAlert(type, message) {
            var $alert = $('#sync-alert');
            $alert.removeClass('alert-success alert-danger d-none').addClass('alert-' + type);
            $('#sync-alert-msg').text(message);
        }

        $('#btn-sync').on('click', function () {
            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span>Syncing...');

            $.ajax({
                url: '{{ route('admin.settings.list-industries.sync') }}',
                method: 'POST',
                success: function (res) {
                    showSyncAlert('success', 'Sync complete! Inserted: ' + res.inserted + ', Updated: ' + res.updated + ', Deleted: ' + res.deleted);
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Sync failed. Please try again.';
                    showSyncAlert('danger', msg);
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="bi bi-arrow-repeat me-1"></i>Sync Now');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
