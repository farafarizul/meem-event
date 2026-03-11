<x-app-layout>
    <x-slot name="header">Check-in Records</x-slot>

    {{-- Event Selector --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.checkins.index') }}" class="d-flex align-items-center gap-3 flex-wrap">
                <label class="form-label fw-semibold mb-0 text-nowrap">
                    <i class="bi bi-calendar2-event me-1"></i>Filter by Event:
                </label>
                <select name="event_id" id="event-selector" class="form-select form-select-sm" style="max-width:350px;">
                    <option value="">-- All Events --</option>
                    @foreach ($events as $event)
                        <option value="{{ $event->event_id }}" {{ (string) $selectedEventId === (string) $event->event_id ? 'selected' : '' }}>
                            {{ $event->event_name }}
                            <small>({{ $event->unique_identifier }})</small>
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-filter me-1"></i>Apply
                </button>
                @if ($selectedEventId)
                    <a href="{{ route('admin.checkins.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-check2-circle me-1"></i>
                Check-in Records
                @if ($selectedEventId)
                    @php $selectedEvent = $events->firstWhere('event_id', $selectedEventId); @endphp
                    @if ($selectedEvent)
                        &mdash; <span class="text-muted fw-normal">{{ $selectedEvent->event_name }}</span>
                    @endif
                @endif
            </h6>
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
                <table id="checkins-table" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Meem Code</th>
                            <th>Full Name</th>
                            <th>Phone Number</th>
                            <th>Event</th>
                            <th>Checked In At</th>
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
                    Remove check-in record for <strong id="delete-name"></strong>?
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
        var deleteCheckinId = null;
        var selectedEventId = '{{ $selectedEventId ?? '' }}';
        var exportUrl       = '{{ route('admin.checkins.export') }}';

        var table = $('#checkins-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.checkins.datatable') }}',
                type: 'GET',
                data: function (d) {
                    d.event_id = selectedEventId;
                    return d;
                }
            },
            columns: [
                { data: 'DT_RowIndex',  name: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
                { data: 'meem_code',    name: 'meem_code', searchable: true },
                { data: 'fullname',     name: 'fullname', searchable: true },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'event_name',   name: 'event_name', searchable: true },
                { data: 'checked_in_at', name: 'checked_in_at' },
                { data: 'action',       name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[5, 'desc']],
            pageLength: 25,
            language: { processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...' }
        });

        // Export filtered (current search + event filter)
        $('#btn-export-filtered').on('click', function () {
            var search = table.search();
            var url    = exportUrl + '?search=' + encodeURIComponent(search);
            if (selectedEventId) url += '&event_id=' + selectedEventId;
            window.location.href = url;
        });

        // Export all (all check-ins, or all for selected event)
        $('#btn-export-all').on('click', function () {
            var url = exportUrl;
            if (selectedEventId) url += '?event_id=' + selectedEventId;
            window.location.href = url;
        });

        // Delete button
        $(document).on('click', '.btn-delete', function () {
            deleteCheckinId = $(this).data('id');
            $('#delete-name').text($(this).data('name'));
            $('#deleteModal').modal('show');
        });

        // Confirm delete
        $('#btn-confirm-delete').on('click', function () {
            $.ajax({
                url: '/admin/checkins/' + deleteCheckinId,
                method: 'POST',
                data: { _method: 'DELETE' },
                success: function () {
                    $('#deleteModal').modal('hide');
                    table.ajax.reload(null, false);
                },
                error: function () {
                    alert('Could not delete check-in record. Please try again.');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
