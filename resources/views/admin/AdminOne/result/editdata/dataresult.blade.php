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
								@if($level_user['inputresult'] == 'Yes' && $results['results']['detail_championship']['status_data'] != 'Finish')<button type="button" class="btn btn-primary" name="btn_save">Simpan Data</button> @endif
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-12 bg_page_main form_action" line="form_action">
					<div class="col-md-12 data_page">
						<form method="post" name="form_data" enctype="multipart/form-data" action="/admin/saveresultlist">
							{{ csrf_field() }}
							<div class="row bg_data_page form_page content">
                                <input type="text" name="code_data" value="{{$code_data}}" readonly="true" style="display:none;" />
                                <input type="text" name="code_championship" value="{{$code_championship}}" readonly="true" style="display:none;" />
                                <input type="text" name="code_event" value="{{$code_event}}" readonly="true" style="display:none;" />

								<div class="col-md-12 bg_form_page">
									<div class="form-group row form_input text-left">
										<label class="col-sm-2 col-form-label">Nama Kejuaraan</label>
										<div class="col-sm-10 input">
                                            <input type="text" name="nama_kejuaraan" placeholder="Nama Kejuaraan" value="{{ $results['results']['detail_championship']['nama_kejuaraan'] }}" readonly="true">
										</div>
									</div>
								</div>
								<div class="col-md-12 bg_form_page">
									<div class="form-group row form_input text-left">
										<label class="col-sm-2 col-form-label">Nomor Lomba</label>
										<div class="col-sm-10 input">
                                            <input type="text" name="nomor_lomba" placeholder="Nomor Lomba" value="{{ $results['results']['detail_event']['code_event'] }}" readonly="true">
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
                                            <th style="min-width:150px; text-align: center;">Catatan</th>
                                            <th style="min-width:50px; text-align: center;">Ranking</th>
                                            <th style="min-width:50px; text-align: center;">Poin</th>
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

                $('input[name="code_data"]').val('{{ $code_data }}');
                $('input[name="code_championship"]').val('{{$code_championship}}');
                $('input[name="code_event"]').val('{{$code_event}}');
                $('input[name="nama_kejuaraan"]').val('{{$results['results']['detail_championship']['nama_kejuaraan']}}');
                $('input[name="nomor_lomba"]').val('{{$results['results']['detail_event']['code_event']}}');

                $('[line="list_data"]').html('<tr><td style="text-align:center; padding: 20px; background-color: #FFFFFF; cursor: default; font-weight: 600; height: 300px; font-size: 14px;" colspan="20"><div class="col-md-12 load_data_i text-center"> <div class="spinner-grow spinner-grow-sm text-muted"></div> <div class="spinner-grow spinner-grow-sm text-secondary"></div> <div class="spinner-grow spinner-grow-sm text-dark"></div></div></td></tr>');
                        
                $.get("/admin/listdataresult",{code_data:'{{$code_data}}',status_data:'Yes',code_championship:'{{$code_championship}}',code_event:'{{$code_event}}'},function(listdata){
                    $('[line="list_data"]').html(listdata);
                }); 

				// Inisialisasi awal
				$('button[name="btn_save"]').prop('disabled', true);
				$('form :input').not('button, [type=button], [type=submit]').prop('disabled', true);

				@if($level_user['inputresult'] == 'Yes')
					$('form :input').not('button, [type=button], [type=submit]').prop('disabled', false);
					$('form :input').on('input change', checkFormInputs);
					checkFormInputs();
				@endif 

				// Tombol Simpan
				$('button[name="btn_save"]').click(function() {
					let nama_event = $('input[name="nomor_lomba"]').val();
					$('div[data-model="confirmasi"]').modal({backdrop: false});
					$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-warning">Anda yakin untuk simpan dan selesai data ini ' + nama_event + '?</div> <div class="form_input text-left"><div class="tag_title" style="color:#ED3237;">Setelah simpan dan selesai data ini tidak bisa diubah kembali</div></div>');
					$('button[btn-action="action-confirmasi"]').remove();
					$('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>');

					$('button[btn-action="action-confirmasi"]').click(function() {
						loadingpage(20000);
						$('form[name="form_data"]').submit();
					});
				});
			});

			// Fungsi cek input
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
