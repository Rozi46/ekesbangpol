@extends('admin.AdminOne.layout.assets')
@section('title', 'Data Jabatan')

@section('content')
<div class="page_main">
    <div class="container-fluid text-left">
        <div class="row">

            {{-- HEADER --}}
            <div class="col-md-12 bg_page_main hd" line="hd_action">
                <div class="col-md-12 hd_page_main" id="pageTitle">Data Jabatan</div>

                <div class="col-md-12 bg_act_page_main">
                    <div class="row">
                        <div class="col-xl-12 col_act_page_main text-left" id="headerActions">
                            {{-- Header buttons dirender dinamis via JS --}}
                        </div>
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
                                        <th width="40" class="text-center">No</th>
                                        <th class="text-center sortable" data-sort="code_data">Kode Data <i class="fa fa-sort"></i></th>
                                        <th class="text-center sortable" data-sort="jenis_cuti">Jabatan <i class="fa fa-sort"></i></th>
                                        <th width="100" class="text-center">Status</th>
                                        <th width="100" class="text-center"><i class="head fa fa-cog"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="jabatanTableBody">
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

            {{-- PANEL: FORM (Tambah & Edit) --}}
            <div class="col-md-12 bg_page_main form_action" id="panel-form" style="display:none;" line="form_action">
                <div class="col-md-12 bg_act_page_main page">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="form-alert" style="display:none;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 bg_form_page">
                    <div class="form_input text-left">
                        <div class="tag_title">Kode Data</div>
                        <input type="text" id="field_code_data" class="form-control" placeholder="Auto-generate jika kosong" readonly> 
                    </div>
                </div>
                <div class="col-md-12 bg_form_page">
                    <div class="form_input text-left">
                        <div class="tag_title">Jabatan <span>*</span></div>
                        <input type="text" id="field_jabatan" class="form-control" placeholder="Masukkan jabatan..." maxlength="100"> 
                        <div class="invalid-feedback" id="err_jabatan"></div>
                    </div>
                </div>
                <div class="col-md-12 bg_form_page">
                    <div class="form_input text-left">
                        <label>Status</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="field_status" id="status_aktif" value="Aktif" checked>
                                <label class="form-check-label" for="status_aktif">Aktif</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="field_status" id="status_nonaktif" value="Tidak Aktif">
                                <label class="form-check-label" for="status_nonaktif">Tidak Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 bg_form_page">
                    <div class="form_input text-left">
                        <button type="button" class="btn btn-primary" id="btnSaveForm"><i class="fa fa-save"></i> Simpan</button>
                        <button type="button" class="btn btn-secondary" id="btnCancelForm"><i class="fa fa-times"></i> Batal</button>
                    </div>
                </div>
            </div>

            {{-- PANEL: VIEW --}}
            <div class="col-md-12 bg_page_main form_action" id="panel-view" style="display:none;" line="form_action">
                <div class="table_data">
                    <table class="table_view table-striped table-hover" id="viewTable">
                        <tbody>
                            <tr>
                                <th width="160" class="bg-light">Kode Data</th>
                                <td id="view_code_data">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Jabatan</th>
                                <td id="view_jabatan">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Status</th>
                                <td id="view_status">-</td>
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
                <div class="col-md-12 bg_form_page">
                    <div class="form_input text-left">
                        @if(($level_user['editjabatan'] ?? 'No') === 'Yes')
                            <button type="button" class="btn btn-warning btn-sm" id="btnEditFromView"><i class="fa fa-edit"></i> Ubah Data</button>
                        @endif
                        <button type="button" class="btn btn-secondary btn-sm" id="btnBackFromView"><i class="fa fa-arrow-left"></i> Kembali</button>
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
        const routes = {
            list:   "{{ url('/admin/datalistjabatan') }}",
            store:  "{{ url('/admin/savejabatan') }}",
            detail: "{{ url('/admin/viewjabatan') }}",
            update: "{{ url('/admin/updatejabatan') }}",
            status: "{{ url('/admin/statusjabatan') }}",
            delete: "{{ url('/admin/deletejabatan') }}"
        };

        const action = {
            new:    {{ (($level_user['newjabatan'] ?? 'No') === 'Yes') ? 'true' : 'false' }},
            edit:   {{ (($level_user['editjabatan'] ?? 'No') === 'Yes') ? 'true' : 'false' }},
            delete: {{ (($level_user['deletejabatan'] ?? 'No') === 'Yes') ? 'true' : 'false' }},
            export: {{ (($level_user['exportjabatan'] ?? 'No') === 'Yes') ? 'true' : 'false' }}
        };

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

        const state = {
            page: 1,
            search: '',
            perPage: parseInt($('#countvdajax').val()) || 10,
            sortBy: 'created_at',
            sortOrder: 'asc',
            timeout: null,
            mode: 'list',       // list | add | edit | view
            currentCode: null
        };

        function showPanel(mode, code = null) {
            state.mode = mode;
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
            let title = 'Data jabatan';
            let buttons = `<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button> `;

            switch (state.mode) {
                case 'list':
                    if (action.new) {
                        buttons += `<button type="button" class="btn btn-primary" id="btnTambah"><i class="fa fa-plus"></i> Tambah Data</button> `;
                    }
                    if (action.export) {
                        buttons += `<button type="button" class="btn btn-info" onclick="exportdata({url:'/admin/exportjabatan',btn:this})"><i class="fa fa-download"></i> Export Data</button>`;
                    }
                    break;

                case 'add':
                    title = 'Tambah Data Jabatan';
                    break;

                case 'edit':
                    title = 'Ubah Data Jabatan';
                    break;

                case 'view':
                    title = 'Detail Data Jabatan';
                    break;
            }

            $('#pageTitle').text(title);
            $('#headerActions').html(buttons);

            if (state.mode === 'list') {
                $('#btnTambah').on('click', function () { showPanel('add'); });
            }
        }

        function resetForm() {
            $('#field_code_data').val('');
            $('#field_jabatan').val('').removeClass('is-invalid');
            $('input[name="field_status"][value="Aktif"]').prop('checked', true);
        }

        function clearFormErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        function loadDetail(code, target) {
            const loading = `<i class="fa fa-spinner fa-spin"></i> Memuat...`;

            if (target === 'view') {
                $('#view_code_data, #view_jabatan, #view_status, #view_created_at, #view_updated_at').html(loading);
            }

            $.ajax({
                url: routes.detail,
                type: 'GET',
                data: { code_data: code },
                success: function (res) {
                    const d = res.data;                 

                    if (target === 'view') {
                        $('#view_code_data').text(d.code_data ?? '-');
                        $('#view_jabatan').text(d.jabatan ?? '-');

                        const statusHtml = d.status_data === 'Aktif'
                            ? `
                                <div class="alert alert-success"
                                    style="margin:0 auto; display:inline-block; text-align:center; font-size:12px; padding:2px 10px;">
                                    <strong>${d.status_data ?? 'Belum Ditentukan'}</strong>
                                </div>
                            `
                            : `
                                <div class="alert alert-danger"
                                    style="margin:0 auto; display:inline-block; text-align:center; font-size:12px; padding:2px 10px;">
                                    <strong>${d.status_data ?? 'Belum Ditentukan'}</strong>
                                </div>
                            `;

                        $('#view_status').html(statusHtml);
                        $('#view_created_at').text(formatTanggalIndonesia(d.created_at) ?? '-');
                        $('#view_updated_at').text(formatTanggalIndonesia(d.updated_at) ?? '-');

                        $('#btnEditFromView').data('code', d.code_data);
                    }

                    if (target === 'edit') {
                        $('#field_code_data').val(d.code_data ?? '');
                        $('#field_jabatan').val(d.jabatan ?? '');
                        $('input[name="field_status"][value="' + (d.status_data ?? 'Aktif') + '"]').prop('checked', true);
                    }
                },
                error: function () {
                    SystemToast('danger', 'Gagal memuat detail data');
                }
            });
        }

        $('#btnSaveForm').on('click', function () {
            clearFormErrors();

            const jabatan = $('#field_jabatan').val().trim();
            if (!jabatan) {
                $('#field_jabatan').addClass('is-invalid');
                $('#err_jabatan').text('jabatan wajib diisi.');
                return;
            }

            const payload = {
                code_data:   $('#field_code_data').val(),
                jabatan:     jabatan,
                status_data: $('input[name="field_status"]:checked').val()
            };

            const isEdit  = state.mode === 'edit';
            const url     = isEdit ? routes.update : routes.store;
            const method  = isEdit ? 'PUT' : 'POST';

            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: url,
                type: method,
                dataType: 'json',
                data: payload,
                success: function (res) {
                    SystemToast('success', res.note || 'Data berhasil disimpan');
                    loadData(1);
                    showPanel('list');
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON?.errors ?? {};

                    if (errors.jabatan) {
                        $('#field_jabatan').addClass('is-invalid');
                        $('#err_jabatan').text(errors.jabatan[0]);
                    }

                    SystemToast('danger', xhr.responseJSON?.note || xhr.responseJSON?.message || 'Gagal menyimpan data');
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
                }
            });
        });

        $('#btnCancelForm').on('click', function () { showPanel('list'); });

        $('#btnEditFromView').on('click', function () {
            const code = $(this).data('code');
            showPanel('edit', code);
        });

        $('#btnBackFromView').on('click', function () { showPanel('list'); });

        function loadData(page = 1) {
            state.page = page;

            $.ajax({
                url: routes.list,
                type: 'GET',
                data: {
                    page:       page,
                    search:     state.search,
                    per_page:   state.perPage,
                    sort_by:    state.sortBy,
                    sort_order: state.sortOrder
                },
                beforeSend: renderLoading,
                success: function (res) {
                    const result = res.results;
                    renderTable(result);
                    renderPagination(result);
                },
                error: renderError
            });
        }

        function renderLoading() {
            $('#jabatanTableBody').html(`
                <tr><td colspan="5" class="text-center">
                    <i class="fa fa-spinner fa-spin"></i> Memuat data...
                </td></tr>`);
        }

        function renderError() {
            $('#jabatanTableBody').html(`
                <tr><td colspan="5" class="text-danger text-center">Gagal memuat data</td></tr>`);
        }

        function renderTable(res) {
            if (!res.data.length) {
                $('#jabatanTableBody').html(`
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data</td>
                    </tr>
                `);
                return;
            }

            let html = '';

            res.data.forEach((item, index) => {
                const isActive = item.status_data === 'Aktif';
                const checked = isActive ? 'checked' : '';
                const nextStatus = isActive ? 'Tidak Aktif' : 'Aktif';

                const editDisabled = !action.edit;
                const deleteDisabled = !action.delete;

                let dropdownItems = `
                    <a class="dropdown-item btn-view" data-code="${item.code_data}">
                        Lihat Data
                    </a>

                    <a
                        class="dropdown-item btn-edit ${editDisabled ? 'disabled text-muted' : ''}"
                        data-code="${item.code_data}"
                        ${editDisabled ? 'aria-disabled="true" tabindex="-1"' : ''}
                    >
                        Ubah Data
                    </a>

                    <a
                        class="dropdown-item delete-data ${deleteDisabled ? 'disabled text-muted' : ''}"
                        data-code="${item.code_data}"
                        data-name="${item.jabatan}"
                        ${deleteDisabled ? 'aria-disabled="true" tabindex="-1"' : ''}
                    >
                        Hapus Data
                    </a>
                `;

                html += `
                    <tr>
                        <td class="text-center">${res.from + index}</td>
                        <td class="text-center">${item.code_data ?? '-'}</td>
                        <td>${item.jabatan ?? '-'}</td>
                        <td class="text-center">
                            <input
                                type="checkbox"
                                class="ios status-toggle"
                                data-code="${item.code_data}"
                                data-status="${nextStatus}"
                                ${checked}
                                ${editDisabled ? 'disabled' : ''}
                            >
                        </td>
                        <td class="text-center">
                            <div class="dropdown dropleft">
                                <button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                    Atur
                                </button>
                                <div class="dropdown-menu">
                                    <h5 class="dropdown-header">Pengaturan Data</h5>
                                    ${dropdownItems}
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            });

            $('#jabatanTableBody').html(html);
            $('.ios').iosCheckbox();
        }

        function renderPagination(res) {
            $('#totalData').text(res.total);
            $('#currentPageText').text(res.current_page);

            const prevPage = res.current_page > 1 ? res.current_page - 1 : null;
            const nextPage = res.current_page < res.last_page ? res.current_page + 1 : null;

            $('#prevPageText').text(prevPage ?? '-');
            $('#nextPageText').text(nextPage ?? '-');

            toggleButton('#btnFirst',    1,              res.current_page === 1);
            toggleButton('#btnPrevPage', prevPage,       !prevPage);
            toggleButton('#btnNextPage', nextPage,       !nextPage);
            toggleButton('#btnLast',     res.last_page,  res.current_page === res.last_page);

            $('#btnPrevPage').toggle(!!prevPage);
            $('#btnNextPage').toggle(!!nextPage);
        }

        function toggleButton(selector, page, disabled) {
            $(selector).data('page', page).prop('disabled', disabled);
        }

        $('#searchInput').hide();
        $('#closeSearch').hide();

        $('#searchInput').on('keyup', function () {
            clearTimeout(state.timeout);
            state.timeout = setTimeout(() => {
                state.search = $(this).val().trim();
                loadData(1);
            }, 400);
        });

        $('#countvdajax').on('change keyup', function () {
            let val = parseInt($(this).val());
            if (isNaN(val) || val < 1) val = 10;
            state.perPage = val;
            loadData(1);
        });

        $(document).on('click', '#btnFirst, #btnPrevPage, #btnNextPage, #btnLast', function (e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (!page || $(this).prop('disabled')) return false;
            loadData(page);
            return false;
        });

        $(document).on('click', '.sortable', function () {
            const sort = $(this).data('sort');
            if (state.sortBy === sort) {
                state.sortOrder = state.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                state.sortBy   = sort;
                state.sortOrder = 'asc';
            }

            $('.sortable i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
            $(this).find('i').removeClass('fa-sort').addClass(
                state.sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down'
            );

            loadData(1);
        });

        $(document).on('click', '.btn-view', function () {
            showPanel('view', $(this).data('code'));
        });

        $(document).on('click', '.btn-edit', function () {
            showPanel('edit', $(this).data('code'));
        });

        $(document).on('change', '.status-toggle', function () {
            const checkbox = $(this);
            const code     = checkbox.data('code');
            const status   = checkbox.data('status');

            checkbox.prop('disabled', true);

            $.ajax({
                url:      routes.status,
                type:     'POST',
                dataType: 'json',
                data:     { code_data: code, status: status },
                success: function (res) {
                    loadData(state.page);
                    SystemToast('success', res.note || 'Status berhasil diperbarui');
                },
                error: function (xhr) {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                    SystemToast('danger', xhr.responseJSON?.note || 'Status gagal diperbarui');
                },
                complete: function () {
                    checkbox.prop('disabled', false);
                }
            });
        });

        $(document).on('click', '.delete-data:not(.disabled)', function () {
            const code = $(this).data('code');
            const name = $(this).data('name');
            const modal = $('div[data-model="confirmasi"]');

            $('div[data-model="confirmasi"]').modal({ backdrop: false });
            $('div[data-model="confirmasi"] .modal-body').html(
                `<div class="alert alert-danger">Anda yakin ingin menghapus data <b>${name}</b>?</div>`
            );
            $('button[btn-action="action-confirmasi"]').remove();
            $('button[btn-action="close-confirmasi"]').before(
                `<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>`
            );

            $(document)
                .off('click', '[btn-action="action-confirmasi"]')
                .on('click', '[btn-action="action-confirmasi"]', function () {
                    const btn = $(this);
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menghapus...');

                    $.ajax({
                        url:  routes.delete,
                        type: 'DELETE',
                        data: { code_data: code },
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

        /* -- Blocker supaya item disabled tidak bisa diklik -- */         
        $(document).on('click', '.dropdown-item.disabled', function (e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });

        showPanel('list');
        loadData(1);

        function formatTanggalIndonesia(dateString) {
            if (!dateString) return '-';

            const date = new Date(dateString);

            const tanggal = date.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                timeZone: 'Asia/Jakarta'
            });

            const waktu = date.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
                timeZone: 'Asia/Jakarta'
            });

            return `${tanggal} - ${waktu}`;
        }

        
        function exportdatatable(type) {
            // tampilkan loading
            SystemToast('info', 'Sedang menyiapkan file export...');

            // hapus cookie lama
            document.cookie = "downloadFinished=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

            // buat iframe hidden untuk download
            let iframe = $('#downloadFrame');
            if (!iframe.length) {
                $('body').append('<iframe id="downloadFrame" style="display:none;"></iframe>');
                iframe = $('#downloadFrame');
            }

            iframe.attr('src', `/admin/export/${type}`);

            // cek apakah download selesai
            const checkDownload = setInterval(function () {
                if (document.cookie.indexOf('downloadFinished=true') !== -1) {
                    clearInterval(checkDownload);

                    SystemToast('success', 'Export selesai, file berhasil diunduh');

                    // hapus cookie
                    document.cookie = "downloadFinished=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                }
            }, 1000);
        }
    });
</script>
@endsection