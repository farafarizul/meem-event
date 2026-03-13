<x-app-layout>
    <x-slot name="header">Check-in Records</x-slot>

    {{-- Event Selector --}}
    <div class="nk-block">
        <div class="card card-bordered mb-3">
            <div class="card-inner py-2">
                <form method="GET" action="{{ route('admin.checkins.index') }}" class="d-flex align-items-center gap-3 flex-wrap">
                    <label class="form-label fw-bold mb-0 text-nowrap">
                        <em class="icon ni ni-calendar me-1"></em>Filter by Event:
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
                    <button type="submit" class="btn btn-primary btn-sm">
                        <em class="icon ni ni-filter me-1"></em>Apply
                    </button>
                    @if ($selectedEventId)
                        <a href="{{ route('admin.checkins.index') }}" class="btn btn-outline-secondary btn-sm">
                            <em class="icon ni ni-cross-circle me-1"></em>Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card card-bordered">
            <div class="card-inner-group">
                <div class="card-inner">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">
                                <em class="icon ni ni-check-circle me-1"></em>
                                Check-in Records
                                @if ($selectedEventId)
                                    @php $selectedEvent = $events->firstWhere('event_id', $selectedEventId); @endphp
                                    @if ($selectedEvent)
                                        &mdash; <span class="text-soft fw-normal">{{ $selectedEvent->event_name }}</span>
                                    @endif
                                @endif
                            </h6>
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
                        <table id="checkins-table" class="table table-orders w-100">
                            <thead class="tb-odr-head">
                                <tr class="tb-odr-item">
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
                    Remove check-in record for <strong id="delete-name"></strong>?
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
