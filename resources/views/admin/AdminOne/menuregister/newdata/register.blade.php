@extends('admin.AdminOne.layout.assets')
@section('title', 'Pendaftaran Peserta')

@section('content')

	<div class="page_main">
		<div class="container-fluid text-left">
			<div class="row">
				<div class="col-md-12 bg_page_main hd" line="hd_action">
					<div class="col-md-12 hd_page_main">Pendaftaran Peserta</div>
					<div class="col-md-12 bg_act_page_main">
						<div class="row">
							<div class="col-xl-12 col_act_page_main text-left">
								<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
								@if($level_user['histroryregister'] == 'Yes')
									<a href="histroryregister"><button type="button" class="btn btn-success" btn="history_data">History Pendaftaran</button></a>
								@endif
								@if($level_user['inputregister'] == 'Yes')
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
										<label class="col-sm-2 col-form-label">Nama Club <span>*</span></label>
										<div class="col-sm-10 input">
											<select id="code_club" name="code_club">
												<option value="">Pilih Club</option>
												@foreach ($list_club as $view_data)
													<option value="{{ $view_data['code_data'] }}">{{ $view_data['nama_club'] }}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-12 bg_form_page">
									<div class="form-group row form_input text-left">
										<label class="col-sm-2 col-form-label">Nama Atlet <span>*</span></label>
										<div class="col-sm-10 input">
											<select id="code_atlete" name="code_atlete">
												<option value="">Pilih Atlet</option>
											</select>
										</div>
									</div>
								</div>
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
											<select id="code_event" name="code_event[]" multiple>
												<option value="">Pilih Nomor Lomba</option>
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

				// Inisialisasi awal
				$('button[name="btn_save"]').prop('disabled', true);
				$('form :input').not('button, [type=button], [type=submit]').prop('disabled', true);

				@if($level_user['inputregister'] == 'Yes')
					$('form :input').not('button, [type=button], [type=submit]').prop('disabled', false);
					$('form :input').on('input change', checkFormInputs);
					checkFormInputs();
				@endif 
				
				$('#code_atlete, #code_championship, #code_event').prop('disabled', true);

				// Select2
				$('#code_club').select2({
					placeholder: 'Pilih Club',
					allowClear: true,
					width: '100%'
				});

				$('#code_atlete').select2({
					placeholder: 'Pilih Atlet',
					allowClear: true,
					width: '100%'
				});

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

				// 1️⃣ CLUB → ATLET
				$('#code_club').on('change', function() {
					let code_club = $(this).val();
					let $atleteSelect = $('#code_atlete');

					// Reset dropdown berikutnya
					$('#code_championship, #code_event').val('').trigger('change').prop('disabled', true);

					if (!code_club) {
						$atleteSelect.empty().append('<option value="">Pilih Atlet</option>').trigger('change').prop('disabled', true);
						return;
					}

					// Tampilkan "Mengambil data..."
					$atleteSelect.empty().append('<option value="">Mengambil data...</option>').trigger('change').prop('disabled', true);

					$.ajax({
						url: '/admin/getatlete',
						type: 'GET',
						data: { code_club: code_club },
						dataType: 'json',
						success: function(results) {
							$atleteSelect.empty();

							if (results.length === 0) {
								$atleteSelect.append('<option value="">Tidak ada atlet tersedia</option>');
							} else {
								$atleteSelect.append('<option value="">Pilih Atlet</option>');
								$.each(results, function(index, val) {
									$atleteSelect.append('<option value="' + val.code_data + '">' + val.nama + '</option>');
								});
							}

							// Aktifkan select atlet
							$atleteSelect.prop('disabled', false).trigger('change');
						},
						error: function() {
							$atleteSelect.empty().append('<option value="">Gagal mengambil data</option>').trigger('change').prop('disabled', true);
							alert('❌ Gagal mengambil data atlet. Pastikan URL getatlete berfungsi.');
						}
					});
				});

				// 2️⃣ ATLET → KEJUARAAN
				$('#code_atlete').on('change', function() {
					let code_atlete = $(this).val();
					let $champSelect = $('#code_championship');

					// Reset berikutnya
					$('#code_event').val('').trigger('change').prop('disabled', true);

					if (!code_atlete) {
						$champSelect.val('').trigger('change').prop('disabled', true);
						return;
					}

					// Aktifkan dropdown kejuaraan (jika datanya sudah ada di server)
					$champSelect.prop('disabled', false);
				});

				// 3️⃣ KEJUARAAN → NOMOR LOMBA
				$('#code_championship').on('change', function() {
					let code_championship = $(this).val();
					let $eventSelect = $('#code_event');					
					let code_atlet = $('#code_atlete').val();

					if (!code_championship) {
						$eventSelect.empty().append('<option value="">Pilih Nomor Lomba</option>').trigger('change').prop('disabled', true);
						return;
					}

					$eventSelect.empty().append('<option value="">Mengambil data...</option>').trigger('change').prop('disabled', true);

					$.ajax({
						url: '/admin/getevent',
						type: 'GET',
                        headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
						data: { 
							code_championship: code_championship,
							code_atlet: code_atlet
						},
						dataType: 'json',
						success: function(results) {
							$eventSelect.empty();

							if (results.length === 0) {
								$eventSelect.append('<option value="">Tidak ada tersedia</option>');
							} else {
								$eventSelect.append('<option value="">Pilih Nomor Lomba</option>');
								$.each(results, function(index, val) {
									$eventSelect.append('<option value="' + val.code_data + '">' + val.code_event + '</option>');
								});
							}

							$eventSelect.prop('disabled', false).trigger('change');
						},
						error: function() {
							$eventSelect.empty().append('<option value="">Gagal mengambil data</option>').trigger('change').prop('disabled', true);
							alert('❌ Gagal mengambil data event.');
						}
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
				let club = $('#code_club').val();
				let atlet = $('#code_atlete').val();
				let championship = $('#code_championship').val();
				let event = $('#code_event').val();

				let isComplete = club && atlet && championship && event;
				$('button[name="btn_save"]').prop('disabled', !isComplete);
			}

			// Jalankan pengecekan setiap kali dropdown berubah
			$('#code_club, #code_atlete, #code_championship, #code_event').on('change', checkFormInputs);
		</script>
	@endsection

@endsection
