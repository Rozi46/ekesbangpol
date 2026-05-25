@extends('admin.AdminOne.layout.assets')
@section('title', 'Data Alamat dan Kontak')

@section('content')
    <div class="page_main">
        <div class="container-fluid text-left">
            <div class="row">

                {{-- HEADER --}}
                <div class="col-md-12 bg_page_main form_action" line="hd_action">
                    <div class="col-md-12 hd_page_main">Data Alamat dan Kontak</div>
                    <div class="col-md-12 bg_act_page_main">
                        <div class="row">
                            <div class="col-xl-12 col_act_page_main text-left">
                                <button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PANEL: FORM (Tambah & Edit) --}}
                <div class="col-md-12 bg_page_main form_action" id="panel-form" line="form_action">
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
                            <div class="col-md-12 bg_form_page">

                                <div class="col-md-12 bg_form_page">
                                    <div class="form_input text-left">
                                        <textarea id="field_isi" name="isi"
                                            class="ckeditor" placeholder="Isi alamat dan kontak.."></textarea>
                                        <div class="invalid-feedback" id="err_isi"></div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group form_input text-left">
                                    @if(($level_user['editalamatkontak'] ?? 'No') === 'Yes')
                                        <button type="button" class="btn btn-primary" id="btnSaveForm">
                                            <i class="fa fa-save"></i> Simpan
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-secondary" id="btnCancelForm">
                                        <i class="fa fa-times"></i> Batal
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
        $(document).ready(function () {

            /* =========================================================
            * ROUTES
            * ========================================================= */
            const ROUTES = {
                detail : "{{ url('/admin/viewalamatkontak') }}",
                update : "{{ url('/admin/updatealamatkontak') }}"
            };

            /* =========================================================
            * CSRF TOKEN
            * ========================================================= */
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            /* =========================================================
            * INIT CKEDITOR
            * ========================================================= */
            function initEditor() {

                if (typeof CKEDITOR === 'undefined') {
                    return;
                }

                // Hapus instance lama jika ada
                if (CKEDITOR.instances.field_isi) {
                    CKEDITOR.instances.field_isi.destroy(true);
                }

                // Buat editor baru
                CKEDITOR.replace('field_isi');

                // Event ketika editor siap
                CKEDITOR.instances.field_isi.on('change', function () {

                    $('#err_isi').text('');
                    $('#cke_field_isi').css('border', '');
                });
            }

            /* =========================================================
            * AMBIL VALUE EDITOR
            * ========================================================= */
            function getIsi() {

                if (
                    typeof CKEDITOR !== 'undefined' &&
                    CKEDITOR.instances.field_isi
                ) {
                    return CKEDITOR.instances.field_isi.getData().trim();
                }

                return $('#field_isi').val().trim();
            }

            /* =========================================================
            * SET VALUE EDITOR
            * ========================================================= */
            function setIsi(value = '') {

                if (
                    typeof CKEDITOR !== 'undefined' &&
                    CKEDITOR.instances.field_isi
                ) {
                    CKEDITOR.instances.field_isi.setData(value);
                } else {
                    $('#field_isi').val(value);
                }
            }

            /* =========================================================
            * CLEAR VALIDATION
            * ========================================================= */
            function clearValidation() {

                $('#err_isi').text('');
                $('#field_isi').removeClass('is-invalid');

                $('#cke_field_isi').css({
                    border: ''
                });
            }

            /* =========================================================
            * SET VALIDATION ERROR
            * ========================================================= */
            function setValidation(message) {

                $('#field_isi').addClass('is-invalid');
                $('#err_isi').text(message);

                $('#cke_field_isi').css({
                    border: '1px solid #dc3545'
                });
            }

            /* =========================================================
            * BUTTON LOADING
            * ========================================================= */
            function buttonLoading(status = true) {
                if (status) {
                    $('#btnSaveForm')
                        .prop('disabled', true)
                        .html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
                } else {
                    $('#btnSaveForm')
                        .prop('disabled', false)
                        .html('<i class="fa fa-save"></i> Simpan');
                }
            }

            /* =========================================================
            * LOAD DATA
            * ========================================================= */
            function loadData() {
                $.ajax({
                    url      : ROUTES.detail,
                    type     : 'GET',
                    dataType : 'json',

                    success: function (res) {
                        console.log(res);
                        const data = res.data || {};
                        $('#field_code_data').val(data.code_data || '');
                        setIsi(data.isi_page || '');
                    },

                    error: function (xhr) {
                        console.log(xhr);
                        SystemToast('danger','Gagal memuat data alamat dan kontak');
                    }
                });
            }

            /* =========================================================
            * SIMPAN DATA
            * ========================================================= */
            $('#btnSaveForm').on('click', function () {
                clearValidation();
                const isi = getIsi();
                /* ================= VALIDASI ================= */
                if (!isi) {
                    setValidation('Alamat dan kontak wajib diisi');
                    return;
                }

                /* ================= FORM DATA ================= */
                const formData = new FormData();
                formData.append('code_data',$('#field_code_data').val());
                formData.append('isi', isi);
                formData.append('_method', 'PUT');

                /* ================= LOADING ================= */
                buttonLoading(true);

                /* ================= AJAX ================= */
                $.ajax({
                    url         : ROUTES.update,
                    type        : 'POST',
                    data        : formData,
                    processData : false,
                    contentType : false,
                    dataType    : 'json',

                    success: function (res) {
                        SystemToast('success', res.note || 'Data berhasil disimpan');
                        loadData();
                    },

                    error: function (xhr) {
                        const response = xhr.responseJSON || {};
                        const errors   = response.errors || {};

                        if (errors.isi) {
                            setValidation(errors.isi[0]);
                        }

                        SystemToast('danger',response.note || 'Gagal menyimpan data');
                    },

                    complete: function () {
                        buttonLoading(false);
                    }
                });
            });

            /* =========================================================
            * BATAL
            * ========================================================= */
            $('#btnCancelForm').on('click', function () {
                clearValidation();
                loadData();
            });

            /* =========================================================
            * INIT
            * ========================================================= */
            initEditor();

            // Delay sedikit agar editor siap
            setTimeout(function () {
                loadData();
            }, 500);

        });
    </script>
@endsection