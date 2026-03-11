<x-app-layout>
    <x-slot name="header">Event Detail</x-slot>

    <div class="row g-4">

        {{-- Left column: event info + QR code --}}
        <div class="col-md-4">

            {{-- Event Info Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-calendar2-event me-1"></i>Event Info</h6>
                    <span class="badge bg-{{ $event->category_event === 'online' ? 'info' : 'success' }}">
                        {{ ucfirst($event->category_event) }}
                    </span>
                </div>
                <div class="card-body">
                    <h5 class="fw-bold mb-3">{{ $event->event_name }}</h5>

                    <div class="mb-2 d-flex gap-2">
                        <i class="bi bi-geo-alt text-muted mt-1 flex-shrink-0"></i>
                        <span>{{ $event->location }}</span>
                    </div>
                    <div class="mb-2 d-flex gap-2">
                        <i class="bi bi-calendar-range text-muted mt-1 flex-shrink-0"></i>
                        <span>
                            {{ $event->start_date->format('d M Y') }}
                            @if (!$event->start_date->eq($event->end_date))
                                &mdash; {{ $event->end_date->format('d M Y') }}
                            @endif
                        </span>
                    </div>
                    <div class="mb-3 d-flex gap-2">
                        <i class="bi bi-key text-muted mt-1 flex-shrink-0"></i>
                        <span class="font-monospace text-muted small">{{ $event->unique_identifier }}</span>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-fill me-1"></i>Edit
                        </a>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>

            {{-- QR Code Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-qr-code me-1"></i>Check-in QR Code</h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted small mb-3">
                        Scan this QR code to open the public check-in page for this event.
                    </p>

                    {{-- SVG QR display --}}
                    <div class="d-inline-block border rounded p-2 bg-white mb-3" id="qr-container">
                        {!! $qrSvg !!}
                    </div>

                    <p class="text-muted small mb-3 text-break">
                        <i class="bi bi-link-45deg me-1"></i>
                        <a href="{{ $checkinUrl }}" target="_blank" class="text-muted">{{ $checkinUrl }}</a>
                    </p>

                    <a href="{{ route('admin.events.qr-download', $event) }}"
                       class="btn btn-primary w-100">
                        <i class="bi bi-download me-2"></i>Download High-Res PNG (1200px)
                    </a>
                </div>
            </div>

        </div>

        {{-- Right column: check-in records --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i>Check-in Records
                        <span class="badge bg-secondary ms-1" id="checkin-count">–</span>
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
                                    <th>Phone</th>
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
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>Confirm Delete
                    </h5>
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
        var eventId         = {{ $event->event_id }};
        var exportUrl       = '{{ route('admin.checkins.export') }}';

        var table = $('#checkins-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.checkins.datatable') }}',
                type: 'GET',
                data: function (d) {
                    d.event_id = eventId;
                    return d;
                }
            },
            columns: [
                { data: 'DT_RowIndex',  name: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
                { data: 'meem_code',    name: 'meem_code', searchable: true },
                { data: 'fullname',     name: 'fullname', searchable: true },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'checked_in_at', name: 'checked_in_at' },
                { data: 'action',       name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[4, 'desc']],
            pageLength: 25,
            language: {
                processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...'
            },
            drawCallback: function () {
                var info = this.api().page.info();
                $('#checkin-count').text(info.recordsTotal);
            }
        });

        // Export filtered
        $('#btn-export-filtered').on('click', function () {
            var search = table.search();
            window.location.href = exportUrl + '?event_id=' + eventId + '&search=' + encodeURIComponent(search);
        });

        // Export all for this event
        $('#btn-export-all').on('click', function () {
            window.location.href = exportUrl + '?event_id=' + eventId;
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
