@extends('admin.AdminOne.layout.assets')
@section('title', 'Hasil Pertandingan')

@php
    use Carbon\Carbon;
    \Carbon\Carbon::setLocale('id');
@endphp

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

                                        <!-- @if($level_user['exportresult'] == 'Yes')<button type="button" class="btn btn-info" onclick="exportdata('result')"><i class="fa fa-download"></i> Export Data</button>@endif -->
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
													<th style="min-width:100px; text-align: center;">Nomor Lomba</th>
													<th style="min-width:100px; text-align: center;">Nama Kejuaraan</th>
													<th style="min-width:100px; text-align: center;">Lokasi</th>
													<th style="min-width:50px; text-align: center;">Tanggal Pelaksanaan</th>
													<th style="min-width:30px; text-align: center;">Jumlah Line</th>
                                                    
													<th class="colright" style="width:100px; text-align: center;"><i class="head fa fa-cog"></i></th>
												</tr>
											</thead>
											<tbody>
												<?php $no = 0;?> @forelse($results['data'] as $view_data) 
                                                    <?php 
                                                        $no++ ;
                                                    ?>
													<tr>
														<td style="text-align:center;">{{ $no }}</td>
														<td>{{ $view_data['event']['code_event'] ?? 'Belum ditentukan' }}</td>
														<td>{{ $view_data['event']['championship']['nama_kejuaraan'] ?? 'Belum ditentukan' }}</td>
														<td>{{ $view_data['event']['championship']['lokasi'] ?? 'Belum ditentukan' }}</td>
                                                        <td style="text-align:center;">{{!empty($view_data['event']['championship']['tanggal_mulai']) ? Carbon::parse($view_data['event']['championship']['tanggal_mulai'])->translatedFormat('d F Y') : 'Belum ditentukan'}} s.d {{!empty($view_data['event']['championship']['tanggal_selesai']) ? Carbon::parse($view_data['event']['championship']['tanggal_selesai'])->translatedFormat('d F Y') : 'Belum ditentukan'}}</td>
														<td style="text-align:center;">{{isset($view_data['event']['championship']['jumlah_line']) ? number_format($view_data['event']['championship']['jumlah_line'], 0, ',', '') : 'Belum ditentukan'}}</td>

														<td class="colright" style="text-align:center;">															
															<div style="display: flex; justify-content: center; gap: 4px;">
																<div class="dropdown dropleft">
																	<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Atur</button>
																	<div class="dropdown-menu">
																		<h5 class="dropdown-header">Pengaturan Data</h5>
																		<a load="true" class="dropdown-item" href="/admin/viewresult?d={{$view_data['code_data']}}&code_championship={{$view_data['event']['championship']['code_data']}}&code_event={{$view_data['code_event']}}">Lihat/Ubah Data</a>
																	</div>
																</div>

																<!-- Tombol Detail -->																
																<button type="button" class="btn btn-sm btn-info" onclick="toggleDetail({{ $no }}, this, '{{ $view_data['event']['code_data'] }}')" data-toggle="detail">Detail</button>

															</div>														
														</td>                                                        
													
                                                        <!-- Kolom sembunyi  -->
                                                        <tr id="detail-{{ $no }}" style="display: none;">
                                                            <td colspan="20">
                                                                <strong>Detail:</strong>
                                                                <div class="table_data freezeHead freezeCol mt-2">
                                                                    <table class="table_view table-striped table-hover" style="width: 100%;">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width:30px; text-align: center;">No</th>
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
                                                                            @forelse($listdata['listdata_result'] as $index => $list_result)
                                                                                <tr>
                                                                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                                                                    <td style="text-align:center;">{{ number_format($list_result['heat_line']['heat']['nomor_seri'] ?? 0, 0,"",".") }}</td>														
                                                                                    <td style="text-align:left;">{{ $list_result['atlet']['nama'] ?? 'Belum ditentukan' }}<br><div class="alert alert-success" style="margin: 0 auto; display: inline-block; text-align: center; font-size: 14px; padding: 2px 10px;"><strong>{{ $list_result['atlet']['club']['nama_club'] ?? 'Belum ditentukan' }}</strong></div></td>
                                                                                    <td style="text-align:center;">{{ number_format($list_result['heat_line']['line_number'] ?? 0, 0,"",".") }}</td>
                                                                                    <td style="text-align:center;">{{ $list_result['heat_line']['best_time'] ?? 'Belum ditentukan' }}</td>
                                                                                    <td style="text-align:center;">{{ $list_result['hasil'] ?? '00:00.00' }}</td>
                                                                                    <td style="text-align:center;">
                                                                                        <img src="{{ $list_result['foto'] ? asset('/themes/admin/AdminOne/image/upload/'.$list_result['foto']) : asset('/themes/admin/AdminOne/image/no_image.png') }}" class="preview-foto" data-id="{{$list_result['id']}}" style="width:150px;height:100px;object-fit:cover;cursor:pointer;border-radius:6px;border:1px solid #ddd;">
                                                                                    </td>
                                                                                    <td style="text-align:left;">{{ $list_result['catatan'] ?? '' }}</td>
                                                                                    <td class="@if(($list_result['ranking'] ?? 0) == 1) rank-1 @elseif(($list_result['ranking'] ?? 0) == 2) rank-2 @elseif(($list_result['ranking'] ?? 0) == 3) rank-3 @endif" style="text-align:center;">{{ number_format($list_result['ranking'] ?? 0, 0,"",".") }}</td>
                                                                                    <td style="text-align:center;">{{ number_format($list_result['poin'] ?? 0, 0,"",".") }}</td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td style="text-align:center; padding: 20px; background-color: #FFFFFF; cursor: default; font-weight: 600; font-size: 14px;" colspan="20">Tidak ada data yang tersedia</td>
                                                                            @endforelse
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

					let currentlyOpen = null;
					function toggleDetail(no, btn) {
						const detailRow = document.getElementById("detail-" + no);
						const allDetailRows = document.querySelectorAll('[id^="detail-"]');
						const allButtons = document.querySelectorAll('button[data-toggle="detail"]');

						// Tutup semua detail
						allDetailRows.forEach(row => {
							row.style.display = "none";
						});
						allButtons.forEach(button => {
							button.textContent = "Detail";
						});

						// Kalau yang diklik berbeda dari yang sedang terbuka, buka yang baru
						if (currentlyOpen !== no) {
							detailRow.style.display = "table-row";
							btn.textContent = "Tutup";
							currentlyOpen = no;
						} else {
							// Kalau yang diklik adalah yang sedang terbuka, tutup semua
							currentlyOpen = null;
						}
					}
				</script>
			@endsection

@endsection