@extends('admin.AdminOne.layout.assets')
@section('title', 'Arsip Logs')

@section('content')
    <div class="page_main">
        <div class="container-fluid text-left">
            <div class="row">

                {{-- HEADER --}}
                <div class="col-md-12 bg_page_main hd" line="hd_action">
                    <div class="col-md-12 hd_page_main" id="pageTitle">Arsip Logs</div>
                    <div class="col-md-12 bg_act_page_main">
                        <div class="row">
                            <div class="col-xl-12 col_act_page_main text-left" id="headerActions"></div>
                        </div>
                    </div>
                </div>

                {{-- PANEL: LIST --}}
                <div class="col-md-12 bg_page_main form_action" id="panel-list" line="form_action">
                    <div class="col-md-12 bg_act_page_main page">
                        <div class="row">
                            <div class="col-xl-12 col_act_page_main text-right">
                                @include('admin.AdminOne.layout.paginationajax')
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 data_page">
                        <div class="row bg_data_page">
                            <div class="table_data freezeHead freezeCol">
                                <table class="table_view table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width:50px; text-align:center;">No</th>
                                            <th class="sortable" style="min-width:120px;" data-sort="code_data">Tanggal Aktivitas <i class="fa fa-sort"></i></th>
                                            <th class="sortable" style="min-width:120px;" data-sort="code_kategori">Pengguna <i class="fa fa-sort"></i></th>
                                            <th class="sortable" style="min-width:300px;" data-sort="judul">Aktifitas <i class="fa fa-sort"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="DataTableBody">
                                        <tr>
                                            <td colspan="4" class="text-center p-4">
                                                <i class="fa fa-spinner fa-spin"></i> Memuat data...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {

            /* =============================================================
            * CONSTANTS
            * ============================================================= */
            const routes = {
                list     : "{{ url('/admin/datalistarsiplogs') }}"
            };

            const action = {
                new    : {{ (($level_user['newarsip']    ?? 'No') === 'Yes') ? 'true' : 'false' }},
                edit   : {{ (($level_user['editarsip']   ?? 'No') === 'Yes') ? 'true' : 'false' }},
                delete : {{ (($level_user['deletearsip'] ?? 'No') === 'Yes') ? 'true' : 'false' }},
                export : {{ (($level_user['exportarsip'] ?? 'No') === 'Yes') ? 'true' : 'false' }}
            };

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

            /* =============================================================
            * STATE
            * ============================================================= */
            const state = {
                page        : 1,
                search      : '',
                perPage     : parseInt($('#countvdajax').val()) || 10,
                sortBy      : 'created_at',
                sortOrder   : 'desc',
                searchTimer : null,
                mode        : 'list',
                currentCode : null
            };

            function formatTanggal(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;
                const opts = { weekday:'long', day:'numeric', month:'long', year:'numeric', timeZone:'Asia/Jakarta' };
                const tOpts = { hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:false, timeZone:'Asia/Jakarta' };
                return date.toLocaleDateString('id-ID', opts) + ' — ' + date.toLocaleTimeString('id-ID', tOpts);
            }

            /* =============================================================
            * PANEL — tampil / sembunyikan
            * ============================================================= */
            function showPanel(mode, code) {
                state.mode        = mode;
                state.currentCode = code || null;

                $('#panel-list, #panel-form, #panel-view').hide();
                $('#form-alert').hide();
                renderHeader();

                switch (mode) {
                    case 'list':
                        $('#panel-list').show();
                        break;

                    case 'add':
                        resetForm();
                        $('#field_code_data').val('Auto-generate');
                        $('#panel-form').show();
                        break;

                    case 'edit':
                        resetForm();
                        loadDetail(code, 'edit');
                        $('#panel-form').show();
                        break;

                    case 'view':
                        loadDetail(code, 'view');
                        $('#panel-view').show();
                        break;
                }
            }

            /* =============================================================
            * HEADER — render tombol sesuai mode
            * ============================================================= */
            function renderHeader() {
                let title   = 'Arsip Logs';
                let buttons = '<button type="button" class="btn btn-secondary" onclick="BackPage()">'
                            + '<i class="fa fa-chevron-left"></i> Kembali</button> ';

                switch (state.mode) {
                    case 'list':
                        title = 'Arsip Logs';
                        if (action.new)
                            buttons += '<button type="button" class="btn btn-primary" id="btnTambah">'
                                    + '<i class="fa fa-plus"></i> Tambah Data</button> ';
                        if (action.export)
                            buttons += '<button type="button" class="btn btn-info" '
                                    + 'onclick="exportdata({url:\'/admin/exportarsip\', btn:this})">'
                                    + '<i class="fa fa-download"></i> Export Data</button>';
                        break;
                    case 'add'  : title = 'Tambah Data Arsip'; break;
                    case 'edit' : title = 'Ubah Data Arsip';   break;
                    case 'view' : title = 'Detail Data Arsip';  break;
                }

                $('#pageTitle').text(title);
                $('#headerActions').html(buttons);

                // Bind tombol Tambah — hapus event lama terlebih dulu supaya tidak menumpuk
                $('#btnTambah').off('click').on('click', function () { showPanel('add'); });
            }

            /* =============================================================
            * LOAD DATA
            * ============================================================= */
            function loadData(page) {
                state.page = page || 1;
                $.ajax({
                    url        : routes.list,
                    type       : 'GET',
                    data       : {
                        page       : state.page,
                        search     : state.search,
                        per_page   : state.perPage,
                        sort_by    : state.sortBy,
                        sort_order : state.sortOrder
                    },
                    beforeSend : renderLoading,
                    success    : function (res) {
                        renderTable(res.results);
                        renderPagination(res.results);
                    },
                    error: renderError
                });
            }

            /* =============================================================
            * RENDER TABLE
            * ============================================================= */
            function renderLoading() {
                $('#DataTableBody').html(
                    '<tr><td colspan="4" class="text-center">' +
                    '<i class="fa fa-spinner fa-spin"></i> Memuat data...</td></tr>'
                );
            }

            function renderError() {
                $('#DataTableBody').html(
                    '<tr><td colspan="4" class="text-danger text-center">Gagal memuat data</td></tr>'
                );
            }

            function renderTable(res) {
                if (!res.data || !res.data.length) {
                    $('#DataTableBody').html('<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>');
                    return;
                }

                let html = '';
                $.each(res.data, function (index, item) {
                    const editCls    = action.edit   ? '' : 'disabled text-muted';
                    const deleteCls  = action.delete ? '' : 'disabled text-muted';
                    const editAttr   = action.edit   ? '' : 'aria-disabled="true" tabindex="-1"';
                    const deleteAttr = action.delete ? '' : 'aria-disabled="true" tabindex="-1"';

                    html +=
                        '<tr>' +
                            '<td class="text-center">' + (res.from + index) + '</td>' +
                            '<td>' + (item.created_at   || '-') + '</td>' +
                            '<td>' + (item.code_user    || '-') + '</td>' +
                            '<td>' + (item.aksi         || '-') + '</td>' +
                        '</tr>';
                });

                $('#DataTableBody').html(html);
            }

            /* =============================================================
            * RENDER PAGINATION
            * ============================================================= */
            function renderPagination(res) {
                const prevPage = res.current_page > 1             ? res.current_page - 1 : null;
                const nextPage = res.current_page < res.last_page ? res.current_page + 1 : null;

                $('#totalData').text(res.total);
                $('#currentPageText').text(res.current_page);
                $('#prevPageText').text(prevPage || '-');
                $('#nextPageText').text(nextPage || '-');

                setPageBtn('#btnFirst',    1,             res.current_page === 1);
                setPageBtn('#btnPrevPage', prevPage,      !prevPage);
                setPageBtn('#btnNextPage', nextPage,      !nextPage);
                setPageBtn('#btnLast',     res.last_page, res.current_page === res.last_page);

                $('#btnPrevPage').toggle(!!prevPage);
                $('#btnNextPage').toggle(!!nextPage);
            }

            function setPageBtn(selector, page, disabled) {
                $(selector).data('page', page).prop('disabled', disabled);
            }

            /* =============================================================
            * EVENT LISTENERS
            * ============================================================= */

            // Tombol form & view
            $('#btnCancelForm').on('click',   function () { showPanel('list'); });
            $('#btnBackFromView').on('click', function () { showPanel('list'); });
            $('#btnEditFromView').on('click', function () { showPanel('edit', $(this).data('code')); });

            // Search dengan debounce
            $('#searchInput').on('keyup', function () {
                clearTimeout(state.searchTimer);
                const val = $(this).val().trim();
                state.searchTimer = setTimeout(function () {
                    state.search = val;
                    loadData(1);
                }, 400);
            });

            // Per-page
            $('#countvdajax').on('change keyup', function () {
                let val = parseInt($(this).val());
                if (isNaN(val) || val < 1) val = 10;
                state.perPage = val;
                loadData(1);
            });

            // Pagination buttons
            $(document).on('click', '#btnFirst, #btnPrevPage, #btnNextPage, #btnLast', function (e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).prop('disabled')) return false;
                loadData(page);
                return false;
            });

            // Sortable headers
            $(document).on('click', '.sortable', function () {
                const sort      = $(this).data('sort');
                state.sortOrder = (state.sortBy === sort && state.sortOrder === 'asc') ? 'desc' : 'asc';
                state.sortBy    = sort;
                $('.sortable i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
                $(this).find('i')
                    .removeClass('fa-sort')
                    .addClass(state.sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
                loadData(1);
            });

            /* =============================================================
            * INIT
            * ============================================================= */
            showPanel('list');
            loadData(1);

        });
    </script>
@endsection