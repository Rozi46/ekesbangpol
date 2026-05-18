@extends('admin.AdminOne.layout.assets')
@section('title', 'Hasil Pertandingan')

@section('content')

	<div class="page_main">
		<div class="container-fluid text-left">
			<div class="row">
				<div class="col-md-12 bg_page_main hd" line="hd_action">
					<div class="col-md-12 hd_page_main">Hasil Pertandingan</div>
					<div class="col-md-12 bg_act_page_main">
						<div class="row">
							<div class="col-xl-12 col_act_page_main text-left">
								<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
								@if($level_user['historyresult'] == 'Yes')
									<a href="/admin/historyresult"><button type="button" class="btn btn-success" btn="history_data">History Hasil Pertandingan</button></a>
								@endif
								@if($level_user['inputresult'] == 'Yes')
									<button type="button" class="btn btn-primary" name="btn_save">Simpan Data</button>
								@endif
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-12 bg_page_main form_action" line="form_action">
					<div class="col-md-12 data_page">
						<form method="post" name="form_data" enctype="multipart/form-data" action="/admin/saveregister">
							{{ csrf_field() }}
							<div class="row bg_data_page form_page content">
								<div class="col-md-12 bg_form_page">
									<div class="form-group row form_input text-left">
										<label class="col-sm-2 col-form-label">Nama Kejuaraan <span>*</span></label>
										<div class="col-sm-10 input">
											<select id="code_championship" name="code_championship">
												<option value="">Pilih Kejuaraan</option>
												@foreach ($list_championship as $view_championship)
													<option value="{{ $view_championship['code_data'] }}">{{ $view_championship['nama_kejuaraan'] }}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-12 bg_form_page">
									<div class="form-group row form_input text-left">
										<label class="col-sm-2 col-form-label">Nomor Lomba <span>*</span></label>
										<div class="col-sm-10 input">
											<select id="code_event" name="code_event">
												<option value="">Pilih Nomor Lomba</option>
											</select>
										</div>
									</div>
								</div>
							</div> 
						</form>
					</div>
				</div>
                <div class="col-md-12 bg_page_main form_action">
                    <div class="col-md-12 data_page view">
                        <div class="row bg_data_page" style="padding-left: 5px;padding-right: 5px;padding-bottom: 5px;">
                            <div class="table_data transaksi">
                                <table class="table_view table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width:30px; text-align: center;">No</th>
                                            <th style="min-width:150; text-align: center;">Seri Lomba</th>
                                            <th style="min-width:150px; text-align: center;">Nama Atlet</th>
                                            <th style="min-width:150px; text-align: center;">Line Number</th>
                                            <th style="min-width:150px; text-align: center;">Best Time</th>
                                            <th style="min-width:150px; text-align: center;">Hasil</th>
                                            <th style="min-width:150px; text-align: center;">Foto Hasil</th>
                                            <th style="min-width:150px; text-align: center;">Ranking</th>
                                            <th style="min-width:150px; text-align: center;">Point</th>
                                        </tr>
                                    </thead>
                                    <tbody line="list_data">
                                        <tr>
                                            <td style="text-align:center; padding: 20px; background-color: #FFFFFF; cursor: default; font-weight: 600; height: 300px; font-size: 14px;" colspan="20" >
                                                <i class="fa fa-shopping-bag"></i>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</div>

	@section('script')
		<script type="text/javascript">
			$(document).ready(function(){

				// Inisialisasi awal
				$('button[name="btn_save"]').prop('disabled', true);
				$('form :input').not('button, [type=button], [type=submit]').prop('disabled', true);

				@if($level_user['inputresult'] == 'Yes')
					$('form :input').not('button, [type=button], [type=submit]').prop('disabled', false);
					$('form :input').on('input change', checkFormInputs);
					checkFormInputs();
				@endif 
				
				$('#code_event').prop('disabled', true);

				// Select2
				$('#code_championship').select2({
					placeholder: 'Pilih Kejuaraan',
					allowClear: true,
					width: '100%'
				});

				$('#code_event').select2({
					placeholder: 'Pilih Nomor Lomba',
					allowClear: true,
					width: '100%'
				});

				$('#code_championship').on('change', function () {
					let code_championship = $(this).val();
					let $eventSelect = $('#code_event');

					if (!code_championship) {
						$eventSelect
							.empty()
							.append('<option value="">Pilih Nomor Lomba</option>')
							.prop('disabled', true);
						return;
					}

					$eventSelect
						.empty()
						.append('<option value="">Mengambil data...</option>')
						.prop('disabled', true);

					$.ajax({
						url: '/admin/geteventresult',
						type: 'GET',
						data: { code_championship },
						dataType: 'json',
						success: function (results) {
							$eventSelect.empty();

							if (results.length === 0) {
								$eventSelect.append('<option value="">Tidak ada tersedia</option>');
							} else {
								$eventSelect.append('<option value="">Pilih Nomor Lomba</option>');
								$.each(results, function (i, val) {
									$eventSelect.append(
										`<option value="${val.code_data}">${val.code_event}</option>`
									);
								});
							}

							$eventSelect.prop('disabled', false);
						},
						error: function () {
							$eventSelect
								.empty()
								.append('<option value="">Gagal mengambil data</option>')
								.prop('disabled', true);

							alert('❌ Gagal mengambil data event.');
						}
					});
				});
                        
				$('#code_event').on('change', function() {
					let code_event = $(this).val();
					let code_championship = $('#code_championship').val();

					$('[line="list_data"]').html('<tr><td style="text-align:center; padding: 20px; background-color: #FFFFFF; cursor: default; font-weight: 600; height: 300px; font-size: 14px;" colspan="20"><div class="col-md-12 load_data_i text-center"> <div class="spinner-grow spinner-grow-sm text-muted"></div> <div class="spinner-grow spinner-grow-sm text-secondary"></div> <div class="spinner-grow spinner-grow-sm text-dark"></div></div></td></tr>');
					$.get("/admin/listdataevent",{code_event:code_event,code_championship:code_championship},function(listdata){
						$('[line="list_data"]').html(listdata);
					});
				});

				// Tombol Simpan
				$('button[name="btn_save"]').click(function() {
					// let nama_event = $('input[name="nama_event"]').val();
					$('select[name="nama_mekanik"]').val(@json( array_keys($results['results']['detail_mekanik'] ?? [] ))).trigger('change'); 
					$('div[data-model="confirmasi"]').modal({backdrop: false});
					$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-warning">Anda yakin untuk simpan data ini?</div>');
					$('button[btn-action="action-confirmasi"]').remove();
					$('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>');

					$('button[btn-action="action-confirmasi"]').click(function() {
						loadingpage(20000);
						$('form[name="form_data"]').submit();
					});
				});
			});

			// Fungsi cek input
			// function checkFormInputs() {
			// 	let isComplete = true;
			// 	$('form :input').each(function () {
			// 		if (
			// 			$(this).is(':visible') &&
			// 			!$(this).is(':disabled') &&
			// 			$(this).attr('type') !== 'hidden' &&
			// 			$(this).attr('type') !== 'button' &&
			// 			$(this).attr('type') !== 'submit'
			// 		) {
			// 			if (!$(this).val().trim()) {
			// 				isComplete = false;
			// 				return false;
			// 			}
			// 		}
			// 	});
			// 	$('button[name="btn_save"]').prop('disabled', !isComplete);
			// }

			function checkFormInputs() {
				let championship = $('#code_championship').val();
				let event = $('#code_event').val();

				let isComplete = championship && event;
				$('button[name="btn_save"]').prop('disabled', !isComplete);
			}

			// Jalankan pengecekan setiap kali dropdown berubah
			$('#code_championship, #code_event').on('change', checkFormInputs);
		</script>
	@endsection

@endsection
