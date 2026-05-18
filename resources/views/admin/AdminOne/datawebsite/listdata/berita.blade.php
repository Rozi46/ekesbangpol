@extends('admin.AdminOne.layout.assets')
@section('title', 'Data Berita')

@section('content')
    <div class="page_main">
        <div class="container-fluid text-left">
            <div class="row">

                {{-- HEADER --}}
                <div class="col-md-12 bg_page_main form_action" line="hd_action">
                    <div class="col-md-12 hd_page_main">Data Berita</div>
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
                    <div class="col-md-12 data_page" id="dataTableBody">
                    </div>
                </div>

                {{-- PANEL: FORM (Tambah & Edit) --}}
                <div class="col-md-12 bg_page_main form_action" id="panel-form" style="display:none;" line="form_action">
                    <div class="col-md-12 data_page">
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-9 bg_form_page">
                                <div class="col-md-12 bg_form_page">
                                    <div class="form_input text-left">
                                        <div class="tag_title">Judul Berita<span>*</span></div>
                                        <input type="text" id="judul_berita" placeholder="Judul berita.." value="" autofocus/>
                                    </div>
                                </div>
                                <div class="col-md-12 bg_form_page">
                                    <div class="form_input text-left">
                                        <div class="tag_title">Isi Berita <span>*</span></div>
                                        <textarea id="isi_berita" class="ckeditor" placeholder="Isi berita.."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 bg_form_page">
                                    <div class="form_input text-left">
                                        <div class="tag_title">Sumber Berita</div>
                                        <input type="text" id="sumber_berita" placeholder="Sumber berita.." value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 bg_form_page">
                                <div class="col-md-12 bg_form_page">
                                    <div class="form_input full text-left">
                                        <div class="tag_title">Foto</div>
                                        <div class="modern-upload-card" id="dropzonePhoto">
                                            <input type="file" id="field_photo_berita" name="photo_berita" accept="image/*"  hidden>
                                            <div class="upload-content">
                                                {{-- Preview --}}
                                                <div class="preview-wrapper">
                                                    <img src="{{ asset('/themes/admin/AdminOne/image/no_image.png') }}" id="preview_photo_berita" class="preview-berita">
                                                    <button type="button" class="btn-change-photo" id="btnChoosePhoto"><i class="fa fa-camera"></i></button>
                                                </div>
                                                {{-- Info --}}
                                                <div class="upload-info">
                                                    <h5> Upload Foto</h5>
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
                                    <tr><th class="bg-light">Judul</th><td id="view_judul">-</td></tr>
                                    <tr><th class="bg-light">Isi</th><td id="view_isi">-</td> </tr>
                                    <tr><th class="bg-light">Sumber</th><td id="view_sumber">-</td></tr>
                                    <tr><th class="bg-light">Foto</th><td id="view_foto">-</td></tr>
                                    <tr><th class="bg-light">Dibuat Pada</th><td id="view_created_at">-</td></tr>
                                    <tr><th class="bg-light">Diperbarui Pada</th><td id="view_updated_at">-</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row bg_data_page form_page content">
                            <div class="col-md-12 bg_form_page">
                                <div class="form-group form_input text-left">        
                                    @if(($level_user['editberita'] ?? 'No') === 'Yes')
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
                const inputFile = $('#field_photo_berita');

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
                        $('#err_photo_berita').text('Ukuran foto maksimal 2MB');

                        inputFile.val('');
                        return;
                    }

                    // VALIDASI FORMAT
                    const allowed = ['image/jpeg','image/png','image/jpg'];

                    if(!allowed.includes(file.type)){
                        $('#err_photo_berita').text('Format harus JPG, JPEG, PNG');

                        inputFile.val('');
                        return;
                    }

                    $('#err_photo_berita').text('');
                    $('#photo_filename').text(file.name);

                    const reader = new FileReader();
                    reader.onload = function(e){
                        $('#preview_photo_berita').attr('src',e.target.result);
                    };

                    reader.readAsDataURL(file);
                }
            // end upload photo drop and drag
            const NO_IMAGE = "{{ asset('/themes/admin/AdminOne/image/no_image.png') }}";

            const routes = {
                list:   "{{ url('/admin/datalistberita') }}",
                store:  "{{ url('/admin/saveberita') }}",
                detail: "{{ url('/admin/viewberita') }}",
                update: "{{ url('/admin/updateberita') }}",
                status: "{{ url('/admin/statusberita') }}",
                delete: "{{ url('/admin/deleteberita') }}"
            };

            const action = {
                new:    {{ (($level_user['newberita'] ?? 'No') === 'Yes') ? 'true' : 'false' }},
                edit:   {{ (($level_user['editberita'] ?? 'No') === 'Yes') ? 'true' : 'false' }},
                delete: {{ (($level_user['deleteberita'] ?? 'No') === 'Yes') ? 'true' : 'false' }},
                export: {{ (($level_user['exportberita'] ?? 'No') === 'Yes') ? 'true' : 'false' }}
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
                let title = 'Data berita';
                let buttons = `<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button> `;

                switch (state.mode) {
                    case 'list':
                        if (action.new) {
                            buttons += `<button type="button" class="btn btn-primary" id="btnTambah"><i class="fa fa-plus"></i> Tambah Data</button> `;
                        }
                        if (action.export) {
                            buttons += `<button type="button" class="btn btn-info" onclick="exportdata({url:'/admin/exportberita',btn:this})"><i class="fa fa-download"></i> Export Data</button>`;
                        }
                        break;

                    case 'add':
                        title = 'Tambah Data berita';
                        break;

                    case 'edit':
                        title = 'Ubah Data berita';
                        break;

                    case 'view':
                        title = 'Detail Data berita';
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
                $('#field_photo_berita').val('');
                // reset radio status  
                $('input[name="field_status"][value="Aktif"]').prop('checked', true);
                $('#field_code_data').val('');
                $('#preview_photo_berita').attr('src','{{ asset("/themes/admin/AdminOne/image/no_image.png") }}');
                $('#photo_filename').text('Belum ada file dipilih');
            }

            function clearFormErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            // function loadDetail(code, target) {
            //     const loading = `<i class="fa fa-spinner fa-spin"></i> Memuat data...`;

            //     if (target === 'view') {
            //         $('#view_code_data, #view_judul_berita, #view_isi_berita, #view_sumber_berita, #view_tumb_berita, #view_status, #view_created_at, #view_updated_at').html(loading);
            //     }

            //     $.ajax({
            //         url: routes.detail,
            //         type: 'GET',
            //         data: { code_data: code },
            //         success: function (res) {
            //             const d = res.data;                 

            //             if (target === 'view') {
            //                 $('#view_code_data').text(d.code_data ?? '-');
            //                 $('#view_judul_berita').text(d.judul_berita ?? '-');
            //                 $('#view_isi_berita').text(d.isi_berita ?? '-');
            //                 $('#view_sumber_berita').text(d.sumber_berita ?? '-');
            //                 $('#view_tumb_berita').text(d.tumb_berita ?? '-');
            //                 $('#view_photo_berita').html(
            //                     d.tumb_berita
            //                         ? `<img src="/themes/admin/AdminOne/image/upload/${d.tumb_berita}" 
            //                                 style="max-width:120px;border-radius:8px;">`
            //                         : '-'
            //                 );

            //                 const statusHtml = d.status_data === 'Aktif'
            //                     ? `
            //                         <div class="alert alert-success"
            //                             style="margin:0 auto; display:inline-block; text-align:center; font-size:12px; padding:2px 10px;">
            //                             <strong>${d.status_data ?? 'Belum Ditentukan'}</strong>
            //                         </div>
            //                     `
            //                     : `
            //                         <div class="alert alert-danger"
            //                             style="margin:0 auto; display:inline-block; text-align:center; font-size:12px; padding:2px 10px;">
            //                             <strong>${d.status_data ?? 'Belum Ditentukan'}</strong>
            //                         </div>
            //                     `;

            //                 $('#view_status').html(statusHtml);
            //                 $('#view_created_at').text(formatTanggalIndonesia(d.created_at) ?? '-');
            //                 $('#view_updated_at').text(formatTanggalIndonesia(d.updated_at) ?? '-');
            //                 $('#btnEditFromView').data('code', d.code_data);
            //             }

            //             if (target === 'edit') {
            //                 $('#field_code_data').val(d.code_data || '');
            //                 $('#field_judul_berita').val(d.judul_berita || '');
            //                 $('#field_isi_berita').val(d.field_isi_berita || '');
            //                 $('#field_sumber_berita').val(d.field_sumber_berita || '');
            //                 // radio status
            //                 $('input[name="field_status"][value="' + (d.status_data || 'Aktif') + '"]').prop('checked', true);

            //                 // reset file input (boleh kosongkan)
            //                 $('#field_photo_berita').val('');

            //                 // tampilkan preview gambar lama
            //                 if (d.photo_berita) {
            //                     $('#preview_photo_berita').attr('src',`/themes/admin/AdminOne/image/upload/${d.photo_berita}`);
            //                     $('#photo_filename').text(d.photo_berita);
            //                 } else {
            //                     $('#preview_photo_berita').attr('src','/themes/admin/AdminOne/image/no_image.png');
            //                     $('#photo_filename').text('Belum ada file dipilih');
            //                 }
            //             }
            //         },
            //         error: function () {
            //             SystemToast('danger', 'Gagal memuat detail data');
            //         }
            //     });
            // }

            function loadDetail(code, target) {
                $.get(routes.detail, { code_data: code }, function (res) {
                    const d = res.data;

                    if (target === 'view') {
                        $('#view_code_data').text(d.code_data);
                        $('#view_judul').text(d.judul_berita);
                        $('#view_isi').html(d.isi_berita);
                        $('#view_sumber').text(d.sumber_berita);

                        $('#view_foto').html(`
                            <img src="${getPhotoUrl(d.tumb_berita)}"
                                style="max-width:150px;border-radius:8px">
                        `);

                        $('#view_created_at').text(formatTanggalIndonesia(d.created_at));
                        $('#view_updated_at').text(formatTanggalIndonesia(d.updated_at));
                    }

                    if (target === 'edit') {
                        $('#field_code_data').val(d.code_data);
                        $('#field_judul_berita').val(d.judul_berita);
                        CKEDITOR.instances.field_isi_berita.setData(d.isi_berita || '');
                        $('#field_sumber_berita').val(d.sumber_berita);
                        $('#field_status_data').val(d.status_data);

                        $('#preview_photo_berita').attr('src', getPhotoUrl(d.tumb_berita));
                    }
                });
            }

            $('#btnSaveForm').on('click', function () {
                clearFormErrors();
                const formData = new FormData();

                formData.append('judul_berita', $('#field_judul_berita').val());
                formData.append('isi_berita', $('#field_isi_berita').val());
                formData.append('sumber_berita', $('#field_sumber_berita').val());

                // FILE FOTO
                const photo = $('#field_photo_berita')[0].files[0];

                if(photo){
                    formData.append('photo_berita', photo);
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
                //     $('#err_photo_berita').text('Foto profil wajib diupload');
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

            // $('#btnSaveForm').on('click', function () {
            //     const formData = new FormData();

            //     formData.append('code_data', $('#field_code_data').val());
            //     formData.append('judul_berita', $('#field_judul_berita').val());
            //     formData.append('isi_berita', CKEDITOR.instances.field_isi_berita.getData());
            //     formData.append('sumber_berita', $('#field_sumber_berita').val());
            //     formData.append('status_data', $('#field_status_data').val());

            //     const photo = $('#field_photo_berita')[0].files[0];
            //     if (photo) {
            //         formData.append('photo_berita', photo);
            //     }

            //     const isEdit = state.mode === 'edit';

            //     if (isEdit) {
            //         formData.append('_method', 'PUT');
            //     }

            //     $.ajax({
            //         url: isEdit ? routes.update : routes.store,
            //         type: 'POST',
            //         data: formData,
            //         processData: false,
            //         contentType: false,

            //         success(res) {
            //             SystemToast('success', res.note);
            //             showPanel('list');
            //             loadData();
            //         },

            //         error(xhr) {
            //             SystemToast('danger', xhr.responseJSON?.message || 'Gagal simpan');
            //         }
            //     });
            // });

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
                $('#dataTableBody').html(`
                    <div class="list_notif read not">
                        <td class="head text-center"><i class="fa fa-spinner fa-spin"></i> Memuat data...</td>                              
                    </div>
                `);
            }

            function renderError() {
                $('#dataTableBody').html(`
                    <div class="list_notif read not">
                        <div class="head text-center">Gagal memuat data</div>                                
                    </div>
                `);
            }
            
            function renderTable(res) {
                if (!res.data.length) {
                    $('#dataTableBody').html(`
                        <div class="list_notif read not">
                            <div class="head text-center">Tidak ada data</div>                                
                        </div>
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
                    html += `
                        <div class="row bg_data_page">
                            <div class="col-md-12 bg_list_post">
                                <div class="image">
                                    <img src="${getPhotoUrl(item.tumb_berita)}" alt="Berita">
                                </div>
                                <div class="det_post">
                                    <div class="title_list">${item.judul_berita}</div>
                                    <div class="date_list"><i class="fa fa-clock-o"></i> Date::parse(${item.created_at})->format('l, j F Y')</div>
                                    <div class="btn_set">
                                        <div class="checkboxlios">
                                            <input type="text" name="status_data_${item.status_data}" value="${item.status_data}" style="display:none;" />
                                            <input type="checkbox" class="ios" name="btn_status_${item.code_data}" btn="btn_status_${item.code_data}" style="display:none;"/>
                                        </div>
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Atur</button>
                                            <div class="dropdown-menu">
                                                <h5 class="dropdown-header">Pengaturan Data</h5>

                                                <a class="dropdown-item" href="editpost?d=${item.code_data}">Lihat/Ubah Data</a>

                                                <a class="dropdown-item" btn="del_data_${item.code_data}">Hapus Data</a>
                                            </div>
                                        </div>
                                        
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
                                                    data-name="${item.nama_berita}">
                                                    Hapus Data
                                                </a>
                                            </div>
                                        </div>
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
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

            function getPhotoUrl(filename) {
                return filename
                    ? `/themes/admin/AdminOne/image/upload/${filename}`
                    : '{{ asset('/themes/admin/AdminOne/image/no_image.png') }}';
            }
        });
    </script>
@endsection

<!-- @section('script')
    <script>
        $(function () {

            /* ===================================================================
            * CONSTANTS & CONFIG
            * =================================================================== */
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

            /* ===================================================================
            * STATE
            * =================================================================== */
            const state = {
                page      : 1,
                search    : '',
                perPage   : parseInt($('#countvdajax').val()) || 10,
                sortBy    : 'created_at',
                sortOrder : 'asc',
                timeout   : null,
                mode      : 'list',   // list | add | edit | view
                currentCode: null
            };

            /* ===================================================================
            * HELPERS
            * =================================================================== */
            function getPhotoUrl(filename) {
                return filename
                    ? `/themes/admin/AdminOne/image/upload/${filename}`
                    : NO_IMAGE;
            }

            function formatTanggalIndonesia(dateString) {
                if (!dateString) return '-';

                const date    = new Date(dateString);
                const options = { timeZone: 'Asia/Jakarta' };

                const tanggal = date.toLocaleDateString('id-ID', {
                    ...options,
                    weekday : 'long',
                    day     : 'numeric',
                    month   : 'long',
                    year    : 'numeric'
                });

                const waktu = date.toLocaleTimeString('id-ID', {
                    ...options,
                    hour   : '2-digit',
                    minute : '2-digit',
                    second : '2-digit',
                    hour12 : false
                });

                return `${tanggal} - ${waktu}`;
            }

            /* ===================================================================
            * PANEL / NAVIGATION
            * =================================================================== */
            function showPanel(mode, code = null) {
                state.mode        = mode;
                state.currentCode = code;

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

            function renderHeader() {
                const titleMap = {
                    list : 'Data Berita',
                    add  : 'Tambah Data Berita',
                    edit : 'Ubah Data Berita',
                    view : 'Detail Data Berita'
                };

                $('#pageTitle').text(titleMap[state.mode] ?? 'Data Berita');

                let buttons = `
                    <button type="button" class="btn btn-secondary" onclick="BackPage()">
                        <i class="fa fa-chevron-left"></i> Kembali
                    </button>
                `;

                if (state.mode === 'list') {
                    if (ACTION.new) {
                        buttons += `
                            <button type="button" class="btn btn-primary" id="btnTambah">
                                <i class="fa fa-plus"></i> Tambah Data
                            </button>
                        `;
                    }
                    if (ACTION.export) {
                        buttons += `
                            <button type="button" class="btn btn-info"
                                onclick="exportdata({ url: '/admin/exportberita', btn: this })">
                                <i class="fa fa-download"></i> Export Data
                            </button>
                        `;
                    }
                }

                $('#headerActions').html(buttons);

                if (state.mode === 'list') {
                    $('#btnTambah').on('click', function () { showPanel('add'); });
                }
            }

            /* ===================================================================
            * FORM UTILITIES
            * =================================================================== */
            function resetForm() {
                $('#panel-form')
                    .find('input[type="text"], input[type="email"], input[type="date"], textarea')
                    .val('');

                $('#panel-form').find('select').val('').trigger('change');

                $('#field_photo_berita').val('');
                $('#field_code_data').val('');
                $('#preview_photo_berita').attr('src', NO_IMAGE);
                $('#photo_filename').text('Belum ada file dipilih');

                $('input[name="field_status"][value="Aktif"]').prop('checked', true);
            }

            function clearFormErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            /* ===================================================================
            * PHOTO DROPZONE
            * =================================================================== */
            (function initDropzone() {
                const dropzone  = $('#dropzonePhoto');
                const inputFile = $('#field_photo_berita');

                // Trigger file input
                $('#btnChoosePhoto').on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    inputFile.trigger('click');
                });

                dropzone.on('click', function () { inputFile.trigger('click'); });
                inputFile.on('click', function (e) { e.stopPropagation(); });

                // Drag & drop events
                dropzone.on('dragover', function (e) {
                    e.preventDefault();
                    dropzone.addClass('dragover');
                });

                dropzone.on('dragleave', function (e) {
                    e.preventDefault();
                    dropzone.removeClass('dragover');
                });

                dropzone.on('drop', function (e) {
                    e.preventDefault();
                    dropzone.removeClass('dragover');

                    const files = e.originalEvent.dataTransfer.files;
                    if (files.length) {
                        inputFile[0].files = files;
                        previewImage(files[0]);
                    }
                });

                inputFile.on('change', function () {
                    if (this.files && this.files[0]) {
                        previewImage(this.files[0]);
                    }
                });

                function previewImage(file) {
                    const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png'];
                    const MAX_SIZE_MB   = 2;

                    if (file.size > MAX_SIZE_MB * 1024 * 1024) {
                        $('#err_photo_berita').text(`Ukuran foto maksimal ${MAX_SIZE_MB}MB`);
                        inputFile.val('');
                        return;
                    }

                    if (!ALLOWED_TYPES.includes(file.type)) {
                        $('#err_photo_berita').text('Format harus JPG, JPEG, atau PNG');
                        inputFile.val('');
                        return;
                    }

                    $('#err_photo_berita').text('');
                    $('#photo_filename').text(file.name);

                    const reader    = new FileReader();
                    reader.onload   = (e) => $('#preview_photo_berita').attr('src', e.target.result);
                    reader.readAsDataURL(file);
                }
            })();

            /* ===================================================================
            * LOAD DETAIL (VIEW & EDIT)
            * =================================================================== */
            function loadDetail(code, target) {
                $.get(ROUTES.detail, { code_data: code }, function (res) {
                    const d = res.data;

                    if (target === 'view') {
                        $('#view_code_data').text(d.code_data ?? '-');
                        $('#view_judul').text(d.judul_berita ?? '-');
                        $('#view_isi').html(d.isi_berita ?? '-');
                        $('#view_sumber').text(d.sumber_berita ?? '-');
                        $('#view_foto').html(`
                            <img src="${getPhotoUrl(d.tumb_berita)}"
                                style="max-width:150px; border-radius:8px;">
                        `);
                        $('#view_created_at').text(formatTanggalIndonesia(d.created_at));
                        $('#view_updated_at').text(formatTanggalIndonesia(d.updated_at));
                        $('#btnEditFromView').data('code', d.code_data);
                    }

                    if (target === 'edit') {
                        $('#field_code_data').val(d.code_data);
                        $('#field_judul_berita').val(d.judul_berita);
                        $('#field_sumber_berita').val(d.sumber_berita);
                        $('#field_status_data').val(d.status_data);

                        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.field_isi_berita) {
                            CKEDITOR.instances.field_isi_berita.setData(d.isi_berita || '');
                        }

                        $('#preview_photo_berita').attr('src', getPhotoUrl(d.tumb_berita));
                        $('#photo_filename').text(d.tumb_berita || 'Belum ada file dipilih');
                    }
                }).fail(function () {
                    SystemToast('danger', 'Gagal memuat detail data');
                });
            }

            /* ===================================================================
            * SAVE FORM
            * =================================================================== */
            $('#btnSaveForm').on('click', function () {
                clearFormErrors();

                const isEdit   = state.mode === 'edit';
                const formData = new FormData();

                formData.append('code_data',    $('#field_code_data').val());
                formData.append('judul_berita', $('#field_judul_berita').val());
                formData.append('isi_berita',   $('#field_isi_berita').val());
                formData.append('sumber_berita','$('#field_sumber_berita').val());
                formData.append('status_data',  $('input[name="field_status"]:checked').val());

                const photo = $('#field_photo_berita')[0].files[0];
                if (photo) formData.append('photo_berita', photo);

                // Method spoofing untuk PUT
                if (isEdit) formData.append('_method', 'PUT');

                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

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
                        const errors = xhr.responseJSON?.errors || {};

                        Object.keys(errors).forEach(function (key) {
                            $(`#field_${key}`).addClass('is-invalid');
                            $(`#err_${key}`).text(errors[key][0]);
                        });

                        SystemToast('danger', xhr.responseJSON?.note || xhr.responseJSON?.message || 'Gagal menyimpan data');
                    },

                    complete: function () {
                        btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
                    }
                });
            });

            /* ===================================================================
            * FORM CANCEL & VIEW BUTTONS
            * =================================================================== */
            $('#btnCancelForm').on('click', function ()    { showPanel('list'); });
            $('#btnBackFromView').on('click', function ()  { showPanel('list'); });
            $('#btnEditFromView').on('click', function ()  { showPanel('edit', $(this).data('code')); });

            /* ===================================================================
            * LOAD DATA (TABLE LIST)
            * =================================================================== */
            function loadData(page = 1) {
                state.page = page;

                $.ajax({
                    url  : ROUTES.list,
                    type : 'GET',
                    data : {
                        page       : page,
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
                    error : renderError
                });
            }

            function renderLoading() {
                $('#dataTableBody').html(`
                    <div class="list_notif read not">
                        <div class="head text-center">
                            <i class="fa fa-spinner fa-spin"></i> Memuat data...
                        </div>
                    </div>
                `);
            }

            function renderError() {
                $('#dataTableBody').html(`
                    <div class="list_notif read not">
                        <div class="head text-center">Gagal memuat data</div>
                    </div>
                `);
            }

            function renderTable(res) {
                if (!res.data.length) {
                    $('#dataTableBody').html(`
                        <div class="list_notif read not">
                            <div class="head text-center">Tidak ada data</div>
                        </div>
                    `);
                    return;
                }

                const rows = res.data.map(function (item) {
                    const editDisabled   = !ACTION.edit   ? 'disabled text-muted' : '';
                    const deleteDisabled = !ACTION.delete ? 'disabled text-muted' : '';

                    return `
                        <div class="col-md-12 bg_list_post">
                            <div class="image">
                                <img src="${getPhotoUrl(item.tumb_berita)}" alt="Berita">
                            </div>
                            <div class="det_post">
                                <div class="title_list">${item.judul_berita}</div>
                                <div class="date_list">
                                    <i class="fa fa-clock-o"></i> ${formatTanggalIndonesia(item.created_at)}
                                </div>
                                <div class="btn_set">
                                    <div class="dropdown dropleft">
                                        <button class="btn btn-default btn-sm dropdown-toggle"
                                            data-toggle="dropdown">Atur</button>
                                        <div class="dropdown-menu">
                                            <h5 class="dropdown-header">Pengaturan Data</h5>
                                            <a class="dropdown-item btn-view"
                                                data-code="${item.code_data}">Lihat Data</a>
                                            <a class="dropdown-item btn-edit ${editDisabled}"
                                                data-code="${item.code_data}">Ubah Data</a>
                                            <a class="dropdown-item delete-data ${deleteDisabled}"
                                                data-code="${item.code_data}"
                                                data-name="${item.judul_berita}">Hapus Data</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#dataTableBody').html(rows.join(''));
                $('.ios').iosCheckbox();
            }

            /* ===================================================================
            * PAGINATION
            * =================================================================== */
            function renderPagination(res) {
                const prevPage = res.current_page > 1              ? res.current_page - 1 : null;
                const nextPage = res.current_page < res.last_page  ? res.current_page + 1 : null;

                $('#totalData').text(res.total);
                $('#currentPageText').text(res.current_page);
                $('#prevPageText').text(prevPage ?? '-');
                $('#nextPageText').text(nextPage ?? '-');

                toggleButton('#btnFirst',    1,             res.current_page === 1);
                toggleButton('#btnPrevPage', prevPage,      !prevPage);
                toggleButton('#btnNextPage', nextPage,      !nextPage);
                toggleButton('#btnLast',     res.last_page, res.current_page === res.last_page);

                $('#btnPrevPage').toggle(!!prevPage);
                $('#btnNextPage').toggle(!!nextPage);
            }

            function toggleButton(selector, page, disabled) {
                $(selector).data('page', page).prop('disabled', disabled);
            }

            /* ===================================================================
            * DELEGATED EVENTS
            * =================================================================== */

            // Search
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

            // Pagination buttons
            $(document).on('click', '#btnFirst, #btnPrevPage, #btnNextPage, #btnLast', function (e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).prop('disabled')) return false;
                loadData(page);
            });

            // Sort columns
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

            // View / Edit buttons
            $(document).on('click', '.btn-view', function () { showPanel('view', $(this).data('code')); });
            $(document).on('click', '.btn-edit', function () { showPanel('edit', $(this).data('code')); });

            // Status toggle
            $(document).on('change', '.status-toggle', function () {
                const checkbox = $(this);
                const code     = checkbox.data('code');
                const status   = checkbox.data('status');

                checkbox.prop('disabled', true);

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
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        SystemToast('danger', xhr.responseJSON?.note || 'Status gagal diperbarui');
                    },
                    complete: function () {
                        checkbox.prop('disabled', false);
                    }
                });
            });

            // Delete
            $(document).on('click', '.delete-data:not(.disabled)', function () {
                const code  = $(this).data('code');
                const name  = $(this).data('name');
                const modal = $('div[data-model="confirmasi"]');

                modal.modal({ backdrop: false });
                modal.find('.modal-body').html(`
                    <div class="alert alert-danger">
                        Anda yakin ingin menghapus data <b>${name}</b>?
                    </div>
                `);

                $('button[btn-action="action-confirmasi"]').remove();
                $('button[btn-action="close-confirmasi"]').before(`
                    <button type="button" class="btn btn-primary btn-sm"
                        btn-action="action-confirmasi">Yakin</button>
                `);

                $(document)
                    .off('click', '[btn-action="action-confirmasi"]')
                    .on('click', '[btn-action="action-confirmasi"]', function () {
                        const btn = $(this);
                        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menghapus...');

                        $.ajax({
                            url  : ROUTES.delete,
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

            // Prevent click pada item disabled
            $(document).on('click', '.dropdown-item.disabled', function (e) {
                e.preventDefault();
                e.stopPropagation();
            });

            /* ===================================================================
            * INIT
            * =================================================================== */
            showPanel('list');
            loadData(1);

        });
    </script>
@endsection -->