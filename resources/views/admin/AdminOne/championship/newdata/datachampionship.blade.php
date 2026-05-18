@extends('admin.AdminOne.layout.assets')
@section('title', 'Tambah Data Kejuaraan')

@section('content')

			<div class="page_main">
                <div class="container-fluid text-left">
					<div class="row">
						<div class="col-md-12 bg_page_main hd" line="hd_action">
							<div class="col-md-12 hd_page_main">Tambah Data Kejuaraan</div>
							<div class="col-md-12 bg_act_page_main">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-left">
										<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
										@if($level_user['newchampionship'] == 'Yes')<button type="button" class="btn btn-primary" name="btn_save">Simpan Data</button>@endif
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 bg_page_main form_action" line="form_action">
							<div class="col-md-12 data_page">
								<form method="post" name="form_data" enctype="multipart/form-data" action="/admin/newchampionship">
									{{csrf_field()}}
									<div class="row bg_data_page form_page content">
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="nama_kejuaraan" class="col-sm-2 col-form-label">Nama Kejuaraan <span>*</span></label>
												<div class="col-sm-10 input">
													<input type="text" name="nama_kejuaraan" placeholder="Nama Kejuaraan" value="{{ old('nama_kejuaraan') }}" autofocus/>
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="lokasi" class="col-sm-2 col-form-label">Lokasi <span>*</span></label>
												<div class="col-sm-10 input">
													<input type="text" name="lokasi" placeholder="Lokasi" value="{{ old('lokasi') }}" >
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="jumlah_line" class="col-sm-2 col-form-label">Junlah Line <span>*</span></label>
												<div class="col-sm-10 input">
													<input type="text" name="jumlah_line" placeholder="0" value="" onKeyPress="return goodchars(event,'0123456789',this)" />
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="tanggal_mulai" class="col-sm-2 col-form-label">Tanggal Mulai <span>*</span></label>
												<div class="col-sm-10 input">
                                                    <input type="date" class="form-control" name="tanggal_mulai">
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="tanggal_selesai" class="col-sm-2 col-form-label">Tanggal Selesai <span>*</span></label>
												<div class="col-sm-10 input">
                                                    <input type="date" class="form-control" name="tanggal_selesai">
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
                        $('form :input').not('button, [type=button], [type=submit]').prop('disabled', true);

                        @if($level_user['newchampionship'] == 'Yes')
                            $('form :input').not('button, [type=button], [type=submit]').prop('disabled', false);

                            $('form :input').on('input change', function () {
                                checkFormInputs();
                            });
                            checkFormInputs();
                        @endif 
                
                        $('input[name="tanggal_mulai"]').change(function(){
                            var value = $(this).val();
                            $('input[name="tanggal_mulai"]').val(value);
                        });
                
                        $('input[name="tanggal_selesai"]').change(function(){
                            var value = $(this).val();
                            $('input[name="tanggal_selesai"]').val(value);
                        });
										
						$('button[name="btn_save"]').click(function(){
							var nama_kejuaraan = $('input[name="nama_kejuaraan"]').val();
							if($(('button[name="btn_save"]')).click){
								$('div[data-model="confirmasi"]').modal({backdrop: false});
								$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-warning">Anda yakin untuk simpan data ' + nama_kejuaraan + '.</div>');
								$('button[btn-action="action-confirmasi"]').remove();
								$('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>');
								$('button[btn-action="action-confirmasi"]').click(function(){
									if($('button[btn-action="action-confirmasi"]').click){
										$('button[btn-action="action-confirmasi"]').remove();
										$('button[btn-action="close-confirmasi"]').remove();
                                        loadingpage(20000);
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