@extends('admin.AdminOne.layout.assets')
@section('title', 'Data Berita')

@section('content')
    <div class="page_main">
        <div class="container-fluid text-left">
            <div class="row">

                {{-- HEADER --}}
                <div class="col-md-12 bg_page_main form_action" line="hd_action">
                    <div class="col-md-12 hd_page_main" id="pageTitle">Data Berita</div>
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
                    <div class="col-md-12 data_page" id="dataTableBody"></div>
                </div>

                {{-- PANEL: FORM (Tambah & Edit) --}}
                <div class="col-md-12 bg_page_main form_action" id="panel-form" style="display:none;" line="form_action">
                    <div class="col-md-12 data_page">
                        <div class="row bg_data_page form_page content">

                            {{-- Hidden: kode data untuk mode edit --}}
                            <input type="hidden" id="field_code_data" value="">

                            {{-- Alert --}}
                            <div class="col-md-12 bg_act_page_main page">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="form-alert" style="display:none;"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Kolom Kiri: Input --}}
                            <div class="col-md-9 bg_form_page">

                                <div class="col-md-12 bg_form_page">
                                    <div class="form_input text-left">
                                        <div class="tag_title">Judul Berita <span>*</span></div>
                                        <input type="text" id="field_judul_berita"
                                            placeholder="Judul berita.." value="" autofocus />
                                        <div class="invalid-feedback" id="err_judul_berita"></div>
                                    </div>
                                </div>

                                <div class="col-md-12 bg_form_page">
                                    <div class="form_input text-left">
                                        <div class="tag_title">Isi Berita <span>*</span></div>
                                        <textarea id="field_isi_berita" name="isi_berita"
                                            class="ckeditor" placeholder="Isi berita.."></textarea>
                                        <div class="invalid-feedback" id="err_isi_berita"></div>
                                    </div>
                                </div>

                                <div class="col-md-12 bg_form_page">
                                    <div class="form_input text-left">
                                        <div class="tag_title">Sumber Berita <span>*</span></div>
                                        <input type="text" id="field_sumber_berita"
                                            placeholder="Sumber berita.." value="" />
                                        <div class="invalid-feedback" id="err_sumber_berita"></div>
                                    </div>
                                </div>

                            </div>

                            {{-- Kolom Kanan: Foto --}}
                            <div class="col-md-3 bg_form_page">
                                <div class="col-md-12 bg_form_page">
                                    <div class="form_input full text-left">
                                        <div class="tag_title">Foto</div>
                                        <div class="modern-upload-card" id="dropzonePhoto">
                                            <input type="file" id="field_photo_berita"
                                                name="photo_berita" accept="image/*" hidden>
                                            <div class="upload-content">
                                                <div class="preview-wrapper">
                                                    <img src="{{ asset('/themes/admin/AdminOne/image/no_image.png') }}"
                                                        id="preview_photo_berita" class="preview-berita">
                                                    <button type="button" class="btn-change-photo" id="btnChoosePhoto">
                                                        <i class="fa fa-camera"></i>
                                                    </button>
                                                </div>
                                                <div class="upload-info">
                                                    <h5>Upload Foto</h5>
                                                    <p>Drag &amp; drop foto disini atau klik tombol kamera</p>
                                                    <small id="photo_filename">Belum ada file dipilih</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback d-block text-center mt-2" id="err_photo_berita"></div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Tombol Aksi --}}
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
                            <table class="table_view table-striped table-hover">
                                <tbody>
                                    <tr><th width="160" class="bg-light">Kode Data</th>   <td id="view_code_data">-</td></tr>
                                    <tr><th class="bg-light">Foto</th>                    <td id="view_foto">-</td></tr>
                                    <tr><th class="bg-light">Judul</th>                   <td id="view_judul">-</td></tr>
                                    <tr><th class="bg-light">Isi</th>                     <td id="view_isi">-</td></tr>
                                    <tr><th class="bg-light">Sumber</th>                  <td id="view_sumber">-</td></tr>
                                    <tr><th class="bg-light">Status Data</th>             <td id="view_status">-</td></tr>
                                    <tr><th class="bg-light">Dibuat Pada</th>             <td id="view_created_at">-</td></tr>
                                    <tr><th class="bg-light">Diperbarui Pada</th>         <td id="view_updated_at">-</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group form_input text-left">
                                    @if(($level_user['editberita'] ?? 'No') === 'Yes')
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

    {{-- Inline style untuk validasi --}}
    <style>
        /* Field invalid: border merah */
        input.is-invalid,
        textarea.is-invalid,
        select.is-invalid {
            border: 1.5px solid #dc3545 !important;
            box-shadow: 0 0 0 2px rgba(220, 53, 69, .15) !important;
            border-radius: 4px;
        }

        /* Upload card invalid */
        .modern-upload-card.is-invalid {
            border: 1.5px solid #dc3545 !important;
            box-shadow: 0 0 0 2px rgba(220, 53, 69, .15) !important;
        }

        /* Pesan error */
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
        }

        .invalid-feedback:empty {
            display: none;
        }
    </style>
@endsection

@section('script')
    <script>
        $(function () {

            /* =====================================================================
            * CONSTANTS
            * ===================================================================== */
            const ROUTES = {
                list   : "{{ url('/admin/datalistberita') }}",
                store  : "{{ url('/admin/saveberita') }}",
                detail : "{{ url('/admin/viewberita') }}",
                update : "{{ url('/admin/updateberita') }}",
                status : "{{ url('/admin/statusberita') }}",
                delete : "{{ url('/admin/deleteberita') }}"
            };

            const ACTION = {
                new    : {{ (($level_user['newberita']    ?? 'No') === 'Yes') ? 'true' : 'false' }},
                edit   : {{ (($level_user['editberita']   ?? 'No') === 'Yes') ? 'true' : 'false' }},
                delete : {{ (($level_user['deleteberita'] ?? 'No') === 'Yes') ? 'true' : 'false' }},
                export : {{ (($level_user['exportberita'] ?? 'No') === 'Yes') ? 'true' : 'false' }}
            };

            const NO_IMAGE = "{{ asset('/themes/admin/AdminOne/image/no_image.png') }}";

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

            /* =====================================================================
            * STATE
            * ===================================================================== */
            const state = {
                page       : 1,
                search     : '',
                perPage    : parseInt($('#countvdajax').val()) || 10,
                sortBy     : 'created_at',
                sortOrder  : 'asc',
                timeout    : null,
                mode       : 'list',   // list | add | edit | view
                currentCode: null
            };

            /* =====================================================================
            * HELPERS
            * ===================================================================== */
            function getPhotoUrl(filename) {
                return filename
                    ? '/themes/admin/AdminOne/image/upload/' + filename
                    : NO_IMAGE;
            }

            function formatTanggal(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                const tgl  = date.toLocaleDateString('id-ID', {
                    weekday : 'long', day: 'numeric',
                    month   : 'long', year: 'numeric',
                    timeZone: 'Asia/Jakarta'
                });
                const wkt  = date.toLocaleTimeString('id-ID', {
                    hour: '2-digit', minute: '2-digit', second: '2-digit',
                    hour12: false, timeZone: 'Asia/Jakarta'
                });
                return tgl + ' - ' + wkt;
            }

            /* =====================================================================
            * FORM UTILITIES
            * ===================================================================== */
            function resetForm() {
                $('#panel-form')
                    .find('input[type="text"], input[type="email"], input[type="date"], textarea')
                    .val('');
                $('#panel-form').find('select').val('').trigger('change');
                $('#field_photo_berita').val('');
                $('#field_code_data').val('');
                $('#preview_photo_berita').attr('src', NO_IMAGE);
                $('#photo_filename').text('Belum ada file dipilih');
                // Reset CKEditor jika sudah siap
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.field_isi_berita) {
                    CKEDITOR.instances.field_isi_berita.setData('');
                }
            }

            function clearErrors() {
                $('#panel-form').find('input, textarea, select').removeClass('is-invalid');
                $('#dropzonePhoto').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            /**
             * Tampilkan error pada field tertentu.
             * @param {string} fieldId  – id tanpa prefix "field_", atau "photo_berita"
             * @param {string} message
             */
            function setFieldError(fieldId, message) {
                const $field = $('#field_' + fieldId);
                if ($field.length) $field.addClass('is-invalid');
                if (fieldId === 'photo_berita') $('#dropzonePhoto').addClass('is-invalid');
                $('#err_' + fieldId).text(message);
            }

            /* =====================================================================
            * PHOTO DROPZONE
            * ===================================================================== */
            (function initDropzone() {
                const $dropzone = $('#dropzonePhoto');
                const $input    = $('#field_photo_berita');

                $('#btnChoosePhoto').on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $input.trigger('click');
                });

                $dropzone.on('click', function () { $input.trigger('click'); });
                $input.on('click', function (e) { e.stopPropagation(); });

                $dropzone.on('dragover', function (e) {
                    e.preventDefault();
                    $dropzone.addClass('dragover');
                }).on('dragleave', function (e) {
                    e.preventDefault();
                    $dropzone.removeClass('dragover');
                }).on('drop', function (e) {
                    e.preventDefault();
                    $dropzone.removeClass('dragover');
                    const files = e.originalEvent.dataTransfer.files;
                    if (files.length) {
                        $input[0].files = files;
                        previewImage(files[0]);
                    }
                });

                $input.on('change', function () {
                    if (this.files && this.files[0]) previewImage(this.files[0]);
                });

                function previewImage(file) {
                    const ALLOWED = ['image/jpeg', 'image/jpg', 'image/png'];
                    const MAX_MB  = 2;

                    if (file.size > MAX_MB * 1024 * 1024) {
                        setFieldError('photo_berita', 'Ukuran foto maksimal ' + MAX_MB + 'MB');
                        $input.val('');
                        return;
                    }
                    if (!ALLOWED.includes(file.type)) {
                        setFieldError('photo_berita', 'Format harus JPG, JPEG, atau PNG');
                        $input.val('');
                        return;
                    }

                    // Hapus error jika valid
                    $('#err_photo_berita').text('');
                    $('#dropzonePhoto').removeClass('is-invalid');
                    $('#photo_filename').text(file.name);

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('#preview_photo_berita').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            })();

            /* =====================================================================
            * PANEL / NAVIGATION
            * ===================================================================== */
            function showPanel(mode, code) {
                state.mode        = mode;
                state.currentCode = code || null;

                $('#panel-list, #panel-form, #panel-view').hide();
                $('#form-alert').hide();
                clearErrors();
                renderHeader();

                switch (mode) {
                    case 'list':
                        $('#panel-list').show();
                        break;

                    case 'add':
                        resetForm();
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

            function renderHeader() {
                const titles = {
                    list : 'Data Berita',
                    add  : 'Tambah Data Berita',
                    edit : 'Ubah Data Berita',
                    view : 'Detail Data Berita'
                };

                $('#pageTitle').text(titles[state.mode] || 'Data Berita');

                let buttons = '<button type="button" class="btn btn-secondary" onclick="BackPage()">'
                            + '<i class="fa fa-chevron-left"></i> Kembali</button> ';

                if (state.mode === 'list') {
                    if (ACTION.new) {
                        buttons += '<button type="button" class="btn btn-primary" id="btnTambah">'
                                + '<i class="fa fa-plus"></i> Tambah Data</button> ';
                    }
                    if (ACTION.export) {
                        buttons += '<button type="button" class="btn btn-info" '
                                + 'onclick="exportdata({url:\'/admin/exportberita\',btn:this})">'
                                + '<i class="fa fa-download"></i> Export Data</button>';
                    }
                }

                $('#headerActions').html(buttons);

                if (state.mode === 'list') {
                    $('#btnTambah').on('click', function () { showPanel('add'); });
                }
            }

            /* =====================================================================
            * LOAD DETAIL (VIEW & EDIT)
            * ===================================================================== */
            function loadDetail(code, target) {
                const loading = `<i class="fa fa-spinner fa-spin"></i> Memuat...`;

                if (target === 'view') {
                    $('#view_code_data, #view_judul, #view_isi, #view_sumber, #view_foto, #view_status, #view_created_at, #view_updated_at').html(loading);
                }
                $.get(ROUTES.detail, { code_data: code })
                    .done(function (res) {
                        const d = res.data;

                        if (target === 'view') {
                            $('#view_code_data').text(d.code_data   || '-');
                            $('#view_judul').text(d.judul_berita    || '-');
                            $('#view_isi').html(d.isi_berita        || '-');
                            $('#view_sumber').text(d.sumber_berita  || '-');
                            $('#view_status').text(d.status_data    || '-');
                            $('#view_foto').html(
                                '<img src="' + getPhotoUrl(d.tumb_berita) + '" '
                                + 'style="width:150px;height:150px;object-fit:contain;border-radius:8px;background:#f5f5f5;">'
                            );
                            $('#view_created_at').text(formatTanggal(d.created_at));
                            $('#view_updated_at').text(formatTanggal(d.updated_at));
                            $('#btnEditFromView').data('code', d.code_data);
                        }

                        if (target === 'edit') {
                            $('#field_code_data').val(d.code_data      || '');
                            $('#field_judul_berita').val(d.judul_berita  || '');
                            $('#field_sumber_berita').val(d.sumber_berita || '');

                            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.field_isi_berita) {
                                CKEDITOR.instances.field_isi_berita.setData(d.isi_berita || '');
                            } else {
                                $('#field_isi_berita').val(d.isi_berita || '');
                            }

                            $('#preview_photo_berita').attr('src', getPhotoUrl(d.tumb_berita));
                            $('#photo_filename').text(d.tumb_berita || 'Belum ada file dipilih');
                        }
                    })
                    .fail(function () {
                        SystemToast('danger', 'Gagal memuat detail data');
                    });
            }

            /* =====================================================================
            * SIMPAN FORM
            * ===================================================================== */
            $('#btnSaveForm').on('click', function () {
                clearErrors();

                const isEdit = state.mode === 'edit';

                // Ambil nilai — CKEditor harus diambil lewat API-nya
                const judulBerita = $('#field_judul_berita').val().trim();
                const isiBerita   = (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.field_isi_berita)
                                    ? CKEDITOR.instances.field_isi_berita.getData().trim()
                                    : $('#field_isi_berita').val().trim();
                const sumberBerita = $('#field_sumber_berita').val().trim();
                const photo        = $('#field_photo_berita')[0].files[0];

                // --- VALIDASI LOKAL ---
                let valid = true;

                if (!judulBerita) {
                    setFieldError('judul_berita', 'Judul berita wajib diisi');
                    valid = false;
                }

                if (!isiBerita) {
                    setFieldError('isi_berita', 'Isi berita wajib diisi');
                    // Tandai wrapper CKEditor jika ada
                    if (typeof CKEDITOR !== 'undefined') {
                        const ckFrame = $('#cke_field_isi_berita');
                        if (ckFrame.length) ckFrame.css('border', '1.5px solid #dc3545');
                    }
                    valid = false;
                }

                if (!sumberBerita) {
                    setFieldError('sumber_berita', 'Sumber berita wajib diisi');
                    valid = false;
                }

                if (!photo && !isEdit) {
                    setFieldError('photo_berita', 'Foto wajib diupload');
                    valid = false;
                }

                if (!valid) return;

                // --- BUILD FormData ---
                const formData = new FormData();
                formData.append('judul_berita',  judulBerita);
                formData.append('isi_berita',    isiBerita);
                formData.append('sumber_berita', sumberBerita);

                if (photo) formData.append('photo_berita', photo);

                if (isEdit) {
                    formData.append('code_data', $('#field_code_data').val());
                    formData.append('_method',   'PUT');
                }

                // --- KIRIM ---
                const $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

                $.ajax({
                    url         : isEdit ? ROUTES.update : ROUTES.store,
                    type        : 'POST',
                    dataType    : 'json',
                    data        : formData,
                    processData : false,
                    contentType : false,

                    success: function (res) {
                        SystemToast('success', res.note || 'Data berhasil disimpan');
                        showPanel('list');
                        loadData(1);
                    },

                    error: function (xhr) {
                        const errors = (xhr.responseJSON && xhr.responseJSON.errors) ? xhr.responseJSON.errors : {};

                        $.each(errors, function (key, messages) {
                            setFieldError(key, messages[0]);
                        });

                        SystemToast('danger',
                            (xhr.responseJSON && (xhr.responseJSON.note || xhr.responseJSON.message))
                            || 'Gagal menyimpan data'
                        );
                    },

                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
                    }
                });
            });

            // Reset CKEditor border merah saat user mulai mengetik
            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.on('instanceReady', function (evt) {
                    evt.editor.on('change', function () {
                        $('#cke_field_isi_berita').css('border', '');
                        $('#err_isi_berita').text('');
                    });
                });
            }

            /* =====================================================================
            * TOMBOL NAVIGASI PANEL
            * ===================================================================== */
            $('#btnCancelForm').on('click',   function () { showPanel('list'); });
            $('#btnBackFromView').on('click', function () { showPanel('list'); });
            $('#btnEditFromView').on('click', function () {
                showPanel('edit', $(this).data('code'));
            });

            /* =====================================================================
            * LOAD DATA (TABEL LIST)
            * ===================================================================== */
            function loadData(page) {
                state.page = page || 1;

                $.ajax({
                    url  : ROUTES.list,
                    type : 'GET',
                    data : {
                        page       : state.page,
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

            function renderLoading() {
                $('#dataTableBody').html(
                    '<div class="list_notif read not">'
                    + '<div class="head text-center"><i class="fa fa-spinner fa-spin"></i> Memuat data...</div>'
                    + '</div>'
                );
            }

            function renderError() {
                $('#dataTableBody').html(
                    '<div class="list_notif read not">'
                    + '<div class="head text-center">Gagal memuat data</div>'
                    + '</div>'
                );
            }

            function renderTable(res) {
                if (!res.data || !res.data.length) {
                    $('#dataTableBody').html(
                        '<div class="list_notif read not">'
                        + '<div class="head text-center">Tidak ada data</div>'
                        + '</div>'
                    );
                    return;
                }

                const rows = res.data.map(function (item) {
                    const editCls    = ACTION.edit   ? '' : 'disabled text-muted';
                    const deleteCls  = ACTION.delete ? '' : 'disabled text-muted';
                    const isActive   = item.status_data === 'Aktif';
                    const checked    = isActive ? 'checked' : '';
                    const nextStatus = isActive ? 'Tidak Aktif' : 'Aktif';
                    const cbId       = 'ios_' + item.code_data;

                    return '<div class="row bg_data_page">'
                        + '<div class="col-md-12 bg_list_post">'
                        + '<div class="image">'
                        + '<img src="' + getPhotoUrl(item.tumb_berita) + '" alt="Berita">'
                        + '</div>'
                        + '<div class="det_post">'
                        + '<div class="title_list">' + (item.judul_berita || '-') + '</div>'
                        + '<div class="date_list"><i class="fa fa-clock-o"></i> ' + formatTanggal(item.created_at) + '</div>'
                        + '<div class="btn_set">'
                        + '<div class="checkboxlios">'
                        + '<input type="checkbox" class="ios status-toggle" id="' + cbId + '" ' + checked
                        + ' data-code="' + item.code_data + '" data-status="' + nextStatus + '">'
                        + '<label for="' + cbId + '"></label>'
                        + '</div>'
                        + '<div class="dropdown dropright">'
                        + '<button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">Atur</button>'
                        + '<div class="dropdown-menu">'
                        + '<h5 class="dropdown-header">Pengaturan Data</h5>'
                        + '<a class="dropdown-item btn-view" data-code="' + item.code_data + '">Lihat Data</a>'
                        + '<a class="dropdown-item btn-edit ' + editCls + '" data-code="' + item.code_data + '">Ubah Data</a>'
                        + '<a class="dropdown-item delete-data ' + deleteCls + '"'
                        + ' data-code="' + item.code_data + '" data-name="' + (item.judul_berita || '') + '">Hapus Data</a>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
                        + '</div>';
                });

                $('#dataTableBody').html(rows.join(''));

                // Init plugin iosCheckbox setelah elemen masuk DOM
                if (typeof $.fn.iosCheckbox === 'function') {
                    $('#dataTableBody .ios').iosCheckbox();
                }
            }

            /* =====================================================================
            * PAGINATION
            * ===================================================================== */
            function renderPagination(res) {
                const prevPage = res.current_page > 1             ? res.current_page - 1 : null;
                const nextPage = res.current_page < res.last_page ? res.current_page + 1 : null;

                $('#totalData').text(res.total);
                $('#currentPageText').text(res.current_page);
                $('#prevPageText').text(prevPage || '-');
                $('#nextPageText').text(nextPage || '-');

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

            /* =====================================================================
            * DELEGATED EVENTS
            * ===================================================================== */

            // Pencarian
            $('#searchInput').hide();
            $('#closeSearch').hide();

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

            // Tombol paginasi
            $(document).on('click', '#btnFirst, #btnPrevPage, #btnNextPage, #btnLast', function (e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).prop('disabled')) return false;
                loadData(page);
            });

            // Sortir kolom
            $(document).on('click', '.sortable', function () {
                const sort = $(this).data('sort');
                state.sortOrder = (state.sortBy === sort && state.sortOrder === 'asc') ? 'desc' : 'asc';
                state.sortBy    = sort;

                $('.sortable i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
                $(this).find('i')
                    .removeClass('fa-sort')
                    .addClass(state.sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down');

                loadData(1);
            });

            // View / Edit dari list
            $(document).on('click', '.btn-view', function () { showPanel('view', $(this).data('code')); });
            $(document).on('click', '.btn-edit:not(.disabled)', function () { showPanel('edit', $(this).data('code')); });

            // Toggle status
            $(document).on('change', '.status-toggle', function () {
                const $chk  = $(this);
                const code  = $chk.data('code');
                const status = $chk.data('status');

                $chk.prop('disabled', true);

                $.ajax({
                    url      : ROUTES.status,
                    type     : 'POST',
                    dataType : 'json',
                    data     : { code_data: code, status: status },
                    success: function (res) {
                        loadData(state.page);
                        SystemToast('success', res.note || 'Status berhasil diperbarui');
                    },
                    error: function (xhr) {
                        $chk.prop('checked', !$chk.prop('checked'));
                        SystemToast('danger', (xhr.responseJSON && xhr.responseJSON.note) || 'Status gagal diperbarui');
                    },
                    complete: function () {
                        $chk.prop('disabled', false);
                    }
                });
            });

            // Hapus data
            $(document).on('click', '.delete-data:not(.disabled)', function () {
                const code  = $(this).data('code');
                const name  = $(this).data('name');
                const $modal = $('div[data-model="confirmasi"]');

                $modal.modal({ backdrop: false });
                $modal.find('.modal-body').html(
                    '<div class="alert alert-danger">Anda yakin ingin menghapus data <b>' + name + '</b>?</div>'
                );

                $('button[btn-action="action-confirmasi"]').remove();
                $('button[btn-action="close-confirmasi"]').before(
                    '<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>'
                );

                $(document)
                    .off('click', '[btn-action="action-confirmasi"]')
                    .on('click',  '[btn-action="action-confirmasi"]', function () {
                        const $btn = $(this);
                        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menghapus...');

                        $.ajax({
                            url  : ROUTES.delete,
                            type : 'DELETE',
                            data : { code_data: code },
                            success: function (res) {
                                $modal.modal('hide');
                                loadData(state.page);
                                SystemToast('success', res.note || 'Data berhasil dihapus');
                            },
                            error: function (xhr) {
                                SystemToast('danger', (xhr.responseJSON && xhr.responseJSON.note) || 'Data gagal dihapus');
                            },
                            complete: function () {
                                $btn.remove();
                            }
                        });
                    });
            });

            // Cegah klik item disabled
            $(document).on('click', '.dropdown-item.disabled', function (e) {
                e.preventDefault();
                e.stopPropagation();
            });

            /* =====================================================================
            * INIT
            * ===================================================================== */
            showPanel('list');
            loadData(1);

        });
    </script>
@endsection