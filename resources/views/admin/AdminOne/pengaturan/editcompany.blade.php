@extends('admin.AdminOne.layout.assets')
@section('title', 'Ubah Data Perusahaan')

@section('content')

			<div class="page_main">
                <div class="container-fluid text-left">
					<div class="row">
						<div class="col-md-12 bg_page_main hd" line="hd_action">
							<div class="col-md-12 hd_page_main">Ubah Data Perusahaan</div>
							<div class="col-md-12 bg_act_page_main">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-left">
										<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
                                            @if($res_user['level'] == 'LV5677001')
												<button type="button" class="btn btn-primary" name="btn_save"><i class="fa fa-save"></i> Simpan Data</button>

												@if($results['count_used'] == 0)<button type="button" class="btn btn-danger" name="btn_del" onclick="DeleteData()">Hapus Data</button>@endif 
											@endif
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 bg_page_main dt" line="form_action">
							<div class="col-md-12 data_page">
								<form method="post" name="form_data" enctype="multipart/form-data" action="/admin/editcompany">
									{{csrf_field()}}
									<div class="row bg_data_page form_page content">
										<input type="text" name="id_data" value="{{ $results['results']['nama_company']['id'] }}" readonly="true" style="display: none;" />
										<div class="col-md-6 bg_form_page">
											<div class="col-md-12 bg_form_page">
												<div class="form_input text-left">
													<div class="tag_title">Kode <span>*</span></div>
													<input type="text" name="code_company" placeholder="Kode Company" value="{{ $results['results']['nama_company']['code_data'] }}" @if($results['count_used'] > 0) readonly="true" @endif />
												</div>
											</div>
											<div class="col-md-12 bg_form_page">
												<div class="form_input text-left">
													<div class="tag_title">Nama <span>*</span></div>
													<input type="text" name="nama_company" placeholder="Nama Company" value="{{ $results['results']['nama_company']['nama_company'] }}" autofocus/>
												</div>
											</div>
											<div class="col-md-12 bg_form_page">
												<div class="form_input text-left">
													<div class="tag_title">Jenis <span>*</span></div>
													<input type="text" name="jenis_company" placeholder="Jenis Company" value="{{ $results['results']['nama_company']['jenis'] }}"/>
												</div>
											</div>
											<div class="col-md-12 bg_form_page">
												<div class="form_input text-left">
													<div class="tag_title">Alamat <span>*</span></div>
													<input type="text" name="alamat_company" placeholder="Alamat Company" value="{{ $results['results']['nama_company']['alamat'] }}"/>
												</div>
											</div>
											<div class="col-md-12 bg_form_page">
												<div class="form_input text-left">
													<div class="tag_title">Email <span>*</span></div>
													<input type="email" name="email_company" placeholder="Email Company" value="{{ $results['results']['nama_company']['email'] }}"/>
												</div>
											</div>
										</div>
										<div class="col-md-6 bg_form_page">
											<div class="form_input text-left">
												<div class="tag_title">Logo Perusahaan</div>
                                                    <img 
                                                        src="{{ $results['results']['nama_company']['foto'] ? asset('/image/company/'.$results['results']['nama_company']['foto']) : asset('/image/setting/icon.png') }}" 
                                                        alt="Logo" srcimg="logo_company" onclick="OpenFile('form_data','logo_company')">

												<input type="file" accept="image/*" name="logo_company" placeholder="Logo Company"/>
												<div class="btn_200">
													<button type="button" class="btn btn-secondary" onclick="OpenFile('form_data','logo_company')">Upload Logo</button>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>

			@section('script')
                <script type="text/javascript">
                    $(document).ready(function(){							
                        // Default: semua tombol disabled
                        $('button[name="btn_save"], button[name="btn_del"]').prop('disabled', true);

                        // Default: disable semua input form KECUALI button
                        $('form :input').not('button, [type=button], [type=submit]').prop('disabled', true);

                        // Jika punya akses editcompany
                        @if($res_user['level'] == 'LV5677001')
                            $('form :input').not('button, [type=button], [type=submit]').prop('disabled', false);

                            // Trigger pengecekan input hanya jika user boleh edit
                            $('form :input').on('input change', function () {
                                checkFormInputs();
                            });
                            checkFormInputs();
                        @endif 

						$('button[name="btn_save"]').click(function(){
							var nama_company = $('input[name="nama_company"]').val();
							if($(('button[name="btn_save"]')).click){
								$('div[data-model="confirmasi"]').modal({backdrop: false});
								$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-warning">Anda yakin untuk simpan data ' + nama_company + '.</div>');
								$('button[btn-action="action-confirmasi"]').remove();
								$('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>');
								$('button[btn-action="action-confirmasi"]').click(function(){
									if($('button[btn-action="action-confirmasi"]').click){
										$('button[btn-action="action-confirmasi"]').remove();
										$('button[btn-action="close-confirmasi"]').remove();
										$('form[name="form_data"]').submit();  
									}
								});
							}
						});
                    });
					
                    function DeleteData() {
                        $('div[data-model="confirmasi"]').modal({backdrop: false});
                        $('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-danger">Anda yakin untuk menghapus data {{ $results['results']['nama_company']['nama_company'] }}.</div>');
                        $('button[btn-action="action-confirmasi"]').remove();
                        $('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>');
                        $('button[btn-action="action-confirmasi"]').click(function(){
                            if($('button[btn-action="action-confirmasi"]').click){
								$('button[btn-action="action-confirmasi"]').remove();
								$('button[btn-action="close-confirmasi"]').remove();
                                loadingpage(20000);
                                window.location.href = "/admin/deletecompany?d={{ $results['results']['nama_company']['id'] }}";
                            }
                        });
                    }
					
					function checkFormInputs() {
						let isComplete = true;
						$('form :input').each(function () {
							if (
								$(this).is(':visible') &&
								!$(this).is(':disabled') &&
								$(this).attr('type') !== 'hidden' &&
								$(this).attr('type') !== 'button' &&
								$(this).attr('type') !== 'submit'
							) {
								if (!$(this).val().trim()) {
									isComplete = false;
									return false;
								}
							}
						});

                        // Tombol hanya enable jika semua input complete
                        $('button[name="btn_save"], button[name="btn_del"]').prop('disabled', !isComplete);
					}
                </script>
            @endsection
@endsection