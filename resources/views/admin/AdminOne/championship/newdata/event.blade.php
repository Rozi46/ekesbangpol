@extends('admin.AdminOne.layout.assets')
@section('title', 'Tambah Nomor Lomba')

@section('content')

			<div class="page_main">
                <div class="container-fluid text-left">
					<div class="row">
						<div class="col-md-12 bg_page_main hd" line="hd_action">
							<div class="col-md-12 hd_page_main">Tambah Nomor Lomba</div>
							<div class="col-md-12 bg_act_page_main">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-left">
										<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
										@if($level_user['newevent'] == 'Yes')<button type="button" class="btn btn-primary" name="btn_save">Simpan Data</button>@endif
									</div>
								</div>
							</div>
						</div>
                        <div class="col-md-12 bg_page_main form_action" line="form_action">
							<div class="col-md-12 data_page">
								<form method="post" name="form_data" enctype="multipart/form-data" action="/admin/newevent">
									{{csrf_field()}}
									<div class="row bg_data_page form_page content">
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="code_event" class="col-sm-2 col-form-label">Nomor Lomba <span>*</span></label>
												<div class="col-sm-10 input">
													<input type="text" name="code_event" placeholder="Nomor Lomba" value="{{ old('code_event') }}" >
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="jarak" class="col-sm-2 col-form-label">Jarak (M)<span>*</span></label>
												<div class="col-sm-10 input">
													<input type="text" name="jarak" placeholder="0" value="" onKeyPress="return goodchars(event,'0123456789',this)" />
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="code_gaya" class="col-sm-2 col-form-label">Kategori <span>*</span></label>
												<div class="col-sm-10 input">
                                                    <select id="code_gaya" name="code_gaya" placeholder="Pilih Kategori">	
                                                        <option value="" selected="true">Pilih Kategori</option>
                                                        @foreach ($list_gaya as $view_gaya)
                                                            <option value="{{$view_gaya['code_data']}}">{{$view_gaya['nama_gaya']}}</option>
                                                        @endforeach
                                                    </select>
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="code_kategori" class="col-sm-2 col-form-label">Kelompok Umur <span>*</span></label>
												<div class="col-sm-10 input">
                                                    <select id="code_kategori" name="code_kategori" placeholder="Pilih Kelompok Umur">	
                                                        <option value="" selected="true">Pilih Kelompok Umur</option>
                                                        @foreach ($list_ku as $view_ku)
                                                            <option value="{{$view_ku['code_data']}}">{{$view_ku['code_kelompok']}}</option>
                                                        @endforeach
                                                    </select>
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="gender" class="col-sm-2 col-form-label">Gender <span>*</span></label>
												<div class="col-sm-10 input">
                                                    <select name="gender" placeholder="Pilih Gender">
                                                        <option value="" style="display:none;">Pilih Gender</option>
                                                        <option value="Putra">Putra</option>
                                                        <option value="Putri">Putri</option>
                                                    </select>
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="tanggal" class="col-sm-2 col-form-label">Waktu Pelaksanaan <span>*</span></label>
												<div class="col-sm-10 input">
                                                    <input type="date" class="form-control" name="tanggal">
												</div>
											</div>
										</div>
										<div class="col-md-12 bg_form_page">
											<div class="form-group row form_input text-left">
												<label for="code_kejuaraan" class="col-sm-2 col-form-label">Kejuaraan <span>*</span></label>
												<div class="col-sm-10 input">
                                                    <select id="code_kejuaraan" name="code_kejuaraan" placeholder="Pilih Kejuaraan">	
                                                        <option value="" selected="true">Pilih Kejuaraan</option>
                                                        @foreach ($list_championship as $view_championship)
                                                            <option value="{{$view_championship['code_data']}}">{{$view_championship['nama_kejuaraan']}}</option>
                                                        @endforeach
                                                    </select>
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

                        @if($level_user['newevent'] == 'Yes')
                            $('form :input').not('button, [type=button], [type=submit]').prop('disabled', false);

                            $('form :input').on('input change', function () {
                                checkFormInputs();
                            });
                            checkFormInputs();
                        @endif 
                
                        $('input[name="tanggal"]').change(function(){
                            var tanggal = $(this).val();
                            $('input[name="tanggal"]').val(tanggal);
                        });

                        $('#code_gaya').select2({
                            placeholder: 'Pilih Kategori',
                            allowClear: true,
                            width: '100%'
                        });

                        $('#code_kategori').select2({
                            placeholder: 'Pilih Kelompok Umur',
                            allowClear: true,
                            width: '100%'
                        });

                        $('#code_kejuaraan').select2({
                            placeholder: 'Pilih Kejuaraan',
                            allowClear: true,
                            width: '100%'
                        });
										
						$('button[name="btn_save"]').click(function(){
							var code_event = $('input[name="code_event"]').val();
							if($(('button[name="btn_save"]')).click){
								$('div[data-model="confirmasi"]').modal({backdrop: false});
								$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-warning">Anda yakin untuk simpan data ' + code_event + '.</div>');
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