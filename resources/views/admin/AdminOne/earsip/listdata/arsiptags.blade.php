@extends('admin.AdminOne.layout.assets')
@section('title', 'Data Arsip Tag')

@section('content')
    <div class="page_main">
        <div class="container-fluid text-left">
            <div class="row">

                {{-- HEADER --}}
                <div class="col-md-12 bg_page_main hd" line="hd_action">
                    <div class="col-md-12 hd_page_main" id="pageTitle">Data Arsip Tag</div>
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
                                            <th width="50" class="text-center">No</th>
                                            <th class="sortable" data-sort="code_data">Kode Data <i class="fa fa-sort"></i></th>
                                            <th class="sortable" data-sort="nama_tag">Nama Tag <i class="fa fa-sort"></i></th>
                                            <th width="100" class="text-center"><i class="head fa fa-cog"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="DataTableBody">
                                        <tr>
                                            <td colspan="5" class="text-center p-4">
                                                <i class="fa fa-spinner fa-spin"></i> Memuat data...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PANEL: FORM --}}
                <div class="col-md-12 bg_page_main form_action" id="panel-form" style="display:none;" line="form_action">
                    <div class="col-md-12 data_page">
                        <div class="col-md-12 bg_act_page_main page">
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="form-alert" style="display:none;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Kode Data</label>
                                    <div class="col-sm-10 input">
                                        <input type="text" id="field_code_data" placeholder="Auto-generate jika kosong" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Nama Tag <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <div class="field-wrapper" id="wrap_namatag">
                                            <input type="text" id="field_namatag" placeholder="Masukkan nama tag...">
                                        </div>
                                        <div class="field-error-msg" id="err_namatag">
                                            <i class="fa fa-info-circle"></i>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group form_input text-left">
                                    <button type="button" class="btn btn-primary" id="btnSaveForm">
                                        <i class="fa fa-save"></i> Simpan
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="btnCancelForm">
                                        <i class="fa fa-times"></i> Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PANEL: VIEW --}}
                <div class="col-md-12 bg_page_main form_action" id="panel-view" style="display:none;" line="form_action">
                    <div class="col-md-12 data_page">
                        <div class="table_data">
                            <table class="table_view table-striped table-hover" id="viewTable">
                                <tbody>
                                    <tr>
                                        <th width="160" class="bg-light">Kode Data</th>
                                        <td id="view_code_data">-</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Nama Tag</th>
                                        <td id="view_namatag">-</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Dibuat Pada</th>
                                        <td id="view_created_at">-</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Diperbarui Pada</th>
                                        <td id="view_updated_at">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group form_input text-left">
                                    @if(($level_user['editarsiptags'] ?? 'No') === 'Yes')
                                        <button type="button" class="btn btn-warning btn-sm" id="btnEditFromView">
                                            <i class="fa fa-edit"></i> Ubah Data
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-secondary btn-sm" id="btnBackFromView">
                                        <i class="fa fa-arrow-left"></i> Kembali
                                    </button>
                                </div>
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
            /* =========================================================
            * CONFIG
            * ========================================================= */
            const routes = {
                list   : "{{ url('/admin/datalistarsiptags') }}",
                store  : "{{ url('/admin/savearsiptags') }}",
                detail : "{{ url('/admin/viewarsiptags') }}",
                update : "{{ url('/admin/updatearsiptags') }}",
                delete : "{{ url('/admin/deletearsiptags') }}"
            };

            const action = {
                new    : {{ (($level_user['newarsiptags']    ?? 'No') === 'Yes') ? 'true' : 'false' }},
                edit   : {{ (($level_user['editarsiptags']   ?? 'No') === 'Yes') ? 'true' : 'false' }},
                delete : {{ (($level_user['deletearsiptags'] ?? 'No') === 'Yes') ? 'true' : 'false' }},
                export : {{ (($level_user['exportarsiptags'] ?? 'No') === 'Yes') ? 'true' : 'false' }}
            };

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

            /* =========================================================
            * STATE
            * ========================================================= */
            const state = {
                page       : 1,
                search     : '',
                perPage    : parseInt($('#countvdajax').val()) || 10,
                sortBy     : 'created_at',
                sortOrder  : 'asc',
                timeout    : null,
                mode       : 'list',
                currentCode: null
            };

            /* =========================================================
            * PANEL & HEADER
            * ========================================================= */
            function showPanel(mode, code = null) {
                state.mode        = mode;
                state.currentCode = code;

                $('#panel-list, #panel-form, #panel-view').hide();
                $('#form-alert').hide();
                clearFormErrors();

                switch (mode) {
                    case 'list':
                        renderHeader();
                        $('#panel-list').show();
                        break;
                    case 'add':
                        renderHeader();
                        resetForm();
                        $('#field_code_data').val('Auto-generate');
                        $('#panel-form').show();
                        break;
                    case 'edit':
                        renderHeader();
                        resetForm();
                        loadDetail(code, 'edit');
                        $('#panel-form').show();
                        break;
                    case 'view':
                        renderHeader();
                        loadDetail(code, 'view');
                        $('#panel-view').show();
                        break;
                }
            }

            function renderHeader() {
                let title   = 'Data Arsip Tag';
                let buttons = `<button type="button" class="btn btn-secondary" onclick="BackPage()">
                                <i class="fa fa-chevron-left"></i> Kembali
                            </button> `;

                switch (state.mode) {
                    case 'list':
                        if (action.new) {
                            buttons += `<button type="button" class="btn btn-primary" id="btnTambah">
                                            <i class="fa fa-plus"></i> Tambah Data
                                        </button> `;
                        }
                        if (action.export) {
                            buttons += `<button type="button" class="btn btn-info"
                                            onclick="exportdata({url:'/admin/exportarsiptags', btn:this})">
                                            <i class="fa fa-download"></i> Export Data
                                        </button>`;
                        }
                        break;
                    case 'add'  : title = 'Tambah Data Arsip Tag'; break;
                    case 'edit' : title = 'Ubah Data Arsip Tag';   break;
                    case 'view' : title = 'Detail Data Arsip Tag';  break;
                }

                $('#pageTitle').text(title);
                $('#headerActions').html(buttons);

                if (state.mode === 'list') {
                    $('#btnTambah').on('click', function () { showPanel('add'); });
                }
            }

            /* =========================================================
            * FORM HELPERS
            * ========================================================= */
            function resetForm() {
                $('#field_code_data').val('');
                $('#field_namatag').val('');
                clearFormErrors();
            }

            function clearFormErrors() {
                // Hapus class is-invalid
                $('.field-wrapper').removeClass('is-invalid');

                // Reset inline style border yang di-set paksa via jQuery
                $('.field-wrapper input, .field-wrapper textarea').css({
                    'border'          : '',
                    'box-shadow'      : '',
                    'background-color': ''
                });

                // Sembunyikan semua pesan error
                $('.field-error-msg').hide();
                $('.field-error-msg span').text('');
            }

            function setFieldError(wrapperId, errId, message) {
                const $wrapper = $('#' + wrapperId);

                $wrapper.addClass('is-invalid');

                $wrapper.find('input, textarea').css({
                    'border'          : '1px solid #e0294a',
                    'box-shadow'      : 'none',
                    'background-color': '#fff9f9'
                });

                const $errBox = $('#' + errId);
                $errBox.find('span').text(message);

                // Paksa warna merah langsung via jQuery
                $errBox.css('color', '#e0294a');
                $errBox.find('i, span').css('color', '#e0294a');

                $errBox.show();
            }

            /* =========================================================
            * LOAD DETAIL
            * ========================================================= */
            function loadDetail(code, target) {
                if (target === 'view') {
                    const loading = '<i class="fa fa-spinner fa-spin"></i> Memuat...';
                    $('#view_code_data, #view_namatag, #view_created_at, #view_updated_at')
                        .html(loading);
                }

                $.ajax({
                    url  : routes.detail,
                    type : 'GET',
                    data : { code_data: code },
                    success: function (res) {
                        const d = res.data;

                        if (target === 'view') {
                            $('#view_code_data').text(d.code_data ?? '-');
                            $('#view_namatag').text(d.nama_tag ?? '-');
                            $('#view_created_at').text(formatTanggal(d.created_at));
                            $('#view_updated_at').text(formatTanggal(d.updated_at));
                            $('#btnEditFromView').data('code', d.code_data);
                        }

                        if (target === 'edit') {
                            $('#field_code_data').val(d.code_data   ?? '');
                            $('#field_namatag').val(d.nama_tag      ?? '');
                        }
                    },
                    error: function () {
                        SystemToast('danger', 'Gagal memuat detail data');
                    }
                });
            }

            /* =========================================================
            * SIMPAN
            * ========================================================= */
            $('#btnSaveForm').on('click', function () {
                clearFormErrors();

                const namatag = $('#field_namatag').val().trim();
                let   hasError     = false;

                if (!namatag) {
                    setFieldError('wrap_namatag', 'err_namatag', 'Nama tag wajib diisi.');
                    hasError = true;
                }

                if (hasError) return;

                const isEdit  = (state.mode === 'edit');
                const url     = isEdit ? routes.update : routes.store;
                const method  = isEdit ? 'PUT' : 'POST';

                const payload = {
                    nama_tag : namatag
                };

                if (isEdit && state.currentCode) {
                    payload.code_data = state.currentCode;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

                $.ajax({
                    url      : url,
                    type     : method,
                    dataType : 'json',
                    data     : payload,
                    success: function (res) {
                        SystemToast('success', res.note || 'Data berhasil disimpan');
                        loadData(1);
                        showPanel('list');
                    },
                    error: function (xhr) {
                        const errors = xhr.responseJSON?.errors ?? {};

                        if (errors.nama_tag) {
                            setFieldError('wrap_namatag', 'err_namatag', errors.nama_tag[0]);
                        }

                        SystemToast('danger', xhr.responseJSON?.note || xhr.responseJSON?.message || 'Gagal menyimpan data');
                    },
                    complete: function () {
                        btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
                    }
                });
            });

            /* =========================================================
            * LOAD DATA
            * ========================================================= */
            function loadData(page = 1) {
                state.page = page;

                $.ajax({
                    url  : routes.list,
                    type : 'GET',
                    data : {
                        page       : page,
                        search     : state.search,
                        per_page   : state.perPage,
                        sort_by    : state.sortBy,
                        sort_order : state.sortOrder
                    },
                    beforeSend: renderLoading,
                    success: function (res) {
                        renderTable(res.results);
                        renderPagination(res.results);
                    },
                    error: renderError
                });
            }

            /* =========================================================
            * RENDER TABLE
            * ========================================================= */
            function renderLoading() {
                $('#DataTableBody').html(
                    `<tr><td colspan="5" class="text-center">
                        <i class="fa fa-spinner fa-spin"></i> Memuat data...
                    </td></tr>`
                );
            }

            function renderError() {
                $('#DataTableBody').html(
                    `<tr><td colspan="4" class="text-danger text-center">Gagal memuat data</td></tr>`
                );
            }

            function renderTable(res) {
                if (!res.data || !res.data.length) {
                    $('#DataTableBody').html(
                        `<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>`
                    );
                    return;
                }

                let html = '';

                res.data.forEach(function (item, index) {
                    const editCls    = !action.edit   ? 'disabled text-muted' : '';
                    const deleteCls  = !action.delete ? 'disabled text-muted' : '';
                    const editAttr   = !action.edit   ? 'aria-disabled="true" tabindex="-1"' : '';
                    const deleteAttr = !action.delete ? 'aria-disabled="true" tabindex="-1"' : '';

                    html += `
                        <tr>
                            <td class="text-center">${res.from + index}</td>
                            <td>${item.code_data    ?? '-'}</td>
                            <td>${item.nama_tag     ?? '-'}</td>
                            <td class="text-center">
                                <div class="dropdown dropleft">
                                    <button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                        Atur
                                    </button>
                                    <div class="dropdown-menu">
                                        <h5 class="dropdown-header">Pengaturan Data</h5>
                                        <a class="dropdown-item btn-view" style="cursor:pointer;"
                                        data-code="${item.code_data}">
                                            Lihat Data
                                        </a>
                                        <a class="dropdown-item btn-edit ${editCls}" style="cursor:pointer;"
                                        data-code="${item.code_data}" ${editAttr}>
                                            Ubah Data
                                        </a>
                                        <a class="dropdown-item delete-data ${deleteCls}" style="cursor:pointer;"
                                        data-code="${item.code_data}"
                                        data-name="${item.nama_tag ?? ''}" ${deleteAttr}>
                                            Hapus Data
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                $('#DataTableBody').html(html);
            }

            /* =========================================================
            * RENDER PAGINATION
            * ========================================================= */
            function renderPagination(res) {
                const prevPage = res.current_page > 1             ? res.current_page - 1 : null;
                const nextPage = res.current_page < res.last_page ? res.current_page + 1 : null;

                $('#totalData').text(res.total);
                $('#currentPageText').text(res.current_page);
                $('#prevPageText').text(prevPage ?? '-');
                $('#nextPageText').text(nextPage ?? '-');

                toggleBtn('#btnFirst',    1,             res.current_page === 1);
                toggleBtn('#btnPrevPage', prevPage,      !prevPage);
                toggleBtn('#btnNextPage', nextPage,      !nextPage);
                toggleBtn('#btnLast',     res.last_page, res.current_page === res.last_page);

                $('#btnPrevPage').toggle(!!prevPage);
                $('#btnNextPage').toggle(!!nextPage);
            }

            function toggleBtn(selector, page, disabled) {
                $(selector).data('page', page).prop('disabled', disabled);
            }

            /* =========================================================
            * EVENT LISTENERS
            * ========================================================= */

            // Tombol form
            $('#btnCancelForm').on('click',   function () { showPanel('list'); });
            $('#btnBackFromView').on('click', function () { showPanel('list'); });
            $('#btnEditFromView').on('click', function () {
                showPanel('edit', $(this).data('code'));
            });

            // Search
            $('#searchInput').on('keyup', function () {
                clearTimeout(state.timeout);
                state.timeout = setTimeout(function () {
                    state.search = $('#searchInput').val().trim();
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

            // Pagination
            $(document).on('click', '#btnFirst, #btnPrevPage, #btnNextPage, #btnLast', function (e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).prop('disabled')) return false;
                loadData(page);
                return false;
            });

            // Sort kolom
            $(document).on('click', '.sortable', function () {
                const sort    = $(this).data('sort');
                state.sortOrder = (state.sortBy === sort && state.sortOrder === 'asc') ? 'desc' : 'asc';
                state.sortBy    = sort;

                $('.sortable i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
                $(this).find('i')
                    .removeClass('fa-sort')
                    .addClass(state.sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down');

                loadData(1);
            });

            // Aksi tabel
            $(document).on('click', '.btn-view', function () {
                showPanel('view', $(this).data('code'));
            });

            $(document).on('click', '.btn-edit:not(.disabled)', function () {
                showPanel('edit', $(this).data('code'));
            });

            // Blokir item disabled
            $(document).on('click', '.dropdown-item.disabled', function (e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            });

            // Hapus data
            $(document).on('click', '.delete-data:not(.disabled)', function () {
                const code  = $(this).data('code');
                const name  = $(this).data('name');
                const modal = $('div[data-model="confirmasi"]');

                modal.modal({ backdrop: false });
                modal.find('.modal-body').html(`<div class="alert alert-danger">Anda yakin ingin menghapus data <b>${name}</b>?</div>`);

                $('button[btn-action="action-confirmasi"]').remove();
                $('button[btn-action="close-confirmasi"]').before(`<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>`);

                $(document)
                    .off('click', '[btn-action="action-confirmasi"]')
                    .on('click',  '[btn-action="action-confirmasi"]', function () {
                        const btn = $(this);
                        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menghapus...');

                        $.ajax({
                            url  : routes.delete,
                            type : 'DELETE',
                            data : { code_data: code },
                            success: function (res) {
                                modal.modal('hide');
                                loadData(state.page);
                                SystemToast('success', res.note || 'Data berhasil dihapus');
                            },
                            error: function (xhr) {
                                SystemToast('danger', xhr.responseJSON?.note || 'Data gagal dihapus');
                            },
                            complete: function () {
                                btn.remove();
                            }
                        });
                    });
            });

            /* =========================================================
            * UTILS
            * ========================================================= */
            function formatTanggal(dateString) {
                if (!dateString) return '-';
                const date    = new Date(dateString);
                const tanggal = date.toLocaleDateString('id-ID', {
                    weekday  : 'long',
                    day      : 'numeric',
                    month    : 'long',
                    year     : 'numeric',
                    timeZone : 'Asia/Jakarta'
                });
                const waktu = date.toLocaleTimeString('id-ID', {
                    hour     : '2-digit',
                    minute   : '2-digit',
                    second   : '2-digit',
                    hour12   : false,
                    timeZone : 'Asia/Jakarta'
                });
                return `${tanggal} - ${waktu}`;
            }

            /* =========================================================
            * INIT
            * ========================================================= */
            showPanel('list');
            loadData(1);

        });
    </script>
@endsection