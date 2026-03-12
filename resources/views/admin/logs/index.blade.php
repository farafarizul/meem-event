<x-app-layout>
    <x-slot name="header">Logs</x-slot>

    @push('styles')
    {{-- Select2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        .select2-container { min-width: 160px; }
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

    {{-- Filters --}}
    <div class="nk-block">
        <div class="card card-bordered mb-3">
            <div class="card-inner py-2">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <label class="form-label fw-bold mb-0 text-nowrap">
                        <em class="icon ni ni-filter me-1"></em>Filters:
                    </label>
                    <select id="filter-meem-code" class="form-select form-select-sm select2-filter" style="min-width:170px;">
                        <option value="">All Meem Codes</option>
                        @foreach ($meemCodes as $code)
                            <option value="{{ $code }}">{{ $code }}</option>
                        @endforeach
                    </select>

                    <select id="filter-module" class="form-select form-select-sm select2-filter" style="min-width:150px;">
                        <option value="">All Modules</option>
                        @foreach ($modules as $module)
                            <option value="{{ $module }}">{{ $module }}</option>
                        @endforeach
                    </select>

                    <select id="filter-method" class="form-select form-select-sm select2-filter" style="min-width:150px;">
                        <option value="">All Methods</option>
                        @foreach ($methods as $method)
                            <option value="{{ $method }}">{{ $method }}</option>
                        @endforeach
                    </select>

                    <select id="filter-operation" class="form-select form-select-sm select2-filter" style="min-width:160px;">
                        <option value="">All Operations</option>
                        @foreach ($operations as $op)
                            <option value="{{ $op }}">{{ $op }}</option>
                        @endforeach
                    </select>

                    <button id="btn-reset-filters" class="btn btn-outline-secondary btn-sm">
                        <em class="icon ni ni-cross-circle me-1"></em>Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- DataTable --}}
        <div class="card card-bordered">
            <div class="card-inner-group">
                <div class="card-inner">
                    <div class="card-title">
                        <h6 class="title"><em class="icon ni ni-activity-alt me-1"></em>Activity Logs</h6>
                    </div>
                </div>
                <div class="card-inner p-0">
                    <div class="table-responsive">
                        <table id="logs-table" class="table table-orders w-100">
                            <thead class="tb-odr-head">
                                <tr class="tb-odr-item">
                                    <th>#</th>
                                    <th>Meem Code</th>
                                    <th>Full Name</th>
                                    <th>Category</th>
                                    <th>Module</th>
                                    <th>Method</th>
                                    <th>Operation</th>
                                    <th>JSON Data</th>
                                    <th>Date / Time</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JSON View Modal --}}
    <div class="modal fade" id="jsonModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><em class="icon ni ni-code me-1"></em>JSON Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <pre id="json-display" class="json-viewer"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // ── Select2 ────────────────────────────────────────────────
        $('.select2-filter').select2({
            theme: 'bootstrap-5',
            width: 'element',
            allowClear: true
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

        // ── DataTable ──────────────────────────────────────────────
        var table = $('#logs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.logs.datatable') }}',
                type: 'GET',
                data: function (d) {
                    d.meem_code = $('#filter-meem-code').val();
                    d.module    = $('#filter-module').val();
                    d.method    = $('#filter-method').val();
                    d.operation = $('#filter-operation').val();
                    return d;
                }
            },
            columns: [
                { data: 'DT_RowIndex',     name: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
                { data: 'meem_code',       name: 'far_log.meem_code' },
                { data: 'fullname',        name: 'users.fullname', defaultContent: '<span class="text-soft">&mdash;</span>' },
                { data: 'log_category',    name: 'far_log.log_category', defaultContent: '&mdash;' },
                { data: 'trail_module',    name: 'far_log.trail_module', defaultContent: '&mdash;' },
                { data: 'trail_method',    name: 'far_log.trail_method', defaultContent: '&mdash;' },
                { data: 'trail_operation', name: 'far_log.trail_operation', defaultContent: '&mdash;' },
                { data: 'json_preview',    name: 'far_log.log_data_json', orderable: false, searchable: false },
                { data: 'create_dttm',     name: 'far_log.create_dttm', defaultContent: '&mdash;' }
            ],
            order: [[8, 'desc']],
            pageLength: 25,
            language: {
                processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...'
            }
        });

        // ── Filter change → reload ─────────────────────────────────
        $('.select2-filter').on('change', function () {
            table.ajax.reload();
        });

        $('#btn-reset-filters').on('click', function () {
            $('.select2-filter').val(null).trigger('change');
        });

        // ── View JSON button ───────────────────────────────────────
        $(document).on('click', '.btn-view-json', function () {
            var raw = $(this).data('json') || '';
            var display;
            try {
                var parsed = JSON.parse(raw);
                display = syntaxHighlight(JSON.stringify(parsed, null, 2));
            } catch (e) {
                display = $('<div>').text(raw).html();
            }
            $('#json-display').html(display);
            $('#jsonModal').modal('show');
        });
    </script>
    @endpush
</x-app-layout>
