@extends('admin.AdminOne.layout.assets')
@section('title', 'Data Kejuaraan')

@php
    use Carbon\Carbon;
    \Carbon\Carbon::setLocale('id');
@endphp

@section('content')

            <div class="page_main">
                <div class="container-fluid text-left">
                    <div class="row">
                        <div class="col-md-12 bg_page_main hd" line="hd_action">
                            <div class="col-md-12 hd_page_main">Data Kejuaraan</div>
							<div class="col-md-12 bg_act_page_main">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-left">
										<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
										@if($level_user['newchampionship'] == 'Yes')<a load="true" href="/admin/newchampionship"><button type="button" class="btn btn-primary">Input Kejuaraan</button></a>@endif
										
										@if($level_user['exportchampionship'] == 'Yes')<button type="button" class="btn btn-info" onclick="exportdata('championship')"><i class="fa fa-download"></i> Export Data</button>@endif
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
													<th style="min-width:150px; text-align: center;">Logo</th>
													<th style="min-width:50px; text-align: center;">Kode Data</th>
													<th style="min-width:150px; text-align: center;">Nama Kejuaraan</th>
													<th style="min-width:150px; text-align: center;">Lokasi</th>
													<th style="min-width:100px; text-align: center;">Jumlah Line</th>
													<th style="min-width:200px; text-align: center;">Waktu Pelaksanaan</th>
													<th style="min-width:100px; text-align: center;">Status</th>
													<th style="min-width:150px; text-align: center;">Print</th>
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
																	$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-danger">Anda yakin untuk menghapus data {{$view_data['nama_kejuaraan']}}.</div>');
																	$('button[btn-action="action-confirmasi"]').remove();
																	$('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>');
																	$('button[btn-action="action-confirmasi"]').click(function(){
																		if($('button[btn-action="action-confirmasi"]').click){
																			$('button[btn-action="action-confirmasi"]').remove();
																			$('button[btn-action="close-confirmasi"]').remove();
																			loadingpage(20000);
																			window.location.href = "/admin/deletechampionship?d={{$view_data['code_data']}}";
																		}
																	});
																}
															});
														});
													</script>
													<tr>
														<td style="text-align:center;">{{$no}}</td>
														<td style="text-align:center;"><img src="{{ !empty($view_data['logo']) ? asset('/themes/admin/AdminOne/image/upload/'.$view_data['logo']) : asset('/themes/admin/AdminOne/image/no_image.png') }}" class="preview-foto" data-id="{{ $view_data['id'] }}" style="width:150px;height:100px;object-fit:cover;cursor:pointer;border-radius:6px;border:1px solid #ddd;"></td>	
														<td style="text-align:center;">{{$view_data['code_data'] ?? 'Belum ditentukan'}}</td>
														<td>{{$view_data['nama_kejuaraan'] ?? 'Belum ditentukan'}}</td>
														<td style="text-align:center;">{{$view_data['lokasi'] ?? 'Belum ditentukan'}}</td>
														<td style="text-align:center;">{{isset($view_data['jumlah_line']) ? number_format($view_data['jumlah_line'], 0, ',', '') : 'Belum ditentukan'}}</td>
                                                        <td style="text-align:center;">{{!empty($view_data['tanggal_mulai']) ? Carbon::parse($view_data['tanggal_mulai'])->translatedFormat('d F Y') : 'Belum ditentukan'}} s.d {{!empty($view_data['tanggal_selesai']) ? Carbon::parse($view_data['tanggal_selesai'])->translatedFormat('d F Y') : 'Belum ditentukan'}}</td>
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
														<td style="text-align:center;">
															<div style="display: flex; justify-content: center; gap: 4px;">
																<a href="printbook?d={{$view_data['code_data']}}" target="_blank" class="btn btn-secondary btn-print-white"><i class="fa fa-print"></i> Print Buku</a>

																<a href="printresult?d={{$view_data['code_data']}}" target="_blank" class="btn btn-info btn-print-white"><i class="fa fa-print"></i> Print Hasil</a>
															</div>
														</td>
														<td class="colright" style="text-align:center;">
															<div class="dropdown dropleft">
																<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Atur</button>
																<div class="dropdown-menu">
																	<h5 class="dropdown-header">Pengaturan Data</h5>
																	<a load="true" class="dropdown-item" href="/admin/editchampionship?d={{$view_data['code_data']}}">Lihat/Ubah Data</a>
																	<a class="dropdown-item @if($view_data['count_used'] > 0) disabled @endif @if($level_user['deletechampionship'] == 'No') disabled @endif" <?php if($view_data['count_used'] == 0){ if($level_user['deletechampionship'] == 'Yes'){ ?> btn="del_data_{{$view_data['code_data']}}"<?php } }?>>Hapus Data</a>
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