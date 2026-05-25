@extends('admin.AdminOne.layout.assets')
@section('title', 'Data Struktur Organisasi')

@section('content')
    <div class="page_main">
        <div class="container-fluid text-left">
            <div class="row">

                {{-- HEADER --}}
                <div class="col-md-12 bg_page_main form_action" line="hd_action">
                    <div class="col-md-12 hd_page_main">Data Struktur Organisasi</div>
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
                                    <div class="form_input full text-left">
                                        <div class="modern-upload-card" id="dropzonePhoto">
                                            <input type="file" id="field_photo" name="photo" accept="image/*" hidden>
                                            <div class="upload-content">
                                                <div class="preview-wrapper">
                                                    <img src="{{ asset('/image/setting/no_image.png') }}" 
                                                        id="preview_photo" 
                                                        class="preview-struktur" 
                                                    >
                                                    <button type="button" class="btn-change-photo" id="btnChoosePhoto">
                                                        <i class="fa fa-camera"></i>
                                                    </button>
                                                </div>
                                                <div class="upload-info">
                                                    <h5>Upload Foto Struktur Organisasi</h5>
                                                    <p>Drag &amp; drop foto disini atau klik tombol kamera</p>
                                                    <small id="photo_filename">Belum ada file dipilih</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback d-block text-center mt-2" id="err_photo"></div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group form_input text-left">
                                    @if(($level_user['editstrukturorganisasi'] ?? 'No') === 'Yes')
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
            const ROUTES = {
                detail : "{{ url('/admin/viewstrukturorganisasi') }}",
                update : "{{ url('/admin/updatestrukturorganisasi') }}"
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const DEFAULT_IMAGE = "{{ asset('/image/setting/no_image.png') }}";

            function buttonLoading(status = true){
                if(status){
                    $('#btnSaveForm').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
                }else{
                    $('#btnSaveForm').prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
                }
            }

            function clearValidation(){
                $('#err_photo').text('');
            }

            function setValidation(message = ''){
                $('#err_photo').text(message);
            }

            function loadData(){
                $.ajax({
                    url      : ROUTES.detail,
                    type     : 'GET',
                    dataType : 'json',

                    beforeSend: function (){
                        $('#preview_photo').attr('src', DEFAULT_IMAGE);
                    },

                    success: function (res){
                        console.log(res);
                        const data = res.data || {};

                        // code data
                        $('#field_code_data').val(data.code_data || 'Belum ditentukan');

                        // image path
                        let imageUrl = DEFAULT_IMAGE;

                        if(data.file_struktur_organisasi &&  data.file_struktur_organisasi !== ''){
                            imageUrl = "{{ asset('image/post') }}/" + data.file_struktur_organisasi;
                        }

                        // preview image
                        $('#preview_photo').attr('src', imageUrl).attr('onerror', "this.onerror=null;this.src='" + DEFAULT_IMAGE + "';");

                        // filename
                        $('#photo_filename').text(data.file_struktur_organisasi ? data.file_struktur_organisasi : 'Belum ada file dipilih');
                    },

                    error: function (xhr)
                    {
                        console.log(xhr);
                        $('#preview_photo').attr('src', DEFAULT_IMAGE);
                        SystemToast('danger','Gagal memuat data struktur organisasi');
                    }
                });
            }

            $('#btnChoosePhoto').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                document.getElementById('field_photo').click();
            });

            $('#dropzonePhoto').on('click', function (e) {
                // abaikan jika klik tombol kamera
                if($(e.target).closest('#btnChoosePhoto').length){
                    return;
                }
                document.getElementById('field_photo').click();
            });

            $('#field_photo').on('change', function (e) {
                clearValidation();
                const file = e.target.files[0];

                if(file){
                    $('#photo_filename').text(file.name);
                    const reader = new FileReader();
                    reader.onload = function (event){
                        $('#preview_photo').attr('src', event.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#dropzonePhoto').on('dragover', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            });

            $('#dropzonePhoto').on('dragleave', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            });

            $('#dropzonePhoto').on('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                if(files.length > 0){
                    $('#field_photo')[0].files = files;
                    $('#field_photo').trigger('change');
                }
            });

            $('#btnSaveForm').on('click', function () {
                clearValidation();
                const photo = $('#field_photo')[0].files[0];
                /* ================= VALIDATION ================= */
                if(!photo){
                    setValidation('Foto struktur organisasi wajib dipilih');
                    return;
                }
                /* ================= FORM DATA ================= */
                const formData = new FormData();
                formData.append('code_data', $('#field_code_data').val());
                formData.append('photo', photo);
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
                    cache       : false,
                    dataType    : 'json',

                    success: function (res){
                        SystemToast('success', res.note || 'Data berhasil disimpan');
                        loadData();
                    },

                    error: function (xhr){
                        console.log(xhr);
                        const response = xhr.responseJSON || {};
                        const errors   = response.errors || {};

                        if(errors.photo){
                            setValidation(errors.photo[0]);
                        }

                        SystemToast('danger', response.note || 'Gagal menyimpan data');
                    },

                    complete: function ()
                    {
                        buttonLoading(false);
                    }
                });

            });

            $('#btnCancelForm').on('click', function () {
                clearValidation();
                $('#field_photo').val('');
                loadData();
            });

            $('#preview_photo').on('error', function () {
                $(this).attr('src', DEFAULT_IMAGE);
            });

            loadData();

        });
    </script>
@endsection