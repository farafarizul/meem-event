<x-app-layout>
    <x-slot name="header">Event Detail</x-slot>

    <div class="nk-block">
        <div class="row g-gs">

            {{-- Left column: event info + QR code --}}
            <div class="col-md-4">

                {{-- Event Info Card --}}
                <div class="card card-bordered mb-3">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title"><em class="icon ni ni-calendar me-1"></em>Event Info</h6>
                                </div>
                                <div class="card-tools">
                                    <span class="badge bg-{{ $event->category_event === 'online' ? 'info' : 'success' }}">
                                        {{ ucfirst($event->category_event) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner">
                            <h5 class="fw-bold mb-3">{{ $event->event_name }}</h5>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0 py-2 d-flex gap-2">
                                    <em class="icon ni ni-map-pin text-soft mt-1 flex-shrink-0"></em>
                                    <span>{{ $event->location }}</span>
                                </li>
                                <li class="list-group-item px-0 py-2 d-flex gap-2">
                                    <em class="icon ni ni-calendar-range text-soft mt-1 flex-shrink-0"></em>
                                    <span>
                                        {{ $event->start_date->format('d M Y') }}
                                        @if (!$event->start_date->eq($event->end_date))
                                            &mdash; {{ $event->end_date->format('d M Y') }}
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item px-0 py-2 d-flex gap-2">
                                    <em class="icon ni ni-key text-soft mt-1 flex-shrink-0"></em>
                                    <span class="font-monospace text-soft small">{{ $event->unique_identifier }}</span>
                                </li>
                                @if ($event->branch)
                                <li class="list-group-item px-0 py-2 d-flex gap-2">
                                    <em class="icon ni ni-building text-soft mt-1 flex-shrink-0"></em>
                                    <span>{{ $event->branch->branch_name }}</span>
                                </li>
                                @endif
                            </ul>

                            <div class="mt-3 d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning btn-sm">
                                    <em class="icon ni ni-edit me-1"></em>Edit
                                </a>
                                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary btn-sm">
                                    <em class="icon ni ni-arrow-left me-1"></em>Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- QR Code Card --}}
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title">
                                <h6 class="title"><em class="icon ni ni-qr me-1"></em>Check-in QR Code</h6>
                            </div>
                        </div>
                        <div class="card-inner text-center">
                            <p class="text-soft small mb-3">
                                Scan this QR code to open the public check-in page for this event.
                            </p>

                            {{-- SVG QR display --}}
                            <div class="d-inline-block border rounded p-2 bg-white mb-3" id="qr-container">
                                {!! $qrSvg !!}
                            </div>

                            <p class="text-soft small mb-3 text-break">
                                <em class="icon ni ni-link me-1"></em>
                                <a href="{{ $checkinUrl }}" target="_blank" class="text-soft">{{ $checkinUrl }}</a>
                            </p>

                            <a href="{{ route('admin.events.qr-download', $event) }}"
                               class="btn btn-primary w-100">
                                <em class="icon ni ni-download me-2"></em>Download High-Res PNG (1200px)
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right column: check-in records --}}
            <div class="col-md-8">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">
                                        <em class="icon ni ni-check-circle me-1"></em>Check-in Records
                                        <span class="badge bg-secondary ms-1" id="checkin-count">&ndash;</span>
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

        </div>
    </div>

    {{-- Delete Confirm Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger">
                        <em class="icon ni ni-alert-circle me-1"></em>Confirm Delete
                    </h5>
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
