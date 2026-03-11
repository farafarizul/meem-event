<x-app-layout>
    <x-slot name="header">Events Management</x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-calendar2-event me-1"></i>All Events</h6>
            <div class="d-flex gap-2">
                <button id="btn-export-filtered" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-funnel me-1"></i>Export Filtered
                </button>
                <button id="btn-export-all" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export All
                </button>
                <a href="{{ route('admin.events.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>New Event
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="events-table" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Unique ID</th>
                            <th>Event Name</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Start Date</th>
                            <th>End Date</th>
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
        var deleteEventId = null;
        var exportUrl     = '{{ route('admin.events.export') }}';

        var table = $('#events-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.events.datatable') }}',
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex',       name: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
                { data: 'unique_identifier', name: 'unique_identifier' },
                { data: 'event_name',        name: 'event_name' },
                { data: 'category_event',    name: 'category_event', className: 'text-center' },
                { data: 'location',          name: 'location' },
                { data: 'start_date',        name: 'start_date' },
                { data: 'end_date',          name: 'end_date' },
                { data: 'action',            name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[5, 'desc']],
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
            deleteEventId = $(this).data('id');
            $('#delete-name').text($(this).data('name'));
            $('#deleteModal').modal('show');
        });

        // Confirm delete
        $('#btn-confirm-delete').on('click', function () {
            $.ajax({
                url: '/admin/events/' + deleteEventId,
                method: 'POST',
                data: { _method: 'DELETE' },
                success: function () {
                    $('#deleteModal').modal('hide');
                    table.ajax.reload(null, false);
                },
                error: function () {
                    alert('Could not delete event. Please try again.');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
