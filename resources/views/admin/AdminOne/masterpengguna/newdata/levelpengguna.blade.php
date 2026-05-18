@extends('admin.AdminOne.layout.assets')
@section('title', 'Tambah Level Pengguna')

@section('content')

			<div class="page_main">
				<div class="container-fluid text-left">
					<div class="row">
						<div class="col-md-12 bg_page_main hd" line="hd_action">
							<div class="col-md-12 hd_page_main">Tambah Level Pengguna</div>
							<div class="col-md-12 bg_act_page_main">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-left">
										<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
										@if($level_user['newlevelusers'] == 'Yes')<button type="button" class="btn btn-primary" name="btn_save" ><i class="fa fa-save"></i> Simpan Data</button>@endif
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 bg_page_main dt" line="form_action">
							<div class="col-md-12 data_page">
								<form method="post" name="form_data" enctype="multipart/form-data" action="/admin/actionlevel">
									{{csrf_field()}}
									<div class="row bg_data_page form_page content">
										<input type="text" name="code_data" value="" readonly="true" style="display:none;" />
										<div class="col-md-12 bg_form_page">
											<div class="form_input text-left">
												<div class="tag_title">Nama Level <span>*</span></div>
												<input type="text" name="level_name" placeholder="Nama Level" value="{{ old('level_name') }}" autofocus/>
											</div>
										</div>
										<div class="col-md-12 bg_form_page"> 
											@foreach ($list_akses['menu'] as $view_menu)
												<div class="col-md-12 col_level">
													<div class="bg_level">
														<div class="level">
															<i class="fa fa-caret-right"></i> Menu {{$view_menu['nama_menu']}}
														</div>
														<div class="checkboxlevel">
															<input type="text" name="{{$view_menu['nama_akses']}}" value="No" style="display: none;" />
															<input type="checkbox" class="ios" name_level="{{$view_menu['nama_akses']}}" line="btn_level"/>
														</div>
													</div>
												</div>
												<div class="col-md-12 col_level submenu" datamenu="{{$view_menu['nama_akses']}}">
													@foreach ($list_akses['submenu'][$view_menu['id']] as $view_submenu)
														<div class="bg_level">
															<div class="level">
																<i class="fa fa-caret-right"></i>
																<?php if($view_menu['nama_akses'] != 'mutasistockproduk'){?>Menu<?php } ?> {{$view_submenu['nama_menu']}}
															</div>
															<div class="checkboxlevel">
																<input type="text" name="{{$view_submenu['nama_akses']}}" value="No" style="display: none;" />
																<input type="checkbox" class="ios" name_level="{{$view_submenu['nama_akses']}}" line="btn_level"/>
															</div>
														</div>
														<div class="col-md-12 col_level btnaction" datamenu="{{$view_submenu['nama_akses']}}">
															@foreach ($list_akses['action'][$view_submenu['id']] as $view_action)
																<div class="bg_level">
																	<div class="level">
																		<i class="fa fa-caret-right"></i> {{$view_action['nama_menu']}}
																	</div>
																	<div class="checkboxlevel">
																		<input type="text" name="{{$view_action['nama_akses']}}" value="No" style="display: none;" />
																		<input type="checkbox" class="ios" name_level="{{$view_action['nama_akses']}}" line="btn_level"/>
																	</div>
																</div>
																<div class="col-md-12 col_level btnsubaction" datamenu="{{$view_action['nama_akses']}}">
																	@foreach ($list_akses['subaction'][$view_action['id']] as $view_subaction)
																		<div class="bg_level">
																			<div class="level">
																				<i class="fa fa-caret-right"></i> {{$view_subaction['nama_menu']}}
																			</div>
																			<div class="checkboxlevel">
																				<input type="text" name="{{$view_subaction['nama_akses']}}" value="No" style="display: none;" />
																				<input type="checkbox" class="ios" name_level="{{$view_subaction['nama_akses']}}" line="btn_level"/>
																			</div>
																		</div>
																	@endforeach
																</div>
															@endforeach
														</div>
													@endforeach
												</div>
											@endforeach
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
						@if($level_user['newlevelusers'] == 'Yes')
							$('form :input').prop('disabled', false);
						@endif
										
						$('button[name="btn_save"]').click(function(){
							var level_name = $('input[name="level_name"]').val();
							if($(('button[name="btn_save"]')).click){
								$('div[data-model="confirmasi"]').modal({backdrop: false});
								$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-warning">Anda yakin untuk simpan data ' + level_name + '.</div>');
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

						$(".ios").iosCheckbox();
						$('input[line="btn_level"]').on('click', function(){
							if($(this).is(':checked')){
								$('input[name="'+$(this).attr("name_level")+'"]').val('No');
								$('[datamenu="'+$(this).attr("name_level")+'"]').hide();
								$('[datasubmenu="'+$(this).attr("name_level")+'"]').hide();
							}else{
								$('input[name="'+$(this).attr("name_level")+'"]').val('Yes');
								$('[datamenu="'+$(this).attr("name_level")+'"]').show();
								$('[datasubmenu="'+$(this).attr("name_level")+'"]').show();
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