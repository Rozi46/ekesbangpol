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
								@if($level_user['editregister'] == 'Yes' && $results['results']['register']['status'] == 'pending')
                                    @if($res_user['level'] == 'LV5677001')
									    <button type="button" class="btn btn-primary" name="btn_save">Simpan Data</button>
                                    @endif

									<!-- <button type="button" class="btn btn-info" name="btn_konfirmasi">Konfirmasi</button> -->

									<!-- <button type="button" class="btn btn-danger" name="btn_rejected">Rejected</button> -->
                                    
                                    <button type="button" class="btn btn-info" onclick="konfirmasi()">Konfirmasi</button>
                                    
                                    <button type="button" class="btn btn-danger" onclick="rejected()">Rejected</button>
								@endif
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-12 bg_page_main form_action" line="form_action">
					<div class="col-md-12 data_page">
						<form method="post" name="form_data" enctype="multipart/form-data" action="/admin/editregister">
							{{ csrf_field() }}
							<div class="row bg_data_page form_page content">
								<div class="col-md-12 bg_form_page">
									<div class="form-group row form_input text-left">
										<label class="col-sm-2 col-form-label">Nomor Pendaftaran <span>*</span></label>
										<div class="col-sm-10 input">                                            
											<input type="text" name="nomor_pendaftaran" placeholder="Nomor Pendaftaran" value="{{ $results['results']['register']['code_data'] }}" readonly="true" >
										</div>
									</div>
								</div>
								<div class="col-md-12 bg_form_page">
									<div class="form-group row form_input text-left">
										<label class="col-sm-2 col-form-label">Nama Club <span>*</span></label>
										<div class="col-sm-10 input">                                            
											<input type="text" name="nama_club" placeholder="Nama Club" value="{{ $results['results']['club']['nama_club'] }}" readonly="true" >
										</div>
									</div>
								</div>
								<div class="col-md-12 bg_form_page">
									<div class="form-group row form_input text-left">
										<label class="col-sm-2 col-form-label">Nama Atlet <span>*</span></label>
										<div class="col-sm-10 input">
											<input type="text" name="nama_atlet" placeholder="Nama Atlet" value="{{ $results['results']['atlet']['nama'] }}" readonly="true" >
										</div>
									</div>
								</div>
								<div class="col-md-12 bg_form_page">
									<div class="form-group row form_input text-left">
										<label class="col-sm-2 col-form-label">Nama Kejuaraan <span>*</span></label>
										<div class="col-sm-10 input">
											<input type="text" name="nama_kejuaraan" placeholder="Nama Kejuaraan" value="{{ $results['results']['champion']['nama_kejuaraan'] }}" readonly="true" >
										</div>
									</div>
								</div>
								<div class="col-md-12 bg_form_page">
									<div class="form-group row form_input text-left">
										<label class="col-sm-2 col-form-label">Nomor Lomba <span>*</span></label>
										<div class="col-sm-10 input">
											<select id="code_event" name="code_event[]" multiple></select>
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
            
    <div class="modal fade" role="dialog" data-model="rejected">
        <div class="modal-dialog modal-ls">
            <div class="modal-content">
                <div class="modal-body">
                    <!-- <div line="hd_model" class="hd_model">Rejected Pendaftaran Peserta</div> -->
                    <form method="post" name="form_rejected" enctype="multipart/form-data" action="/admin/rejectedregister">
                        {{csrf_field()}}
                            <input type="text" name="d" value="{{$request['d']}}" readonly="true" style="display: none;" />
                            <input type="text" name="code_data" value="{{ $results['results']['register']['code_data'] }}" readonly="true" style="display: none;" />
                            <input type="text" name="nama_atlet" value="{{ $results['results']['atlet']['nama'] }}" readonly="true" style="display: none;" />
                            <div class="alert alert-warning">Anda yakin untuk rejected data {{ $results['results']['register']['code_data'] }}</div>
                            <div class="form_input text-left">
                                <div class="tag_title" style="color:#ED3237;">Setelah rejected data tidak bisa diedit kembali</div>
                            </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" btn-action="close-confirmasi">Tutup</button>
                </div>
            </div>
        </div>
    </div>

	@section('script')
		<script type="text/javascript">
			$(document).ready(function(){     
                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                });  
				// Select2
				$('#code_event').select2({
					placeholder: 'Pilih Nomor Lomba',
                    allowClear: true,
					width: '100%'
				});	 
                
                loadEvent();  

                lockForm(true);

                // trigger saat select2 berubah
                $('#code_event').on('change.select2', validateSelect2);

                // initial check
                validateSelect2();

                @if($level_user['editregister'] == 'Yes' && $results['results']['register']['status'] == 'pending')
                    lockForm(false);
                @endif

				// Tombol Simpan
				$('button[name="btn_save"]').click(function() {
					let nomor_pendaftaran = $('input[name="nomor_pendaftaran"]').val();
					$('div[data-model="confirmasi"]').modal({backdrop: false});
					$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-warning">Anda yakin untuk simpan data ' + nomor_pendaftaran + '.</div>');
					$('button[btn-action="action-confirmasi"]').remove();
					$('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>');

					$('button[btn-action="action-confirmasi"]').click(function() {
                        $('button[btn-action="action-confirmasi"]').remove();
                        $('button[btn-action="close-confirmasi"]').remove();
						loadingpage(20000);
						$('form[name="form_data"]').submit();
					});
				});

				// Tombol Rejected
				// $('button[name="btn_rejected"]').click(function() {
				// 	let nomor_pendaftaran = $('input[name="nomor_pendaftaran"]').val();
				// 	$('div[data-model="confirmasi"]').modal({backdrop: false});
				// 	$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-danger">Anda yakin untuk rejected data ' + nomor_pendaftaran + '. <br> Setelah rejected data tidak dapat diubah.</div>');
				// 	$('button[btn-action="action-confirmasi"]').remove();
				// 	$('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>');

				// 	$('button[btn-action="action-confirmasi"]').click(function() {
                //         if($('button[btn-action="action-confirmasi"]').click){
                //             $('button[btn-action="action-confirmasi"]').remove();
                //             $('button[btn-action="close-confirmasi"]').remove();
                //             loadingpage(20000);
                //             window.location.href = "/admin/rejectedregister?d={{ $results['results']['register']['code_data'] }}";
                //         }
				// 	});
				// });
			});                

            function loadEvent() {
                let code_championship = @json($results['results']['register']['code_champion']);
                let code_atlet = @json($results['results']['register']['code_athlete']);
                let selectedEvents = @json($results['results']['register']['code_event'] ?? []);

                selectedEvents = normalizeSelected(selectedEvents);
                // console.log("selected form PHP:", selectedEvents);

                $.ajax({
                    url: '/admin/getevent',
                    type: 'GET',
                    data: { code_championship, code_atlet },
                    dataType: 'json',
                    success: function(results) {

                        let $eventSelect = $('#code_event');
                        $eventSelect.empty();                     

                        results.forEach(val => {
                            $eventSelect.append(
                                $('<option>', {value: val.code_data, text:  val.code_event})
                            );
                        });

                        setTimeout(() => {                            
                            $eventSelect.val(selectedEvents).trigger('change');
                            // console.log("Applied Selected:", selectedEvents);
                        }, 0);

                        // console.log("Options:", $('#code_event option').map(function(){return $(this).val();}).get());
                    }
                });
            }

            // 🧹 Normalizer universal
            function normalizeSelected(input) {
                if (!input) return [];

                // Jika sudah array clean → return
                if (Array.isArray(input) && typeof input[0] === "string") {
                    return input;
                }

                // Jika array tapi isinya string JSON → gabungkan dan parse
                if (Array.isArray(input) && input.length === 1 && input[0].includes('[')) {
                    try {
                        return JSON.parse(input[0]);
                    } catch(e) {}
                }

                // Jika string JSON
                if (typeof input === 'string' && input.includes('[')) {
                    try {
                        return JSON.parse(input);
                    } catch(e) {}
                }

                // Jika string comma separated → split
                if (typeof input === 'string' && input.includes(',')) {
                    return input.split(',').map(e => e.trim().replace(/"/g, ''));
                }

                // fallback → jadikan array string
                return [String(input).replace(/"/g, '')];
            }	

            function lockForm(state = true) {
                $('button[name="btn_save"], button[name="btn_konfirmasi"], button[name="btn_rejected"]').prop('disabled', state);
                $('form :input').not('button, [type=button], [type=submit]').prop('disabled', state);
                $('.select2').prop('disabled', state).trigger('change.select2');
            }

            function validateSelect2() {
                let val = $('#code_event').val(); // array atau null

                if (!val || val.length === 0) {
                    $('button[name="btn_save"], button[name="btn_konfirmasi"], button[name="btn_rejected"]').prop('disabled', true);
                } else {
                    $('button[name="btn_save"], button[name="btn_konfirmasi"], button[name="btn_rejected"]').prop('disabled', false);
                }
            }

            function konfirmasi() {
                $('div[data-model="confirmasi"]').modal({ backdrop: false });

                $('.modal-body').html(`
                    <form method="post" name="form_konfirmasi" enctype="multipart/form-data" action="/admin/verifiedregister">
                        {{csrf_field()}}
                        <input type="text" name="d" value="{{$request['d']}}" readonly="true" style="display: none;" />
                        <input type="text" name="code_data" value="{{ $results['results']['register']['code_data'] }}" readonly="true" style="display: none;" />
                        <input type="text" name="nama_atlet" value="{{ $results['results']['atlet']['nama'] }}" readonly="true" style="display: none;" />
                        <div class="alert alert-warning">Anda yakin untuk konfirmasi data {{ $results['results']['register']['code_data'] }}</div>
                        <div class="form_input text-left">
                            <div class="tag_title" style="color:#ED3237;">Setelah konfirmasi data tidak bisa diedit kembali</div>
                        </div>
                    </form>
                `);

                $('button[btn-action="action-confirmasi"]').remove();
                $('button[btn-action="close-confirmasi"]').before(`
                    <button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>
                `);

                $('div[data-model="confirmasi"]').off('click', 'button[btn-action="action-confirmasi"]')
                    .on('click', 'button[btn-action="action-confirmasi"]', function() {
                        $('button[btn-action="action-confirmasi"]').remove();
                        $('button[btn-action="close-confirmasi"]').remove();
                        loadingpage(20000);
                        $('form[name="form_konfirmasi"]').submit();
                    });
            }

            function rejected() {
                $('div[data-model="rejected"]').modal({backdrop: false});             
                $('button[btn-action="action-confirmasi"]').remove();
                $('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi" style="padding: 5px 10px 7px 10px;">Yakin</button>');
                $('button[btn-action="action-confirmasi"]').on('click', function(){
                    if($('button[btn-action="action-confirmasi"]').click){
                        $('button[btn-action="action-confirmasi"]').remove();
                        $('button[btn-action="close-confirmasi"]').remove();
                        loadingpage(20000);
                        $('form[name="form_rejected"]').submit();
                    }
                });
            }


            // modal.off('click', 'button[btn-action="action-confirmasi"]')
            //     .on('click', 'button[btn-action="action-confirmasi"]', function() {
            //         loadingpage(20000);

            //         $.post('/admin/deletechampionship', {
            //             _token: $('meta[name="csrf-token"]').attr('content'),
            //             d: "{{ $results['results']['champion']['code_data'] }}"
            //         }, function(){
            //             window.location.reload();
            //         });
            // });


		</script>
	@endsection

@endsection
