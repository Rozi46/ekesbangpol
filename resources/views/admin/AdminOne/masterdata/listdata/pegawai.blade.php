@extends('admin.AdminOne.layout.assets')
@section('title', 'Data Pegawai')

@section('content')
    <div class="page_main">
        <div class="container-fluid text-left">
            <div class="row">

                {{-- HEADER --}}
                <div class="col-md-12 bg_page_main form_action" line="hd_action">
                    <div class="col-md-12 hd_page_main">Data Pegawai</div>
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
                                            <th style="width:30px; text-align: center;">No</th>
                                            <th class="sortable" style="min-width:200px; text-align: center;" data-sort="nip">NIP <i class="fa fa-sort"></i></th>
                                            <th class="sortable" style="min-width:200px; text-align: center;" data-sort="nama_pegawai">Nama Pegawai <i class="fa fa-sort"></i></th>
                                            <th class="sortable" style="min-width:200px; text-align: center;" data-sort="gender">Gender <i class="fa fa-sort"></i></th>
                                            <th class="sortable" style="min-width:200px; text-align: center;" data-sort="jabatan">Jabatan <i class="fa fa-sort"></i></th>
                                            <th class="sortable" style="min-width:200px; text-align: center;" data-sort="email">Email <i class="fa fa-sort"></i></th>
                                            <th class="sortable" style="min-width:200px; text-align: center;" data-sort="nomor_hp">Nomor HP <i class="fa fa-sort"></i></th>
                                            <th style="min-width:100px; text-align: center;">Status</th>
                                            <th class="colright" style="min-width:100px; text-align: center;"><i class="head fa fa-cog"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="dataTableBody">
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

                {{-- PANEL: FORM (Tambah & Edit) --}}
                <div class="col-md-12 bg_page_main form_action" id="panel-form" style="display:none;" line="form_action">
                    <div class="col-md-12 data_page">
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Kode Data <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <input type="text" id="field_code_data" placeholder="Auto-generate" readonly>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Nama Pegawai <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <input type="text" id="field_nama_pegawai" placeholder="Masukkan nama pegawai..." autofocus>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Nomor KTP <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <input type="text" id="field_nomor_ktp" placeholder="Masukkan nomor KTP...">
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Nama Pegawai <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <select id="field_agama">
                                            <option value="" style="display:none;">Pilih Agama</option>
                                            <option value="Islam">Islam</option>
                                            <option value="Protestan">Protestan</option>
                                            <option value="Katolik">Katolik</option>
                                            <option value="Hindu">Hindu</option>
                                            <option value="Buddha">Buddha</option>
                                            <option value="Khonghucu">Khonghucu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">NIP <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <input type="text" id="field_nip" placeholder="Masukkan NIP...">
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Gender <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <select id="field_gender">
                                            <option value="" style="display:none;">Pilih Gender</option>
                                            <option value="Laki-laki">Laki-laki</option>
                                            <option value="Perempuan">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Tempat Lahir <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <input type="text" id="field_tempat_lahir" placeholder="Masukkan tempat lahir...">
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Tanggal Lahir <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <input type="date" id="field_tanggal_lahir">
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Jabatan <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <select id="field_jabatan"></select>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Pendidikan <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <input type="text" id="field_pendidikan" placeholder="Masukkan pendidikan...">
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Jurusan <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <input type="text" id="field_jurusan" placeholder="Masukkan jurusan...">
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Pangkat <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <select id="field_pangkat"></select>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Alamat <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <textarea id="field_alamat" placeholder="Masukkan alamat..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Email <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <input type="email" id="field_email" placeholder="Masukkan email...">
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Nomor HP <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <input type="text" id="field_nomor_hp" placeholder="Masukkan nomor HP..." onKeyPress="return goodchars(event,'0123456789,',this)">
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Photo Profil <span>*</span></label>
                                    <div class="col-sm-10 input">
                                        <div class="modern-upload-card" id="dropzonePhoto">
                                            <input type="file" id="field_photo_profil" name="photo_profil" accept="image/*"  hidden>
                                            <div class="upload-content">
                                                {{-- Preview --}}
                                                <div class="preview-wrapper">
                                                    <img src="{{ asset('/themes/admin/AdminOne/image/no_image.jpg') }}" id="preview_photo_profil" class="preview-avatar">
                                                    <button type="button" class="btn-change-photo" id="btnChoosePhoto"><i class="fa fa-camera"></i></button>
                                                </div>
                                                {{-- Info --}}
                                                <div class="upload-info">
                                                    <h5> Upload Foto Profil</h5>
                                                    <p>Drag & drop foto disini atau klik tombol kamera</p>
                                                    <small id="photo_filename">Belum ada file dipilih</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group row form_input text-left">
                                    <label class="col-sm-2 col-form-label">Status <span>*</span></label>
                                    <div class="col-sm-10 input">
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
                        </div> 
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group form_input text-left">                                        
                                    <button type="button" class="btn btn-primary" id="btnSaveForm"><i class="fa fa-save"></i> Simpan</button>
                                    <button type="button" class="btn btn-secondary" id="btnCancelForm"><i class="fa fa-times"></i> Batal</button>
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
                                    <tr><th width="160" class="bg-light">Kode Data</th><td id="view_code_data">-</td></tr>
                                    <tr><th class="bg-light">Nama Pegawai</th><td id="view_nama_pegawai">-</td></tr>
                                    <tr><th class="bg-light">Nomor KTP</th><td id="view_nomor_ktp">-</td> </tr>
                                    <tr><th class="bg-light">Agama</th><td id="view_agama">-</td></tr>
                                    <tr><th class="bg-light">NIP</th><td id="view_nip">-</td></tr>
                                    <tr><th class="bg-light">Gender</th><td id="view_gender">-</td></tr>
                                    <tr><th class="bg-light">Tempat Lahir</th><td id="view_tempat_lahir">-</td></tr>
                                    <tr><th class="bg-light">Tanggal Lahir</th><td id="view_tanggal_lahir">-</td> </tr>
                                    <tr><th class="bg-light">Jabatan</th><td id="view_jabatan">-</td></tr>
                                    <tr><th class="bg-light">Pendidikan</th><td id="view_pendidikan">-</td></tr>
                                    <tr><th class="bg-light">Jurusan</th><td id="view_jurusan">-</td></tr>
                                    <tr><th class="bg-light">Pangkat</th><td id="view_pangkat">-</td></tr>
                                    <tr><th class="bg-light">Alamat</th><td id="view_alamat">-</td></tr>
                                    <tr><th class="bg-light">Email</th><td id="view_email">-</td></tr>
                                    <tr><th class="bg-light">Nomor HP</th><td id="view_nomor_hp">-</td></tr>
                                    <tr><th class="bg-light">Photo Profil</th><td id="view_photo_profil">-</td></tr>
                                    <tr><th class="bg-light">Status</th><td id="view_status">-</td></tr>
                                    <tr><th class="bg-light">Dibuat Pada</th><td id="view_created_at">-</td></tr>
                                    <tr><th class="bg-light">Diperbarui Pada</th><td id="view_updated_at">-</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group form_input text-left">        
                                    @if(($level_user['editpegawai'] ?? 'No') === 'Yes')
                                        <button type="button" class="btn btn-warning btn-sm" id="btnEditFromView"><i class="fa fa-edit"></i> Ubah Data</button>
                                    @endif
                                    <button type="button" class="btn btn-secondary btn-sm" id="btnBackFromView"><i class="fa fa-arrow-left"></i> Kembali</button>
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
            // start upload photo drop and drag
                const dropzone  = $('#dropzonePhoto');
                const inputFile = $('#field_photo_profil');

                $('#btnChoosePhoto').on('click', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    inputFile.trigger('click');
                });

                dropzone.on('click', function(){
                    inputFile.trigger('click');
                });

                inputFile.on('click', function(e){
                    e.stopPropagation();
                });

                dropzone.on('dragover', function(e){
                    e.preventDefault();
                    dropzone.addClass('dragover');
                });

                dropzone.on('dragleave', function(e){
                    e.preventDefault();
                    dropzone.removeClass('dragover');
                });

                dropzone.on('drop', function(e){
                    e.preventDefault();
                    dropzone.removeClass('dragover');
                    const files = e.originalEvent.dataTransfer.files;

                    if(files.length){
                        inputFile[0].files = files;
                        previewImage(files[0]);
                    }
                });

                inputFile.on('change', function(){
                    if(this.files && this.files[0]){
                        previewImage(this.files[0]);
                    }
                });

                function previewImage(file){
                    // VALIDASI SIZE
                    if(file.size > 2 * 1024 * 1024){
                        $('#err_photo_profil').text('Ukuran foto maksimal 2MB');

                        inputFile.val('');
                        return;
                    }

                    // VALIDASI FORMAT
                    const allowed = ['image/jpeg','image/png','image/jpg'];

                    if(!allowed.includes(file.type)){
                        $('#err_photo_profil').text('Format harus JPG, JPEG, PNG');

                        inputFile.val('');
                        return;
                    }

                    $('#err_photo_profil').text('');
                    $('#photo_filename').text(file.name);

                    const reader = new FileReader();
                    reader.onload = function(e){
                        $('#preview_photo_profil').attr('src',e.target.result);
                    };

                    reader.readAsDataURL(file);
                }
            // end upload photo drop and drag

            const routes = {
                list:   "{{ url('/admin/datalistpegawai') }}",
                store:  "{{ url('/admin/savepegawai') }}",
                detail: "{{ url('/admin/viewpegawai') }}",
                update: "{{ url('/admin/updatepegawai') }}",
                status: "{{ url('/admin/statuspegawai') }}",
                delete: "{{ url('/admin/deletepegawai') }}",
                jabatan: "{{ url('/admin/listopjabatan') }}",
                pangkat: "{{ url('/admin/listoppangkat') }}"
            };

            const action = {
                new:    {{ (($level_user['newpegawai'] ?? 'No') === 'Yes') ? 'true' : 'false' }},
                edit:   {{ (($level_user['editpegawai'] ?? 'No') === 'Yes') ? 'true' : 'false' }},
                delete: {{ (($level_user['deletepegawai'] ?? 'No') === 'Yes') ? 'true' : 'false' }},
                export: {{ (($level_user['exportpegawai'] ?? 'No') === 'Yes') ? 'true' : 'false' }}
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
                        loadJabatan();
                        loadPangkat();
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
                let title = 'Data pegawai';
                let buttons = `<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button> `;

                switch (state.mode) {
                    case 'list':
                        if (action.new) {
                            buttons += `<button type="button" class="btn btn-primary" id="btnTambah"><i class="fa fa-plus"></i> Tambah Data</button> `;
                        }
                        if (action.export) {
                            buttons += `<button type="button" class="btn btn-info" onclick="exportdata({url:'/admin/exportpegawai',btn:this})"><i class="fa fa-download"></i> Export Data</button>`;
                        }
                        break;

                    case 'add':
                        title = 'Tambah Data pegawai';
                        break;

                    case 'edit':
                        title = 'Ubah Data pegawai';
                        break;

                    case 'view':
                        title = 'Detail Data pegawai';
                        break;
                }

                $('#pageTitle').text(title);
                $('#headerActions').html(buttons);

                if (state.mode === 'list') {
                    $('#btnTambah').on('click', function () { showPanel('add'); });
                }
            }

            function resetForm() {
                // reset input text/email/date
                $('#panel-form').find('input[type="text"], input[type="email"], input[type="date"], textarea').val('');
                // reset select
                $('#panel-form').find('select').val('').trigger('change');
                // reset file
                $('#field_photo_profil').val('');
                // reset radio status  
                $('input[name="field_status"][value="Aktif"]').prop('checked', true);
                $('#field_code_data').val('');
                $('#preview_photo_profil').attr('src','{{ asset("/themes/admin/AdminOne/image/no_image.jpg") }}');
                $('#photo_filename').text('Belum ada file dipilih');
            }

            function clearFormErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            function loadDetail(code, target) {
                const loading = `<i class="fa fa-spinner fa-spin"></i> Memuat...`;

                if (target === 'view') {
                    $('#view_code_data, #view_nama_pegawai, #view_nomor_ktp, #view_agama, #view_nip, #view_gender, #view_tempat_lahir, #view_tanggal_lahir, #view_jabatan, #view_pendidikan, #view_jurusan, #view_pangkat, #view_alamat, #view_email, #view_nomor_hp, #view_photo_profil, #view_status, #view_created_at, #view_updated_at').html(loading);
                }

                $.ajax({
                    url: routes.detail,
                    type: 'GET',
                    data: { code_data: code },
                    success: function (res) {
                        const d = res.data;                 

                        if (target === 'view') {
                            $('#view_code_data').text(d.code_data ?? '-');
                            $('#view_nama_pegawai').text(d.nama_pegawai ?? '-');
                            $('#view_nomor_ktp').text(d.nomor_ktp ?? '-');
                            $('#view_agama').text(d.agama ?? '-');
                            $('#view_nip').text(d.nip ?? '-');
                            $('#view_gender').text(d.gender ?? '-');
                            $('#view_tempat_lahir').text(d.tempat_lahir ?? '-');
                            $('#view_tanggal_lahir').text(d.tanggal_lahir ?? '-');
                            $('#view_jabatan').text(d.position.jabatan ?? '-');
                            $('#view_pendidikan').text(d.pendidikan ?? '-');
                            $('#view_jurusan').text(d.jurusan ?? '-');
                            $('#view_pangkat').text(d.ranks ? `${d.ranks.pangkat}-${d.ranks.golongan}.${d.ranks.ruang}` : '-');
                            $('#view_alamat').text(d.alamat ?? '-');
                            $('#view_email').text(d.email ?? '-');
                            $('#view_nomor_hp').text(d.nomor_hp ?? '-');
                            $('#view_photo_profil').text(d.photo_profil ?? '-');
                            $('#view_photo_profil').html(
                                d.photo_profil
                                    ? `<img src="/themes/admin/AdminOne/image/upload/${d.photo_profil}" 
                                            style="max-width:120px;border-radius:8px;">`
                                    : '-'
                            );

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
                            $('#field_code_data').val(d.code_data || '');
                            $('#field_nama_pegawai').val(d.nama_pegawai || '');
                            $('#field_nomor_ktp').val(d.nomor_ktp || '');
                            $('#field_agama').val(d.agama || '').trigger('change');
                            $('#field_nip').val(d.nip || '');
                            $('#field_gender').val(d.gender || '').trigger('change');
                            $('#field_tempat_lahir').val(d.tempat_lahir || '');
                            $('#field_tanggal_lahir').val(d.tanggal_lahir || '');
                            $('#field_pendidikan').val(d.pendidikan || '');
                            $('#field_jurusan').val(d.jurusan || '');
                            $('#field_alamat').val(d.alamat || '');
                            $('#field_email').val(d.email || '');
                            $('#field_nomor_hp').val(d.nomor_hp || '');

                            loadJabatan(d.code_jabatan);
                            loadPangkat(d.code_pangkat);

                            // radio status
                            $('input[name="field_status"][value="' + (d.status_data || 'Aktif') + '"]').prop('checked', true);

                            // reset file input (boleh kosongkan)
                            $('#field_photo_profil').val('');

                            // tampilkan preview gambar lama
                            if (d.photo_profil) {
                                $('#preview_photo_profil').attr('src',`/themes/admin/AdminOne/image/upload/${d.photo_profil}`);
                                $('#photo_filename').text(d.photo_profil);
                            } else {
                                $('#preview_photo_profil').attr('src','/themes/admin/AdminOne/image/no_image.jpg');
                                $('#photo_filename').text('Belum ada file dipilih');
                            }
                        }
                    },
                    error: function () {
                        SystemToast('danger', 'Gagal memuat detail data');
                    }
                });
            }

            $('#btnSaveForm').on('click', function () {
                clearFormErrors();
                const formData = new FormData();

                formData.append('code_data', $('#field_code_data').val());
                formData.append('nama_pegawai', $('#field_nama_pegawai').val().trim());
                formData.append('nomor_ktp', $('#field_nomor_ktp').val().trim());
                formData.append('agama', $('#field_agama').val());
                formData.append('nip', $('#field_nip').val().trim());
                formData.append('gender', $('#field_gender').val());
                formData.append('tempat_lahir', $('#field_tempat_lahir').val().trim());
                formData.append('tanggal_lahir', $('#field_tanggal_lahir').val());
                formData.append('code_jabatan', $('#field_jabatan').val());
                formData.append('pendidikan', $('#field_pendidikan').val().trim());
                formData.append('jurusan', $('#field_jurusan').val().trim());
                formData.append('code_pangkat', $('#field_pangkat').val());
                formData.append('alamat', $('#field_alamat').val().trim());
                formData.append('email', $('#field_email').val().trim());
                formData.append('nomor_hp', $('#field_nomor_hp').val().trim());
                formData.append('status_data', $('input[name="field_status"]:checked').val());

                // FILE FOTO
                const photo = $('#field_photo_profil')[0].files[0];

                if(photo){
                    formData.append('photo_profil', photo);
                }

                // VALIDASI
                let valid = true;

                // $('#panel-form')
                //     .find('input[type="text"], input[type="email"], input[type="date"], textarea, select')
                //     .each(function () {
                //         const id = $(this).attr('id');
                //         if(!id || id === 'field_code_data'){
                //             return;
                //         }

                //         if($(this).val().trim() === ''){
                //             valid = false;
                //             $(this).addClass('is-invalid');
                //             $('#err_' + id.replace('field_', ''))
                //                 .text('Field wajib diisi');
                //         }
                //     });

                // const isEdit = state.mode === 'edit';
                // const url = isEdit ? routes.update : routes.store;
                // const method  = isEdit ? 'PUT' : 'POST';

                const isEdit = state.mode === 'edit';
                const url = isEdit ? routes.update : routes.store;
                // selalu POST untuk FormData
                const method = 'POST';
                // spoof method PUT saat edit
                if (isEdit) {
                    formData.append('_method', 'PUT');
                }

                // VALIDASI FOTO hanya wajib saat tambah data
                // if (!photo && !isEdit) {
                //     valid = false;
                //     $('#err_photo_profil').text('Foto profil wajib diupload');
                // }

                // if(!valid){
                //     return;
                // }                

                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: method,
                    dataType: 'json',
                    data: formData,

                    processData: false,
                    contentType: false,

                    success: function (res) {
                        SystemToast('success',res.note || 'Data berhasil disimpan');
                        loadData(1);
                        showPanel('list');
                    },

                    error: function (xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        Object.keys(errors).forEach(function (key) {
                            $('#field_' + key).addClass('is-invalid');

                            $('#err_' + key).text(errors[key][0]);
                        });

                        SystemToast('danger', xhr.responseJSON?.note || xhr.responseJSON?.message || 'Gagal menyimpan data');
                    },

                    complete: function () {
                        btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
                    }
                });
            });

            $('#btnCancelForm').on('click', function () { 
                showPanel('list'); 
            });

            $('#btnEditFromView').on('click', function () {
                const code = $(this).data('code');
                showPanel('edit', code);
            });

            $('#btnBackFromView').on('click', function () { 
                showPanel('list'); 
            });

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
                $('#dataTableBody').html(`<tr><td colspan="9" class="text-center"><i class="fa fa-spinner fa-spin"></i> Memuat data...</td></tr>`);
            }

            function renderError() {
                $('#dataTableBody').html(`<tr><td colspan="9" class="text-danger text-center">Gagal memuat data</td></tr>`);
            }
            
            function renderTable(res) {
                if (!res.data.length) {
                    $('#dataTableBody').html(`<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>`);
                    return;
                }

                let html = '';
                res.data.forEach((item, index) => {
                    const isActive = item.status_data === 'Aktif';
                    const checked = isActive ? 'checked' : '';
                    const nextStatus = isActive ? 'Tidak Aktif' : 'Aktif';
                    const editDisabled = !action.edit;
                    const deleteDisabled = !action.delete;
                    html += `
                        <tr>
                            <td class="text-center">${res.from + index}</td>
                            <td class="text-center">${item.nip ?? '-'}</td>
                            <td>${item.nama_pegawai ?? '-'}</td>
                            <td class="text-center">${item.gender ?? '-'}</td>
                            <td>${item.position.jabatan ?? '-'}</td>
                            <td>${item.email ?? '-'} </td>
                            <td class="text-center">${item.nomor_hp ?? '-'}</td>
                            <td class="text-center">
                                <input type="checkbox"
                                    class="ios status-toggle"
                                    data-code="${item.code_data}"
                                    data-status="${nextStatus}"
                                    ${checked}
                                    ${editDisabled ? 'disabled' : ''}
                                >
                            </td>
                            <td class="colright text-center">
                                <div class="dropdown dropleft">
                                    <button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">Atur</button>
                                    <div class="dropdown-menu">
                                        <h5 class="dropdown-header">Pengaturan Data</h5>
                                        <a class="dropdown-item btn-view" data-code="${item.code_data}">Lihat Data</a>
                                        <a
                                            class="dropdown-item btn-edit
                                            ${editDisabled ? 'disabled text-muted' : ''}"
                                            data-code="${item.code_data}">
                                            Ubah Data
                                        </a>
                                        <a
                                            class="dropdown-item delete-data
                                            ${deleteDisabled ? 'disabled text-muted' : ''}"
                                            data-code="${item.code_data}"
                                            data-name="${item.nama_pegawai}">
                                            Hapus Data
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                $('#dataTableBody').html(html);
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

            $('#field_jabatan').select2({
                placeholder: 'Pilih Jabatan',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#panel-form')
            });

            function loadJabatan(selected = '') {
                $('#field_jabatan').html('<option value="">Memuat data...</option>');

                $.ajax({
                    url: routes.jabatan,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        u: "{{ session('id') }}",
                        token: "{{ session('key_token') }}"
                    },
                    success: function (res) {
                        let html = '<option value="">Pilih Jabatan</option>';

                        if (res.results && res.results.length > 0) {
                            res.results.forEach(item => {
                                html += `
                                    <option value="${item.code_data}" 
                                        ${selected == item.code_data ? 'selected' : ''}>
                                        ${item.jabatan}
                                    </option>
                                `;
                            });
                        }

                        $('#field_jabatan').html(html).trigger('change');
                    },
                    error: function () {
                        $('#field_jabatan').html('<option value="">Gagal load data</option>');
                        SystemToast('danger', 'Gagal memuat data jabatan');
                    }
                });
            }

            $('#field_pangkat').select2({
                placeholder: 'Pilih Pangkat',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#panel-form')
            });

            function loadPangkat(selected = '') {
                $('#field_pangkat').html('<option value="">Memuat data...</option>');

                $.ajax({
                    url: routes.pangkat,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        u: "{{ session('id') }}",
                        token: "{{ session('key_token') }}"
                    },
                    success: function (res) {
                        let html = '<option value="">Pilih Pangkat</option>';

                        if (res.results && res.results.length > 0) {
                            res.results.forEach(item => {
                                html += `
                                    <option value="${item.code_data}" 
                                        ${selected == item.code_data ? 'selected' : ''}>
                                        ${item.pangkat}-${item.golongan}.${item.ruang}
                                    </option>
                                `;
                            });
                        }

                        $('#field_pangkat').html(html).trigger('change');
                    },
                    error: function () {
                        $('#field_pangkat').html('<option value="">Gagal load data</option>');
                        SystemToast('danger', 'Gagal memuat data pangkat');
                    }
                });
            }
       
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
        });
    </script>
@endsection