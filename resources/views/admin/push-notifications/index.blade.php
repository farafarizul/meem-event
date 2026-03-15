<x-app-layout>
    <x-slot name="header">Push Notifications</x-slot>

    @push('styles')
    {{-- Select2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        pre.json-viewer {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: .375rem;
            padding: 1rem;
            max-height: 420px;
            overflow: auto;
            font-size: .8rem;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .json-string  { color: #198754; }
        .json-number  { color: #0d6efd; }
        .json-boolean { color: #fd7e14; }
        .json-null    { color: #6c757d; }
        .json-key     { color: #842029; }
    </style>
    @endpush

    {{-- Compose Form --}}
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-bell me-1"></i>Send Push Notification</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.push-notifications.store') }}" method="POST" id="push-notif-form">
                        @csrf

                        {{-- Recipient Mode --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Recipient Mode <span class="text-danger">*</span></label>
                            <select name="recipient_mode" id="recipient_mode"
                                class="form-select @error('recipient_mode') is-invalid @enderror" required>
                                <option value="all"      {{ old('recipient_mode', 'all') === 'all'      ? 'selected' : '' }}>All Users</option>
                                <option value="selected" {{ old('recipient_mode') === 'selected' ? 'selected' : '' }}>Selected Users</option>
                            </select>
                            @error('recipient_mode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Selected Users (shown only when recipient_mode = selected) --}}
                        <div class="mb-3" id="selected-users-wrapper" style="display:none;">
                            <label class="form-label fw-semibold">Select Recipients <span class="text-danger">*</span></label>
                            <select name="selected_users[]" id="selected_users" multiple
                                class="form-select @error('selected_users') is-invalid @enderror"
                                style="width:100%;">
                                {{-- Repopulate on validation error --}}
                                @if (old('selected_users'))
                                    @foreach (old('selected_users') as $meemCode)
                                        <option value="{{ $meemCode }}" selected>{{ $meemCode }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('selected_users')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Search by Meem Code or Full Name.</div>
                        </div>

                        {{-- Title --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notification Title <span class="text-danger">*</span></label>
                            <input type="text" name="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}" required maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Message --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Message / Content <span class="text-danger">*</span></label>
                            <textarea name="message" rows="4"
                                class="form-control @error('message') is-invalid @enderror"
                                required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Image URL --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Image URL <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="url" name="image_url"
                                class="form-control @error('image_url') is-invalid @enderror"
                                value="{{ old('image_url') }}"
                                placeholder="https://example.com/image.jpg">
                            @error('image_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Additional Data Fields --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Additional Data 1</label>
                                <input type="text" name="additional_data_1"
                                    class="form-control @error('additional_data_1') is-invalid @enderror"
                                    value="{{ old('additional_data_1') }}"
                                    placeholder="e.g. event_id=123">
                                @error('additional_data_1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Additional Data 2</label>
                                <input type="text" name="additional_data_2"
                                    class="form-control @error('additional_data_2') is-invalid @enderror"
                                    value="{{ old('additional_data_2') }}"
                                    placeholder="e.g. promo_code=ABC">
                                @error('additional_data_2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Additional Data 3</label>
                                <input type="text" name="additional_data_3"
                                    class="form-control @error('additional_data_3') is-invalid @enderror"
                                    value="{{ old('additional_data_3') }}"
                                    placeholder="e.g. type=announcement">
                                @error('additional_data_3')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btn-send">
                                <i class="bi bi-send me-1"></i>Send Notification
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- History / List --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-1"></i>Sent Notifications History</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="push-notif-table" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Mode</th>
                            <th>Recipients</th>
                            <th>Status</th>
                            <th>Sent By</th>
                            <th>Date / Time</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-bell me-1"></i>Notification Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detail-modal-body">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // ── Toggle selected-users panel ────────────────────────────
        function toggleSelectedUsers() {
            var mode = $('#recipient_mode').val();
            if (mode === 'selected') {
                $('#selected-users-wrapper').show();
                $('#selected_users').attr('required', true);
            } else {
                $('#selected-users-wrapper').hide();
                $('#selected_users').removeAttr('required');
            }
        }

        toggleSelectedUsers();
        $('#recipient_mode').on('change', toggleSelectedUsers);

        // ── Select2 AJAX ───────────────────────────────────────────
        $('#selected_users').select2({
            theme: 'bootstrap-5',
            placeholder: 'Search by Meem Code or Full Name…',
            minimumInputLength: 1,
            allowClear: true,
            ajax: {
                url: '{{ route('admin.push-notifications.users.search') }}',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            }
        });

        // ── JSON pretty-printer ────────────────────────────────────
        function syntaxHighlight(json) {
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return json.replace(
                /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
                function (match) {
                    var cls = 'json-number';
                    if (/^"/.test(match)) {
                        cls = /:$/.test(match) ? 'json-key' : 'json-string';
                    } else if (/true|false/.test(match)) {
                        cls = 'json-boolean';
                    } else if (/null/.test(match)) {
                        cls = 'json-null';
                    }
                    return '<span class="' + cls + '">' + match + '</span>';
                }
            );
        }

        function prettyJson(raw) {
            if (!raw) return '<span class="text-muted">—</span>';
            try {
                var obj = (typeof raw === 'string') ? JSON.parse(raw) : raw;
                return '<pre class="json-viewer">' + syntaxHighlight(JSON.stringify(obj, null, 2)) + '</pre>';
            } catch (e) {
                return '<pre class="json-viewer">' + $('<div>').text(raw).html() + '</pre>';
            }
        }

        // ── DataTable ──────────────────────────────────────────────
        var table = $('#push-notif-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.push-notifications.datatable') }}',
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex',    name: 'DT_RowIndex',    orderable: false, searchable: false, width: '50px' },
                { data: 'title',          name: 'title' },
                { data: 'recipient_mode', name: 'recipient_mode', className: 'text-center' },
                { data: 'total_recipient',name: 'total_recipient', className: 'text-center' },
                { data: 'send_status',    name: 'send_status',    className: 'text-center' },
                { data: 'created_by',     name: 'created_by',     defaultContent: '—' },
                { data: 'created_at',     name: 'created_at' },
                { data: 'action',         name: 'action',         orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[6, 'desc']],
            pageLength: 25,
            language: {
                processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...'
            }
        });

        // ── View Detail ────────────────────────────────────────────
        $(document).on('click', '.btn-view-detail', function () {
            var id = $(this).data('id');
            $('#detail-modal-body').html(
                '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>'
            );
            $('#detailModal').modal('show');

            $.get('{{ url('admin/push-notifications') }}/' + id, function (data) {
                var codes = '—';
                if (data.selected_meem_codes) {
                    try {
                        var arr = JSON.parse(data.selected_meem_codes);
                        codes = arr.join(', ');
                    } catch (e) {
                        codes = data.selected_meem_codes;
                    }
                }

                var statusBadge = data.send_status === 'success'
                    ? '<span class="badge bg-success">Success</span>'
                    : '<span class="badge bg-danger">Failed</span>';

                var modeBadge = data.recipient_mode === 'all'
                    ? '<span class="badge bg-primary">All</span>'
                    : '<span class="badge bg-info text-dark">Selected</span>';

                var html = '<dl class="row mb-0">'
                    + '<dt class="col-sm-3">Title</dt>'
                    + '<dd class="col-sm-9">' + $('<span>').text(data.title).html() + '</dd>'

                    + '<dt class="col-sm-3">Message</dt>'
                    + '<dd class="col-sm-9">' + $('<span>').text(data.message).html() + '</dd>'

                    + '<dt class="col-sm-3">Image URL</dt>'
                    + '<dd class="col-sm-9">'
                    + (data.image_url ? '<a href="' + $('<span>').text(data.image_url).html() + '" target="_blank">' + $('<span>').text(data.image_url).html() + '</a>' : '—')
                    + '</dd>'

                    + '<dt class="col-sm-3">Recipient Mode</dt>'
                    + '<dd class="col-sm-9">' + modeBadge + '</dd>'

                    + '<dt class="col-sm-3">Selected Meem Codes</dt>'
                    + '<dd class="col-sm-9">' + $('<span>').text(codes).html() + '</dd>'

                    + '<dt class="col-sm-3">Additional Data 1</dt>'
                    + '<dd class="col-sm-9">' + (data.additional_data_1 ? $('<span>').text(data.additional_data_1).html() : '—') + '</dd>'

                    + '<dt class="col-sm-3">Additional Data 2</dt>'
                    + '<dd class="col-sm-9">' + (data.additional_data_2 ? $('<span>').text(data.additional_data_2).html() : '—') + '</dd>'

                    + '<dt class="col-sm-3">Additional Data 3</dt>'
                    + '<dd class="col-sm-9">' + (data.additional_data_3 ? $('<span>').text(data.additional_data_3).html() : '—') + '</dd>'

                    + '<dt class="col-sm-3">Status</dt>'
                    + '<dd class="col-sm-9">' + statusBadge + '</dd>'

                    + '<dt class="col-sm-3">Total Recipients</dt>'
                    + '<dd class="col-sm-9">' + (data.total_recipient || 0) + '</dd>'

                    + '<dt class="col-sm-3">Error Message</dt>'
                    + '<dd class="col-sm-9 text-danger">' + (data.error_message ? $('<span>').text(data.error_message).html() : '—') + '</dd>'

                    + '<dt class="col-sm-3">Sent By</dt>'
                    + '<dd class="col-sm-9">' + (data.created_by ? $('<span>').text(data.created_by).html() : '—') + '</dd>'

                    + '<dt class="col-sm-3">Sent At</dt>'
                    + '<dd class="col-sm-9">' + (data.created_at || '—') + '</dd>'

                    + '<dt class="col-sm-3">OneSignal Notification ID</dt>'
                    + '<dd class="col-sm-9">' + (data.onesignal_notification_id ? $('<span>').text(data.onesignal_notification_id).html() : '—') + '</dd>'

                    + '<dt class="col-sm-3">OneSignal App ID</dt>'
                    + '<dd class="col-sm-9">' + (data.onesignal_app_id ? $('<span>').text(data.onesignal_app_id).html() : '—') + '</dd>'

                    + '</dl>'
                    + '<hr>'
                    + '<h6 class="fw-semibold mt-3"><i class="bi bi-braces me-1"></i>OneSignal Request Payload</h6>'
                    + prettyJson(data.onesignal_request_payload)
                    + '<h6 class="fw-semibold mt-3"><i class="bi bi-braces me-1"></i>OneSignal Response</h6>'
                    + prettyJson(data.onesignal_response);

                $('#detail-modal-body').html(html);
            }).fail(function () {
                $('#detail-modal-body').html('<div class="alert alert-danger">Failed to load notification details.</div>');
            });
        });
    </script>
    @endpush
</x-app-layout>
