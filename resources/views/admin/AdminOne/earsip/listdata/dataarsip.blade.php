@extends('admin.AdminOne.layout.assets')
@section('title', 'Data Arsip')

<!-- @section('style')
    <style>
        /* ── Dropzone ─────────────────────────────────────────────── */
        .modern-upload-card {
            border: 2px dashed #d9dee8;
            border-radius: 16px;
            padding: 25px;
            background: #fafbfd;
            transition: border-color .25s, background .25s, transform .2s;
            cursor: pointer;
        }
        .modern-upload-card:hover  { border-color: #4f7cff; }
        .modern-upload-card.dragover {
            border-color: #4f7cff;
            background: #eef4ff;
            transform: scale(1.01);
        }
        .upload-placeholder {
            text-align: center;
            padding: 10px 0;
        }
        .upload-placeholder > i {
            color: #4f7cff;
            margin-bottom: 10px;
            display: block;
        }

        /* ── File card (panel view) ───────────────────────────────── */
        .file-card {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 10px 14px;
            max-width: 460px;
            width: 100%;
        }
        .file-card .file-icon {
            width: 48px; height: 48px; min-width: 48px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e0e4f0;
            display: flex; align-items: center; justify-content: center;
        }
        .file-card .file-icon i { font-size: 22px; }
        .file-card .file-info {
            flex: 1;
            min-width: 0;
        }
        .file-card .file-name {
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .file-card .file-meta {
            color: #6b7280;
            font-size: 12px;
            margin-top: 2px;
        }
        .file-card .file-action { flex-shrink: 0; }
    </style>
@endsection -->

@section('content')
    <div class="page_main">
        <div class="container-fluid text-left">
            <div class="row">

                {{-- HEADER --}}
                <div class="col-md-12 bg_page_main hd" line="hd_action">
                    <div class="col-md-12 hd_page_main" id="pageTitle">Data Arsip</div>
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
                                            <th class="sortable" style="min-width:120px;" data-sort="code_data">Kode Data <i class="fa fa-sort"></i></th>
                                            <th class="sortable" style="min-width:120px;" data-sort="code_kategori">Kategori <i class="fa fa-sort"></i></th>
                                            <th class="sortable" style="min-width:300px;" data-sort="judul">Judul <i class="fa fa-sort"></i></th>
                                            <th class="sortable" style="min-width:130px;" data-sort="tanggal_dokumen">Tanggal Dokumen <i class="fa fa-sort"></i></th>
                                            <th style="min-width:200px;">Deskripsi</th>
                                            <th class="sortable" style="min-width:100px;" data-sort="akses">Akses <i class="fa fa-sort"></i></th>
                                            <th style="min-width:150px;">File</th>
                                            <th class="colright" style="min-width:100px; text-align:center;"><i class="head fa fa-cog"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="DataTableBody">
                                        <tr>
                                            <td colspan="9" class="text-center p-4">
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

                            {{-- Kode Data --}}
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Kode Data</label>
                                    <div class="col-sm-10 input">
                                        <input type="text" id="field_code_data" placeholder="Auto-generate jika kosong" readonly>
                                    </div>
                                </div>
                            </div>

                            {{-- Kategori --}}
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Kategori <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <div class="field-wrapper" id="wrap_codekategori">
                                            <select id="field_codekategori" style="width:100%;"></select>
                                        </div>
                                        <div class="field-error-msg" id="err_codekategori" style="display:none;">
                                            <i class="fa fa-info-circle"></i> <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Judul --}}
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Judul <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <div class="field-wrapper" id="wrap_judul">
                                            <input type="text" id="field_judul" placeholder="Masukkan judul...">
                                        </div>
                                        <div class="field-error-msg" id="err_judul" style="display:none;">
                                            <i class="fa fa-info-circle"></i> <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tanggal Dokumen --}}
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Tanggal Dokumen <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <div class="field-wrapper" id="wrap_tanggaldokumen">
                                            <input type="date" id="field_tanggaldokumen">
                                        </div>
                                        <div class="field-error-msg" id="err_tanggaldokumen" style="display:none;">
                                            <i class="fa fa-info-circle"></i> <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Deskripsi <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <div class="field-wrapper" id="wrap_deskripsi">
                                            <textarea id="field_deskripsi" placeholder="Masukkan deskripsi..."></textarea>
                                        </div>
                                        <div class="field-error-msg" id="err_deskripsi" style="display:none;">
                                            <i class="fa fa-info-circle"></i> <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Akses --}}
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Akses <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <div class="field-wrapper" id="wrap_akses">
                                            <select id="field_akses">
                                                <option value="" style="display:none;">Pilih Akses</option>
                                                <option value="publik">Publik</option>
                                                <option value="internal">Internal</option>
                                                <option value="rahasia">Rahasia</option>
                                            </select>
                                        </div>
                                        <div class="field-error-msg" id="err_akses" style="display:none;">
                                            <i class="fa fa-info-circle"></i> <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- File Arsip --}}
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">File Arsip <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <div class="field-wrapper" id="wrap_filepath">
                                            <div class="modern-upload-card" id="dropzonePhoto">

                                                <input type="file" id="field_filepath" name="file_path"
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png" hidden>

                                                {{-- Placeholder --}}
                                                <div id="uploadPlaceholder" class="upload-placeholder">
                                                    <i class="fa fa-cloud-upload-alt fa-3x"></i>
                                                    <h5 style="margin-left:12px; margin-bottom:4px;">Upload File Arsip</h5>
                                                    <p style="margin-left:12px; color:#6b7280; font-size:13px; margin-bottom:14px;">
                                                        Drag &amp; Drop file atau klik tombol di bawah
                                                    </p>
                                                    <button style="margin-left:12px;" type="button" class="btn btn-primary" id="btnChooseFile">
                                                        <i class="fa fa-upload"></i> Pilih File
                                                    </button>
                                                </div>

                                                {{-- Preview Card --}}
                                                <div id="filePreviewCard" style="display:none;">
                                                    <div style="display:flex; align-items:center; gap:14px;">
                                                        <div id="previewIconWrap"
                                                            style="margin-left:12px; margin-top:12px; width:56px; height:56px; min-width:56px;
                                                                    border-radius:12px; background:#f0f2f8;
                                                                    border:1px solid #e0e4f0;
                                                                    display:flex; justify-content:center; align-items:center;">
                                                            <i id="previewIcon" class="fa fa-file-alt"
                                                            style="font-size:26px; color:#4f7cff;"></i>
                                                        </div>
                                                        <div style="flex:1; min-width:0;">
                                                            <div id="previewName"
                                                                style="font-weight:600; font-size:14px;
                                                                        white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">-</div>
                                                            <div style="color:#6b7280; font-size:12px; margin-top:3px;">
                                                                <span id="previewSize">-</span>
                                                            </div>
                                                            <div class="progress mt-2" style="height:6px; display:none;" id="uploadProgressWrap">
                                                                <div class="progress-bar" id="uploadProgressBar"
                                                                    role="progressbar" style="width:0%;"></div>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <button type="button" class="btn btn-danger btn-sm" id="btnRemoveFile"
                                                                    style="margin-right:12px;width:34px; height:34px; padding:0;
                                                                        display:flex; align-items:center; justify-content:center;">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="field-error-msg" id="err_filepath" style="display:none;">
                                            <i class="fa fa-info-circle"></i> <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Tombol Simpan / Batal --}}
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
                                    <tr><th width="160" class="bg-light">Kode Data</th><td id="view_code_data">-</td></tr>
                                    <tr><th class="bg-light">Kategori</th><td id="view_codekategori">-</td></tr>
                                    <tr><th class="bg-light">Judul</th><td id="view_judul">-</td></tr>
                                    <tr><th class="bg-light">Tanggal Dokumen</th><td id="view_tanggaldokumen">-</td></tr>
                                    <tr><th class="bg-light">Deskripsi</th><td id="view_deskripsi">-</td></tr>
                                    <tr><th class="bg-light">Akses</th><td id="view_akses">-</td></tr>
                                    <tr><th class="bg-light">File</th><td id="view_filepath">-</td></tr>
                                    <tr><th class="bg-light">Dibuat Pada</th><td id="view_created_at">-</td></tr>
                                    <tr><th class="bg-light">Diperbarui Pada</th><td id="view_updated_at">-</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group form_input text-left">
                                    @if(($level_user['editarsip'] ?? 'No') === 'Yes')
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

            /* =============================================================
            * CONSTANTS
            * ============================================================= */
            const routes = {
                list     : "{{ url('/admin/datalistarsip') }}",
                store    : "{{ url('/admin/savearsip') }}",
                detail   : "{{ url('/admin/viewarsip') }}",
                update   : "{{ url('/admin/updatearsip') }}",
                delete   : "{{ url('/admin/deletearsip') }}",
                kategori : "{{ url('/admin/listopkategoriarsip') }}"
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

            /* =============================================================
            * HELPER — file utilities
            * ============================================================= */
            const FILE_ICON_MAP = {
                pdf  : { icon: 'fa-file-pdf-o',         color: '#dc3545' },
                doc  : { icon: 'fa-file-word-o',        color: '#0d6efd' },
                docx : { icon: 'fa-file-word-o',        color: '#0d6efd' },
                xls  : { icon: 'fa-file-excel-o',       color: '#198754' },
                xlsx : { icon: 'fa-file-excel-o',       color: '#198754' },
                ppt  : { icon: 'fa-file-powerpoint-o',  color: '#fd7e14' },
                pptx : { icon: 'fa-file-powerpoint-o',  color: '#fd7e14' },
                jpg  : { icon: 'fa-file-image-o',       color: '#6f42c1' },
                jpeg : { icon: 'fa-file-image-o',       color: '#6f42c1' },
                png  : { icon: 'fa-file-image-o',       color: '#6f42c1' },
                zip  : { icon: 'fa-file-archive-o',     color: '#6c757d' },
                rar  : { icon: 'fa-file-archive-o',     color: '#6c757d' },
                txt  : { icon: 'fa-file-text-o',        color: '#6c757d' }
            };

            function getFilename(filePath) {
                return (filePath || '').split(/[\/\\]/).pop();
            }

            function getFileIconColor(filePath) {
                const ext = getFilename(filePath).split('.').pop().toLowerCase();
                return FILE_ICON_MAP[ext] || { icon: 'fa-file-alt', color: '#4f7cff' };
            }

            function shortFilename(filename, maxLen) {
                maxLen = maxLen || 6;
                const dotIdx = filename.lastIndexOf('.');
                if (dotIdx === -1) return filename;
                const name = filename.substring(0, dotIdx);
                const ext  = filename.substring(dotIdx + 1);
                return name.length <= maxLen ? filename : name.substring(0, maxLen) + '….' + ext;
            }

            function formatBytes(bytes) {
                if (bytes < 1024)        return bytes + ' B';
                if (bytes < 1048576)     return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / 1048576).toFixed(2) + ' MB';
            }

            function formatTanggal(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;
                const opts = { weekday:'long', day:'numeric', month:'long', year:'numeric', timeZone:'Asia/Jakarta' };
                const tOpts = { hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:false, timeZone:'Asia/Jakarta' };
                return date.toLocaleDateString('id-ID', opts) + ' — ' + date.toLocaleTimeString('id-ID', tOpts);
            }

            /* =============================================================
            * FILE PREVIEW
            * ============================================================= */
            let savedFileOnEdit = false;

            function previewFile(file) {
                const { icon, color } = getFileIconColor(file.name);
                savedFileOnEdit = false;
                $('#previewIcon').attr('class', 'fa ' + icon).css('color', color);
                $('#previewName').text(file.name);
                $('#previewSize').text(formatBytes(file.size));
                $('#uploadProgressWrap').hide();
                $('#uploadProgressBar').css('width', '0%').text('');
                $('#uploadPlaceholder').hide();
                $('#filePreviewCard').fadeIn(150);
                $('#dropzonePhoto').css('border', '');     // hapus error border jika ada
            }

            function previewSavedFile(filePath) {
                const filename        = getFilename(filePath);
                const { icon, color } = getFileIconColor(filename);
                savedFileOnEdit = true;
                $('#previewIcon').attr('class', 'fa ' + icon).css('color', color);
                $('#previewName').text(filename);
                $('#previewSize').text('File tersimpan di server');
                $('#uploadProgressWrap').hide();
                $('#uploadPlaceholder').hide();
                $('#filePreviewCard').show();
                $('#dropzonePhoto').css('border', '');
            }

            function resetFilePreview() {
                savedFileOnEdit = false;
                $('#field_filepath').val('');
                $('#previewIcon').attr('class', 'fa fa-file-alt').css('color', '#4f7cff');
                $('#previewName').text('-');
                $('#previewSize').text('-');
                $('#uploadProgressWrap').hide();
                $('#uploadProgressBar').css('width', '0%').text('');
                $('#filePreviewCard').hide();
                $('#uploadPlaceholder').show();
                $('#dropzonePhoto').css('border', '');
            }

            function hasFile() {
                const fileInput = $('#field_filepath')[0];
                return (fileInput.files && fileInput.files.length > 0) || savedFileOnEdit;
            }

            /* =============================================================
            * DROPZONE — events (bound once on init)
            * ============================================================= */
            const $dropzone  = $('#dropzonePhoto');
            const $inputFile = $('#field_filepath');

            $dropzone.on('click', function (e) {
                // Klik di tombol Choose/Remove ditangani sendiri — jangan bubblke ke dropzone
                if ($(e.target).closest('#btnChooseFile, #btnRemoveFile').length) return;
                $inputFile.trigger('click');
            });

            // Cegah native click pada input file memicu klik dropzone lagi
            $inputFile.on('click', function (e) { e.stopPropagation(); });

            $('#btnChooseFile').on('click', function (e) {
                e.stopPropagation();
                $inputFile.trigger('click');
            });

            $inputFile.on('change', function () {
                if (this.files && this.files[0]) previewFile(this.files[0]);
            });

            $dropzone
                .on('dragover', function (e) { e.preventDefault(); $(this).addClass('dragover'); })
                .on('dragleave', function (e) { e.preventDefault(); $(this).removeClass('dragover'); })
                .on('drop', function (e) {
                    e.preventDefault();
                    $(this).removeClass('dragover');
                    const files = e.originalEvent.dataTransfer.files;
                    if (!files || !files.length) return;
                    // Masukkan file ke input agar bisa diambil saat submit
                    try {
                        const dt = new DataTransfer();
                        dt.items.add(files[0]);
                        $inputFile[0].files = dt.files;
                    } catch (err) { /* DataTransfer API tidak tersedia (browser lama) */ }
                    previewFile(files[0]);
                });

            $('#btnRemoveFile').on('click', function (e) {
                e.stopPropagation();
                resetFilePreview();
            });

            /* =============================================================
            * SELECT2 — inisialisasi sekali
            * ============================================================= */
            $('#field_codekategori').select2({
                placeholder    : 'Pilih Kategori',
                allowClear     : true,
                width          : '100%',
                dropdownParent : $('#panel-form'),
                ajax: {
                    url      : routes.kategori,
                    dataType : 'json',
                    delay    : 250,
                    data: function (params) {
                        return {
                            search : params.term || '',
                            u      : "{{ session('id') }}",
                            token  : "{{ session('key_token') }}"
                        };
                    },
                    processResults: function (res) {
                        return {
                            results: $.map(res.results || [], function (item) {
                                return { id: item.code_data, text: item.nama_kategori };
                            })
                        };
                    },
                    cache: false
                }
            });

            /* =============================================================
            * loadKategori — set nilai terpilih di Select2 (mode edit)
            * ============================================================= */
            function loadKategori(selected) {
                if (!selected) {
                    $('#field_codekategori').val(null).trigger('change');
                    return;
                }

                // Jika option sudah ada, cukup set nilainya
                if ($('#field_codekategori option[value="' + selected + '"]').length) {
                    $('#field_codekategori').val(selected).trigger('change');
                    return;
                }

                // Fetch ke server untuk mendapat label kategori yang dipilih
                $.ajax({
                    url      : routes.kategori,
                    type     : 'GET',
                    dataType : 'json',
                    data     : { search: '', u: "{{ session('id') }}", token: "{{ session('key_token') }}" },
                    success  : function (res) {
                        const found = (res.results || []).find(function (item) {
                            return String(item.code_data) === String(selected);
                        });
                        if (found) {
                            $('#field_codekategori')
                                .append(new Option(found.nama_kategori, found.code_data, true, true))
                                .trigger('change');
                        }
                    }
                });
            }

            /* =============================================================
            * PANEL — tampil / sembunyikan
            * ============================================================= */
            function showPanel(mode, code) {
                state.mode        = mode;
                state.currentCode = code || null;

                $('#panel-list, #panel-form, #panel-view').hide();
                $('#form-alert').hide();
                clearFormErrors();
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
                let title   = 'Data Arsip';
                let buttons = '<button type="button" class="btn btn-secondary" onclick="BackPage()">'
                            + '<i class="fa fa-chevron-left"></i> Kembali</button> ';

                switch (state.mode) {
                    case 'list':
                        title = 'Data Arsip';
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
            * FORM HELPERS
            * ============================================================= */
            function resetForm() {
                $('#field_code_data').val('');
                $('#field_codekategori').val(null).trigger('change');
                $('#field_judul').val('');
                $('#field_tanggaldokumen').val('');
                $('#field_deskripsi').val('');
                $('#field_akses').val('');
                resetFilePreview();
                clearFormErrors();
            }

            function clearFormErrors() {
                $('.field-wrapper').removeClass('is-invalid');
                $('.field-wrapper input, .field-wrapper textarea, .field-wrapper select').css({
                    border: '', 'box-shadow': '', 'background-color': ''
                });
                $('#dropzonePhoto').css('border', '');
                $('.field-error-msg').hide().find('span').text('');
            }

            function setFieldError(wrapperId, errId, message) {
                $('#' + wrapperId)
                    .addClass('is-invalid')
                    .find('input, textarea, select')
                    .css({ border: '1px solid #e0294a', 'box-shadow': 'none', 'background-color': '#fff9f9' });

                if (wrapperId === 'wrap_filepath') {
                    $('#dropzonePhoto').css('border', '2px dashed #e0294a');
                }

                $('#' + errId)
                    .css('color', '#e0294a')
                    .find('i, span').css('color', '#e0294a').end()
                    .find('span').text(message).end()
                    .show();
            }

            /* =============================================================
            * LOAD DETAIL
            * ============================================================= */
            function loadDetail(code, target) {
                if (target === 'view') {
                    const loading = '<i class="fa fa-spinner fa-spin"></i> Memuat...';
                    $('#view_code_data, #view_codekategori, #view_judul, #view_tanggaldokumen,#view_deskripsi, #view_akses, #view_filepath, #view_created_at, #view_updated_at').html(loading);
                }

                $.ajax({
                    url      : routes.detail,
                    type     : 'GET',
                    data     : { code_data: code },
                    success  : function (res) {
                        const d = res.data || {};

                        if (target === 'view') {
                            $('#view_code_data').text(d.code_data                   || '-');
                            $('#view_codekategori').text(d.kategori.nama_kategori   || '-');
                            $('#view_judul').text(d.judul                           || '-');
                            $('#view_tanggaldokumen').text(d.tanggal_dokumen        || '-');
                            $('#view_deskripsi').text(d.deskripsi                   || '-');
                            $('#view_akses').text(d.akses                           || '-');
                            renderFileView(d.file_path, '#view_filepath');
                            $('#view_created_at').text(formatTanggal(d.created_at));
                            $('#view_updated_at').text(formatTanggal(d.updated_at));
                            $('#btnEditFromView').data('code', d.code_data);
                        }

                        if (target === 'edit') {
                            $('#field_code_data').val(d.code_data            || '');
                            $('#field_judul').val(d.judul                    || '');
                            $('#field_tanggaldokumen').val(d.tanggal_dokumen || '');
                            $('#field_deskripsi').val(d.deskripsi            || '');
                            $('#field_akses').val(d.akses                    || '');
                            loadKategori(d.code_kategori);
                            if (d.file_path) previewSavedFile(d.file_path);
                            else             resetFilePreview();
                        }
                    },
                    error: function () {
                        SystemToast('danger', 'Gagal memuat detail data');
                    }
                });
            }

            /* =============================================================
            * RENDER FILE — panel VIEW (card style)
            * ============================================================= */
            function renderFileView(filePath, selector) {
                if (!filePath) {
                    $(selector).html('<span class="text-muted">Tidak ada file</span>');
                    return;
                }

                const filename        = getFilename(filePath);
                const { icon, color } = getFileIconColor(filename);
                const href            = '/storage/' + filePath;

                $(selector).html(`
                    <div class="file-card">
                        <div class="file-icon">
                            <i class="fa ${icon}" style="color:${color};"></i>
                        </div>

                        <div class="file-info">
                            <div class="file-name" title="${filename}">${filename}</div>
                            <div class="file-meta">Dokumen Arsip</div>
                        </div>

                        <div class="file-action">
                            <a href="${href}" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fa fa-eye text-white" style="margin-right:6px;"></i> Lihat
                            </a>
                        </div>
                    </div>
                `);

                // $(selector).html(
                //     '<div class="file-card">' +
                //         '<div class="file-icon">' +
                //             '<i class="fa ' + icon + '" style="color:' + color + ';"></i>' +
                //         '</div>' +
                //         '<div class="file-info">' +
                //             '<div class="file-name" title="' + filename + '">' + filename + '</div>' +
                //             '<div class="file-meta">Dokumen Arsip</div>' +
                //         '</div>' +
                //         '<div class="file-action">' +
                //             '<a href="' + href + '" target="_blank" class="btn btn-primary btn-sm">' +
                //                 '<i class="fa fa-eye"></i> Lihat' +
                //             '</a>' +
                //         '</div>' +
                //     '</div>'
                // );
            }

            /* =============================================================
            * RENDER FILE — tabel LIST (icon + nama singkat)
            * ============================================================= */
            function renderFileList(filePath) {
                if (!filePath) {
                    return '<span class="badge badge-secondary">Tidak Ada</span>';
                }

                const filename        = getFilename(filePath);
                const { icon, color } = getFileIconColor(filename);
                const href            = '/storage/' + filePath;

                // return '<a href="' + href + '" target="_blank" ' +
                //     '   style="display:inline-flex; align-items:center; gap:6px; text-decoration:none;">' +
                //     '    <i class="fa ' + icon + '" style="color:' + color + ';"></i>' +
                //     '    <span class="table-badge">' + shortFilename(filename) + '</span>' +
                //     '</a>';

                return `
                <a href="${href}" target="_blank" style="display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
                    <i class="fa ${icon}" style="color:${color};"></i>
                    <div class="table-badge">${shortFilename(filename)}</div>
                </a>`;
            }

            /* =============================================================
            * SIMPAN
            * ============================================================= */
            $('#btnSaveForm').on('click', function () {
                clearFormErrors();

                const code_kategori   = $('#field_codekategori').val()      || '';
                const judul           = $('#field_judul').val().trim();
                const tanggal_dokumen = $('#field_tanggaldokumen').val().trim();
                const deskripsi       = $('#field_deskripsi').val().trim();
                const akses           = $('#field_akses').val().trim();
                let   hasError        = false;

                if (!code_kategori)   { setFieldError('wrap_codekategori',   'err_codekategori',   'Kategori wajib diisi.');           hasError = true; }
                if (!judul)           { setFieldError('wrap_judul',          'err_judul',          'Judul wajib diisi.');               hasError = true; }
                if (!tanggal_dokumen) { setFieldError('wrap_tanggaldokumen', 'err_tanggaldokumen', 'Tanggal dokumen wajib diisi.');     hasError = true; }
                if (!deskripsi)       { setFieldError('wrap_deskripsi',      'err_deskripsi',      'Deskripsi wajib diisi.');           hasError = true; }
                if (!akses)           { setFieldError('wrap_akses',          'err_akses',          'Akses wajib diisi.');               hasError = true; }
                if (!hasFile())       { setFieldError('wrap_filepath',       'err_filepath',       'File arsip wajib diunggah.');       hasError = true; }

                if (hasError) return;

                const isEdit   = (state.mode === 'edit');
                const formData = new FormData();

                formData.append('code_kategori',   code_kategori);
                formData.append('judul',           judul);
                formData.append('tanggal_dokumen', tanggal_dokumen);
                formData.append('deskripsi',       deskripsi);
                formData.append('akses',           akses);

                const newFile = $('#field_filepath')[0].files[0];
                if (newFile) {
                    formData.append('file_path', newFile);
                }

                if (isEdit) {
                    formData.append('code_data', state.currentCode);
                    if (!newFile) formData.append('keep_file', '1');
                }

                const $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

                $.ajax({
                    url         : isEdit ? routes.update : routes.store,
                    type        : 'POST',
                    data        : formData,
                    processData : false,
                    contentType : false,
                    xhr: function () {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function (e) {
                            if (e.lengthComputable) {
                                const pct = Math.round((e.loaded / e.total) * 100);
                                $('#uploadProgressWrap').show();
                                $('#uploadProgressBar').css('width', pct + '%').text(pct + '%');
                            }
                        });
                        return xhr;
                    },
                    success: function (res) {
                        $('#uploadProgressBar').css('width', '100%').text('100%');
                        SystemToast('success', res.note);
                        loadData(1);
                        showPanel('list');
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON && xhr.responseJSON.note
                            ? xhr.responseJSON.note
                            : 'Gagal menyimpan data';
                        SystemToast('danger', msg);
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
                    }
                });
            });

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
                    '<tr><td colspan="9" class="text-center">' +
                    '<i class="fa fa-spinner fa-spin"></i> Memuat data...</td></tr>'
                );
            }

            function renderError() {
                $('#DataTableBody').html(
                    '<tr><td colspan="9" class="text-danger text-center">Gagal memuat data</td></tr>'
                );
            }

            function renderTable(res) {
                if (!res.data || !res.data.length) {
                    $('#DataTableBody').html('<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>');
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
                            '<td>' + (item.code_data                || '-') + '</td>' +
                            '<td>' + (item.kategori.nama_kategori   || '-') + '</td>' +
                            '<td>' + (item.judul                    || '-') + '</td>' +
                            '<td>' + (item.tanggal_dokumen          || '-') + '</td>' +
                            '<td>' + (item.deskripsi                || '-') + '</td>' +
                            '<td>' + (item.akses                    || '-') + '</td>' +
                            '<td>' + renderFileList(item.file_path) + '</td>' +
                            '<td class="colright text-center">' +
                                '<div class="dropdown dropleft">' +
                                    '<button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">Atur</button>' +
                                    '<div class="dropdown-menu">' +
                                        '<h5 class="dropdown-header">Pengaturan Data</h5>' +
                                        '<a class="dropdown-item btn-view" style="cursor:pointer;"' +
                                        ' data-code="' + item.code_data + '">Lihat Data</a>' +
                                        '<a class="dropdown-item btn-edit ' + editCls + '" style="cursor:pointer;"' +
                                        ' data-code="' + item.code_data + '" ' + editAttr + '>Ubah Data</a>' +
                                        '<a class="dropdown-item delete-data ' + deleteCls + '" style="cursor:pointer;"' +
                                        ' data-code="' + item.code_data + '"' +
                                        ' data-name="' + (item.judul || '') + '" ' + deleteAttr + '>Hapus Data</a>' +
                                    '</div>' +
                                '</div>' +
                            '</td>' +
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

            // Row actions
            $(document).on('click', '.btn-view',               function () { showPanel('view', $(this).data('code')); });
            $(document).on('click', '.btn-edit:not(.disabled)', function () { showPanel('edit', $(this).data('code')); });
            $(document).on('click', '.dropdown-item.disabled',  function (e) { e.preventDefault(); e.stopPropagation(); });

            // Hapus data
            $(document).on('click', '.delete-data:not(.disabled)', function () {
                const code  = $(this).data('code');
                const name  = $(this).data('name');
                const $modal = $('div[data-model="confirmasi"]');

                $modal.modal({ backdrop: false });
                $modal.find('.modal-body').html(
                    '<div class="alert alert-danger">Anda yakin ingin menghapus data <b>' + name + '</b>?</div>'
                );

                // Pastikan tidak ada tombol konfirmasi lama yang menumpuk
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
                            url  : routes.delete,
                            type : 'DELETE',
                            data : { code_data: code },
                            success: function (res) {
                                $modal.modal('hide');
                                loadData(state.page);
                                SystemToast('success', res.note || 'Data berhasil dihapus');
                            },
                            error: function (xhr) {
                                const msg = xhr.responseJSON && xhr.responseJSON.note
                                    ? xhr.responseJSON.note
                                    : 'Data gagal dihapus';
                                SystemToast('danger', msg);
                            },
                            complete: function () { $btn.remove(); }
                        });
                    });
            });

            /* =============================================================
            * INIT
            * ============================================================= */
            showPanel('list');
            loadData(1);

        });
    </script>
@endsection