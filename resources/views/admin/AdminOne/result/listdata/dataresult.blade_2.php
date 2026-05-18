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
										@if($level_user['inputresult'] == 'Yes')<a load="true" href="/admin/inputresult"><button type="button" class="btn btn-primary">Input Hasil Pertandingan</button></a>@endif

                                        @if($level_user['exportresult'] == 'Yes')<button type="button" class="btn btn-info" onclick="exportdata('result')"><i class="fa fa-download"></i> Export Data</button>@endif
									</div>
								</div>
							</div>
                        </div>
						<div class="col-md-12 bg_page_main dt" line="form_action">
							<div class="col-md-12 bg_act_page_main page">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-right">
										@include('admin.AdminOne.layout.pagination')
									</div>
								</div>
							</div>
							<div class="col-md-12 data_page">
								<div class="row bg_data_page">
									<div class="table_data freezeHead freezeCol">
										<table class="table_view table-striped table-hover">
											<thead>
												<tr>
													<th style="width:30px; text-align: center;">No</th>
													<th style="min-width:150px; text-align: center;">Kode Data</th>
													<th style="min-width:150px; text-align: center;">Nama Kejuaraan</th>
													<th style="min-width:150px; text-align: center;">Nomor Lomba</th>
													<th style="min-width:50px; text-align: center;">Seri Lomba</th>
													<th style="min-width:150px; text-align: center;">Nama Atlet</th>
													<th style="min-width:50px; text-align: center;">Line Number</th>
													<th style="min-width:100px; text-align: center;">Best Time</th>
													<th style="min-width:100px; text-align: center;">Hasil</th>
													<th style="min-width:150px; text-align: center;">Foto Hasil</th>
													<th style="min-width:150px; text-align: center;">Catatan</th>
													<th style="min-width:50px; text-align: center;">Ranking</th>
													<th style="min-width:50px; text-align: center;">Poin</th>
                                                    
													<th class="colright" style="width:60px; text-align: center;"><i class="head fa fa-cog"></i></th>
												</tr>
											</thead>
											<tbody>
												<?php $no = 0;?> @forelse($results['data'] as $view_data) 
                                                    <?php 
                                                        $no++ ;
                                                    ?>
													<script type="text/javascript">
														$(document).ready(function(){
															$('[btn="del_data_{{$view_data['code_event']}}"]').click(function(){
																if($('[btn="del_data_{{$view_data['code_event']}}"]').click){
																	$('div[data-model="confirmasi"]').modal({backdrop: false});
																	$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-danger">Anda yakin untuk menghapus data {{$view_data['code_event']}}.</div>');
																	$('button[btn-action="action-confirmasi"]').remove();
																	$('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>');
																	$('button[btn-action="action-confirmasi"]').click(function(){
																		if($('button[btn-action="action-confirmasi"]').click){
																			$('button[btn-action="action-confirmasi"]').remove();
																			$('button[btn-action="close-confirmasi"]').remove();
																			loadingpage(20000);
																			window.location.href = "/admin/deleteheatline?d={{$view_data['code_event']}}";
																		}
																	});
																}
															});
														});
													</script>
													<tr>
														<td style="text-align:center;">{{ $no }}</td>
														<td>{{ $view_data['event']['championship']['nama_kejuaraan'] ?? '-' }}</td>
														<td>{{ $view_data['event']['code_event'] ?? '-' }}</td>
														<td>{{ $view_data['event']['championship']['code_data'] ?? '-' }}</td>

														<td class="colright" style="text-align:center;">															
															<div style="display: flex; justify-content: center; gap: 4px;">
																<div class="dropdown dropleft">
																	<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Atur</button>
																	<div class="dropdown-menu">
																		<h5 class="dropdown-header">Pengaturan Data</h5>
																		<a load="true" class="dropdown-item" href="/admin/viewresult?d={{$view_data['code_event']}}&code_championship=&code_event=">Lihat/Ubah Data</a>
																	</div>
																</div>

																<!-- Tombol Detail -->																
																<button type="button" class="btn btn-sm btn-info" onclick="toggleDetail({{ $no }}, this, '{{ $view_data['event']['code_data'] }}')" data-toggle="detail">Detail</button>

															</div>														
														</td>
													</tr>
													
													<!-- Kolom sembunyi  -->
													<tr id="detail-{{ $no }}" style="display: none;">
														<td colspan="20">
															<strong>Detail:</strong>
															<div class="table_data freezeHead freezeCol mt-2">
																<table class="table_view table-striped table-hover" style="width: 100%;">
																	<thead>
																		<tr>
																			<th style="width:30px; text-align: center;">No</th>
																			<th style="min-width:150px; text-align: center;">Kode Data</th>
																			<th style="min-width:150px; text-align: center;">Nama Kejuaraan</th>
																			<th style="min-width:150px; text-align: center;">Nomor Lomba</th>
																			<th style="min-width:50px; text-align: center;">Seri Lomba</th>
																			<th style="min-width:150px; text-align: center;">Nama Atlet</th>
																			<th style="min-width:50px; text-align: center;">Line Number</th>
																			<th style="min-width:100px; text-align: center;">Best Time</th>
																			<th style="min-width:100px; text-align: center;">Hasil</th>
																			<th style="min-width:150px; text-align: center;">Foto Hasil</th>
																			<th style="min-width:150px; text-align: center;">Catatan</th>
																			<th style="min-width:50px; text-align: center;">Ranking</th>
																			<th style="min-width:50px; text-align: center;">Poin</th>
																		</tr>
																	</thead>
																	<tbody>
																	</tbody>
																</table>
															</div>
														</td>
													</tr>
													<tr  style="display: none;"></tr>
												@empty
													<tr>
														<td style="text-align:center; padding: 20px; background-color: #FFFFFF; cursor: default; font-weight: 600; font-size: 14px;" colspan="20">Tidak ada data yang tersedia</td>
													</tr>
												@endforelse
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
					});

					function toggleDetail(no, btn, code_event) {
						const row = document.getElementById("detail-" + no);

						if (row.style.display === "table-row") {
							row.style.display = "none";
							btn.textContent = "Detail";
							return;
						}

						// tutup semua
						document.querySelectorAll('[id^="detail-"]').forEach(r => r.style.display="none");
						document.querySelectorAll('button[data-toggle="detail"]').forEach(b => b.textContent="Detail");

						// tampilkan
						row.style.display = "table-row";
						btn.textContent = "Tutup";

						// load data jika kosong
						if (!row.dataset.loaded) {
							fetch(`/admin/detailresult?code_event=${code_event}`)
								.then(res => res.json())
								.then(data => {
									let html = "";

									data.forEach((d, i) => {
										html += `
											<tr>
												<td>${i+1}</td>
												<td>${d.atlet?.nama ?? '-'}</td>
												<td>${d.heatLine?.line_number ?? '-'}</td>
												<td>${d.heatLine?.best_time ?? '-'}</td>
												<td>${d.hasil ?? '-'}</td>
												<td>${d.ranking ?? '-'}</td>
												<td>${d.poin ?? '-'}</td>
											</tr>
										`;
									});

									row.querySelector("tbody").innerHTML = html;
									row.dataset.loaded = true;
								});
						}
					}
				</script>
			@endsection

@endsection