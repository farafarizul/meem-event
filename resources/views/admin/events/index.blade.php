<x-app-layout>
    <x-slot name="header">Events Management</x-slot>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner-group">
                <div class="card-inner">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title"><em class="icon ni ni-calendar me-1"></em>All Events</h6>
                        </div>
                        <div class="card-tools d-flex gap-2">
                            <button id="btn-export-filtered" class="btn btn-outline-secondary btn-sm">
                                <em class="icon ni ni-filter me-1"></em>Export Filtered
                            </button>
                            <button id="btn-export-all" class="btn btn-outline-success btn-sm">
                                <em class="icon ni ni-file-xls me-1"></em>Export All
                            </button>
                            <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm">
                                <em class="icon ni ni-plus-circle me-1"></em>New Event
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-inner p-0">
                    <div class="table-responsive">
                        <table id="events-table" class="table table-orders w-100">
                            <thead class="tb-odr-head">
                                <tr class="tb-odr-item">
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
