@extends('admin.AdminOne.layout.assets')
@section('title', 'Nomor Lomba')

@php
    use Carbon\Carbon;
    \Carbon\Carbon::setLocale('id');
@endphp

@section('content')

            <div class="page_main">
                <div class="container-fluid text-left">
                    <div class="row">
                        <div class="col-md-12 bg_page_main hd" line="hd_action">
                            <div class="col-md-12 hd_page_main">Nomor Lomba</div>
							<div class="col-md-12 bg_act_page_main">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-left">
										<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
										@if($level_user['newevent'] == 'Yes')<a load="true" href="/admin/newevent"><button type="button" class="btn btn-primary">Input Nomor Lomba</button></a>@endif
										
										@if($level_user['exportevent'] == 'Yes')<button type="button" class="btn btn-info" onclick="exportdata('event')"><i class="fa fa-download"></i> Export Data</button>@endif
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
													<th style="min-width:50px; text-align: center;">Nomor Lomba</th>
													<th style="min-width:200px; text-align: center;">Nama Lomba</th>
													<th style="min-width:150px; text-align: center;">Waktu Pelaksanaan</th>
													<th style="min-width:200px; text-align: center;">Nama Kejuaraan</th>
													<th style="min-width:100px; text-align: center;">Jumlah Peserta</th>
													<th style="min-width:200px; text-align: center;">Generate Heat</th>
													<th style="min-width:150px; text-align: center;">Status</th>
													<th style="min-width:150px; text-align: center;">Aksi</th>
                                                    
													<th class="colright" style="width:30px; text-align: center;"><i class="head fa fa-cog"></i></th>
												</tr>
											</thead>
											<tbody>
												<?php $no = 0;?> @forelse($results['data'] as $view_data) 
                                                    <?php 
                                                        $no++ ;
                                                    ?>
													<script type="text/javascript">
														$(document).ready(function(){
															$('[btn="del_data_{{$view_data['code_data']}}"]').click(function(){
																if($('[btn="del_data_{{$view_data['code_data']}}"]').click){
																	$('div[data-model="confirmasi"]').modal({backdrop: false});
																	$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-danger">Anda yakin untuk menghapus data {{$view_data['code_event']}}.</div>');
																	$('button[btn-action="action-confirmasi"]').remove();
																	$('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>');
																	$('button[btn-action="action-confirmasi"]').click(function(){
																		if($('button[btn-action="action-confirmasi"]').click){
																			$('button[btn-action="action-confirmasi"]').remove();
																			$('button[btn-action="close-confirmasi"]').remove();
																			loadingpage(20000);
																			window.location.href = "/admin/deleteevent?d={{$view_data['code_data']}}";
																		}
																	});
																}
															});

															$('[btn="generateHeat_{{$view_data['code_data']}}"]').click(function(){
																if($('[btn="generateHeat_{{$view_data['code_data']}}"]').click){
																	$('div[data-model="confirmasi"]').modal({ backdrop: false });
																	$('.modal-body').html(`
																		<form method="post" name="form_generateHeat" enctype="multipart/form-data" action="/admin/generateheat?d={{ $view_data['code_data'] }}">
																			{{csrf_field()}}
																			<input type="text" name="d" value="{{ $view_data['code_data'] }}" readonly="true" style="display: none;" />
																			<input type="text" name="code_data" value="{{ $view_data['code_event'] }}" readonly="true" style="display: none;" />
																			
																			<div class="alert alert-warning">Anda yakin untuk generate heat data {{ $view_data['code_event'] }}</div>
																			<div class="form_input text-left">
																				<div class="tag_title" style="color:#ED3237;">Heat lama akan dihapus!</div>
																			</div>
																		</form>
																	`);

																	$('button[btn-action="action-confirmasi"]').remove();
																	$('button[btn-action="close-confirmasi"]').before(`<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>`);

																	$('div[data-model="confirmasi"]').off('click', 'button[btn-action="action-confirmasi"]')
																	.on('click', 'button[btn-action="action-confirmasi"]', function() {	
																		$('button[btn-action="action-confirmasi"]').remove();
																		$('button[btn-action="close-confirmasi"]').remove();
																		loadingpage(20000);
																		$('form[name="form_generateHeat"]').submit();
																	});
																}
															});
														});
													</script>
													<tr>
														<td style="text-align:center;">{{$no}} </td>
														<td >{{ $view_data['code_event'] ?? 'Belum ditentukan' }}</td>
														 @php
															$jarak = isset($view_data['jarak']) ? number_format($view_data['jarak'], 0, ',', '') . ' M' : 'Belum ditentukan';
															$gaya = $view_data['kategori']['nama_gaya'] ?? '';
															$kelompok = $view_data['kelompok_umur']['code_kelompok'] ?? '';
															$gender = $view_data['gender'] ?? '';
															$hasil = trim("$jarak $gaya $kelompok $gender");
														@endphp
														<td >{{ $hasil }}</td>
                                                        <td style="text-align:center;">{{ !empty($view_data['tanggal']) ? Carbon::parse($view_data['tanggal'])->translatedFormat('d F Y') : 'Belum ditentukan' }}</td>
														<td style="text-align:center;">{{ $view_data['championship']['nama_kejuaraan'] ?? 'Belum ditentukan' }}</td>
														<td style="text-align:center;">{{ number_format($view_data['heat_lines_count'] ?? 0, 0,"",".") }}</td>
														<td style="text-align:center;">
															@if($view_data['count_used'] == 0)
																<div class="alert alert-warning" style="margin: 0 auto; display: inline-block; text-align: center; font-size: 14px; padding: 2px 10px;">
																	<strong>Belum Generate</strong>
																</div>
															@else
																<div class="alert alert-success" style="margin: 0 auto; display: inline-block; text-align: center; font-size: 14px; padding: 2px 10px;">
																	<strong>Heat Generated</strong>
																</div>
															@endif
														</td>
														<td style="text-align:center;">
															@if($view_data['status_data'] == 'Proses')
																<div class="alert alert-warning" style="margin: 0 auto; display: inline-block; text-align: center; font-size: 14px; padding: 2px 10px;">
																	<strong>{{ $view_data['status_data'] ?? 'Belum Ditentukan'}}</strong>
																</div>
															@elseif($view_data['status_data'] == 'Finish')
																<div class="alert alert-success" style="margin: 0 auto; display: inline-block; text-align: center; font-size: 14px; padding: 2px 10px;">
																	<strong>{{ $view_data['status_data'] ?? 'Belum Ditentukan'}}</strong>
																</div>
															@else
																<div class="alert alert-danger" style="margin: 0 auto; display: inline-block; text-align: center; font-size: 14px; padding: 2px 10px;">
																	<strong>{{ $view_data['status_data'] ?? 'Belum Ditentukan'}}</strong>
																</div>
															@endif
														</td>
														<td style="text-align:center;"><button class="btn btn-info" btn="generateHeat_{{$view_data['code_data']}}" @if($view_data['status_data'] == 'Finish') disabled @endif>Generate Heat & Line</button></td>
														<td class="colright" style="text-align:center;">
															<div class="dropdown dropleft">
																<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Atur</button>
																<div class="dropdown-menu">
																	<h5 class="dropdown-header">Pengaturan Data</h5>
																	<a load="true" class="dropdown-item" href="/admin/editevent?d={{$view_data['code_data']}}">Lihat/Ubah Data</a>
																	<a class="dropdown-item @if($view_data['count_used'] > 0) disabled @endif @if($level_user['deleteevent'] == 'No') disabled @endif" <?php if($view_data['count_used'] == 0){ if($level_user['deleteevent'] == 'Yes'){ ?> btn="del_data_{{$view_data['code_data']}}"<?php } }?>>Hapus Data</a>
																</div>
															</div>
														</td>
													</tr>
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
				</script>
			@endsection

@endsection