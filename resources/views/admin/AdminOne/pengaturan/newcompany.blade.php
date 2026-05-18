@extends('admin.AdminOne.layout.assets')
@section('title', 'Tambah Data Perusahaan')

@section('content')

			<div class="page_main">
                <div class="container-fluid text-left">
					<div class="row">
						<div class="col-md-12 bg_page_main hd" line="hd_action">
							<div class="col-md-12 hd_page_main">Tambah Data Perusahaan</div>
							<div class="col-md-12 bg_act_page_main">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-left">
										<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
										<!-- <button type="button" class="btn btn-primary" name="btn_save" onclick="loadingpage(20000),SaveData('form_data')">Simpan Data</button> -->
                                        <button type="button" class="btn btn-primary" name="btn_save"><i class="fa fa-save"></i> Simpan Data</button>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 bg_page_main dt" line="form_action">
							<div class="col-md-12 data_page">
								<form method="post" name="form_data" enctype="multipart/form-data" action="/admin/savecompany">
									{{csrf_field()}}
									<div class="row bg_data_page form_page content">
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="nama" class="col-sm-2 col-form-label">Nama Perusahaan <span>*</span></label>
												<div class="col-sm-10 input">
													<input type="text" name="nama" placeholder="Nama Perusahaan" value="" >
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="jenis" class="col-sm-2 col-form-label">Jenis <span>*</span></label>
												<div class="col-sm-10 input">
													<input type="text" name="jenis" placeholder="Jenis Perusahaan" value="" >
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="alamat" class="col-sm-2 col-form-label">Alamat <span>*</span></label>
												<div class="col-sm-10 input">
													<input type="text" name="alamat" placeholder="Alamat" value="" >
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="email" class="col-sm-2 col-form-label">Email <span>*</span></label>
												<div class="col-sm-10 input">
													<input type="text" name="email" placeholder="Email" value="" >
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
						$('button[name="btn_save"]').prop('disabled', true);
						$('form :input').on('input change', function () {
							checkFormInputs();
						});
						checkFormInputs();

						$('form :input').prop('disabled', true);       
						@if($res_user['level'] == 'LV5677001')
							$('form :input').prop('disabled', false);
						@endif  

						$('button[name="btn_save"]').click(function(){
							var nama_perusahaan = $('input[name="nama"]').val();
							if($(('button[name="btn_save"]')).click){
								$('div[data-model="confirmasi"]').modal({backdrop: false});
								$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-warning">Anda yakin untuk simpan data perusahaan ' + nama_perusahaan + '.</div>');
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
						$('button[name="btn_save"]').prop('disabled', !isComplete);
					}
				</script>
            @endsection
@endsection