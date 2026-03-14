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
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <label class="form-label fw-semibold mb-0 text-nowrap">
                    <i class="bi bi-funnel me-1"></i>Filters:
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

                <button id="btn-reset-filters" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Reset
                </button>
            </div>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-journal-text me-1"></i>Activity Logs</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="logs-table" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
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

    {{-- JSON View Modal --}}
    <div class="modal fade" id="jsonModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-braces me-1"></i>JSON Data</h5>
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
            // Match JSON strings (including object keys), booleans, nulls, and numbers
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
                { data: 'fullname',        name: 'users.fullname', defaultContent: '<span class="text-muted">—</span>' },
                { data: 'log_category',    name: 'far_log.log_category', defaultContent: '—' },
                { data: 'trail_module',    name: 'far_log.trail_module', defaultContent: '—' },
                { data: 'trail_method',    name: 'far_log.trail_method', defaultContent: '—' },
                { data: 'trail_operation', name: 'far_log.trail_operation', defaultContent: '—' },
                { data: 'json_preview',    name: 'far_log.log_data_json', orderable: false, searchable: false },
                { data: 'create_dttm',     name: 'far_log.create_dttm', defaultContent: '—' }
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

            var jsontostring = JSON.stringify(raw, null, 2);
            //if jsontostring not a string, display raw
            if (typeof jsontostring !== 'string') {
                jsontostring = String(raw);
            }else{
                display = syntaxHighlight(jsontostring);
                console.log(display)
            }

            $('#json-display').html(display);
            $('#jsonModal').modal('show');
        });
    </script>
    @endpush
</x-app-layout>
