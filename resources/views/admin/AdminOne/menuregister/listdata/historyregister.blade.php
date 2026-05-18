@extends('admin.AdminOne.layout.assets')
@section('title', 'History Pendaftaran')

@php
    use Carbon\Carbon;
    \Carbon\Carbon::setLocale('id');
@endphp

@section('content')

            <div class="page_main">
                <div class="container-fluid text-left">
                    <div class="row">
                        <div class="col-md-12 bg_page_main hd" line="hd_action">
                            <div class="col-md-12 hd_page_main">History Pendaftaran</div>
							<div class="col-md-12 bg_act_page_main">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-left">
										<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
										@if($level_user['inputregister'] == 'Yes')<a load="true" href="/admin/menuregister"><button type="button" class="btn btn-primary">Input Pendaftaran</button></a>@endif
										
										@if($level_user['exportregister'] == 'Yes')<button type="button" class="btn btn-info" onclick="exportdata('register')"><i class="fa fa-download"></i> Export Data</button>@endif
									</div>
								</div>
							</div>
                        </div>

                        <div class="col-md-12 bg_page_main dt" line="form_action">
							<!-- <div class="col-md-12 bg_page_main form_action" line="form_action"> -->
						<!-- <div class="col-md-12 bg_page_main dt"> -->  
							<div class="col-md-12 bg_act_page_main page">
								<div class="row align-items-center">
									<div class="col-xl-6 col_act_page_main text-left" style="margin-bottom:6px;">
										<!-- Filter Data 
										<select name="tipe_data" placeholder="Tipe Penjualan" style="padding-top: 8px; padding-bottom: 6px;">
											<option value="transaksi">Per Transaksi</option>
											<option value="item">Per Barang</option>
										</select>
										<input type="text" name="datefilterstart" placeholder="Dari tanggal" style="width: 90px; text-align: padding-left: 0px; center; cursor: pointer;" readonly="" value="{{ \Carbon\Carbon::parse($datefilterstart)->format('d M Y') }}"/> 
										- <input type="text" name="datefilterend" placeholder="Sampai tanggal" style="width: 90px; text-align: center; padding-left: 0px; cursor: pointer;" readonly="" value="{{ \Carbon\Carbon::parse($datefilterend)->format('d M Y') }}" />
										<button type="button" class="btn btn-default filter" onclick="datefilter()">Filter</button> -->
									</div>
									<div class="col-xl-6 col_act_page_main text-right" style="margin-bottom:6px;">
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
													<th style="min-width:300px; text-align: center;">Tanggal Pendaftaran</th>
													<th class="colleft" style="min-width:150px; text-align: center;">Nomor Pendaftaran</th>
													<th style="min-width:150px; text-align: center;">Nama Atlet</th>
													<th style="min-width:150px; text-align: center;">Club</th>
													<th style="min-width:150px; text-align: center;">Kejuaran</th>
													<th style="min-width:150px; text-align: center;">Nomor Lomba</th>
													<th style="min-width:150px; text-align: center;">KU</th>
													<th style="min-width:150px; text-align: center;">Status</th>
													<th class="colright" style="width:30px; text-align: center;"><i class="head fa fa-cog"></i></th>
												</tr>
											</thead>
											<tbody>
												<?php $no = 0;?> @forelse($results['data'] as $view_data) 
                                                    <?php 
                                                        $no++ ;
                                                    ?>
													<script type="text/javascript">
                                                        function viewdata_{{$no}}() {
                                                            loadingpage(2000);
                                                            window.location.href = "viewregister?d={{$view_data['code_data']}}";
                                                        }

														$(document).ready(function(){
															$('[btn="del_konfirmasi_{{$view_data['code_data']}}"]').click(function(){
																if($('[btn="del_konfirmasi_{{$view_data['code_data']}}"]').click){
																	let modal = $('div[data-model="confirmasi"]'); // pakai modal yang sama
																	modal.modal({ backdrop: false });

																	modal.find('.modal-body').html(`
																		<form method="post" name="form_konfirmasi" enctype="multipart/form-data" action="/admin/verifiedregister">
																			{{csrf_field()}}
																			<input type="text" name="d" value="{{ $view_data['code_data'] }}" readonly="true" style="display: none;" />
																			<input type="text" name="code_data" value="{{ $view_data['code_data'] }}" readonly="true" style="display: none;" />
																			<input type="text" name="nama_atlet" value="{{ $listdata['detail_atlet'][$view_data['code_data']]['nama'] }}" readonly="true" style="display: none;" />
																			<div class="alert alert-warning">Anda yakin untuk konfirmasi data {{ $view_data['code_data'] }}</div>
																			<div class="form_input text-left">
																				<div class="tag_title" style="color:#ED3237;">Setelah konfirmasi data tidak bisa diedit kembali</div>
																			</div>
																		</form>
																	`);

																	modal.find('button[btn-action="action-confirmasi"]').remove();
																	modal.find('button[btn-action="close-confirmasi"]').before(`
																		<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>
																	`);

																	modal.off('click', 'button[btn-action="action-confirmasi"]')
																		.on('click', 'button[btn-action="action-confirmasi"]', function() {
																			loadingpage(20000);
																			$('form[name="form_konfirmasi"]').submit();
																		});
																}
															});
															$('[btn="del_rejected_{{$view_data['code_data']}}"]').click(function(){
																if($('[btn="del_rejected_{{$view_data['code_data']}}"]').click){
																	let modal = $('div[data-model="confirmasi"]'); // pakai modal yang sama
																	modal.modal({ backdrop: false });

																	modal.find('.modal-body').html(`
																		<form method="post" name="form_rejected" enctype="multipart/form-data" action="/admin/rejectedregister">
																			{{csrf_field()}}
																			<input type="text" name="d" value="{{ $view_data['code_data'] }}" readonly="true" style="display: none;" />
																			<input type="text" name="code_data" value="{{ $view_data['code_data'] }}" readonly="true" style="display: none;" />
																			<input type="text" name="nama_atlet" value="{{ $listdata['detail_atlet'][$view_data['code_data']]['nama'] }}" readonly="true" style="display: none;" />
																			<div class="alert alert-warning">Anda yakin untuk rejected data {{ $view_data['code_data'] }}</div>
																			<div class="form_input text-left">
																				<div class="tag_title" style="color:#ED3237;">Setelah rejected data tidak bisa diedit kembali</div>
																			</div>
																		</form>
																	`);

																	modal.find('button[btn-action="action-confirmasi"]').remove();
																	modal.find('button[btn-action="close-confirmasi"]').before(`
																		<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>
																	`);

																	modal.off('click', 'button[btn-action="action-confirmasi"]')
																		.on('click', 'button[btn-action="action-confirmasi"]', function() {
																			loadingpage(20000);
																			$('form[name="form_rejected"]').submit();
																		});
																}
															});
														});
													</script>
													<tr>
														<td style="text-align:center;">{{$no}}</td>
														<td style="text-align:center;">{{ !empty($view_data['submitted_at']) ? \Carbon\Carbon::parse($view_data['submitted_at'])->translatedFormat('l, j F Y - H:i:s') : 'Belum ditentukan' }}</td>
														<td class="colleft link" style="text-align:center;" title="Detail"  onclick="viewdata_{{$no}}()">{{$view_data['code_data'] ?? 'Belum ditentukan'}}</td>
														<td style="text-align:left;">{{$listdata['detail_atlet'][$view_data['code_data']]['nama'] ?? 'Belum ditentukan'}}</td>
														<td style="text-align:left;">{{$listdata['detail_club'][$view_data['code_data']]['nama_club'] ?? 'Belum ditentukan'}}</td>
														<td>{{$listdata['detail_champion'][$view_data['code_data']]['nama_kejuaraan'] ?? 'Belum ditentukan'}}</td>
                                                        <!-- <td>
                                                            @foreach($listdata['detail_event'][$view_data['code_data']] ?? [] as $event)
                                                                <span class="badge bg-info">{{ $event['code_event'] }}</span><br>
                                                            @endforeach
                                                        </td> -->
                                                        <td>
                                                            @foreach($listdata['detail_event'][$view_data['code_data']] ?? [] as $event)
                                                                <div>{{ $event['code_event'] }}</div>
                                                            @endforeach
                                                        </td>

                                                        <td>
                                                            @foreach($listdata['detail_event'][$view_data['code_data']] ?? [] as $event)
                                                                <div>{{ $event['kelompok_umur']['code_kelompok'] }}</div>
                                                            @endforeach
                                                        </td>

														<td style="text-align:center;">
															@if($view_data['status'] == 'pending')
																<div class="alert alert-warning" style="margin: 0 auto; display: inline-block; text-align: center; font-size: 14px; padding: 2px 10px;">
																	<strong>{{ $view_data['status'] ?? 'Belum Ditentukan'}}</strong>
																</div>
															@elseif($view_data['status'] == 'verified')
																<div class="alert alert-success" style="margin: 0 auto; display: inline-block; text-align: center; font-size: 14px; padding: 2px 10px;">
																	<strong>{{ $view_data['status'] ?? 'Belum Ditentukan'}}</strong>
																</div>
															@elseif($view_data['status'] == 'rejected')
																<div class="alert alert-danger" style="margin: 0 auto; display: inline-block; text-align: center; font-size: 14px; padding: 2px 10px;">
																	<strong>{{ $view_data['status'] ?? 'Belum Ditentukan'}}</strong>
																</div>
															@endif
														</td>

														<td class="colright" style="text-align:center;">
															<div class="dropdown dropleft">
																<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Atur</button>
																<div class="dropdown-menu">
																	<h5 class="dropdown-header">Pengaturan Data</h5>
																	<a load="true" class="dropdown-item" href="viewregister?d={{$view_data['code_data']}}">Lihat/Ubah Data</a>
																	<a class="dropdown-item @if($view_data['status'] != 'pending') disabled @endif" <?php if($view_data['status'] == 'pending'){ if($level_user['editregister'] == 'Yes'){ ?> btn="del_konfirmasi_{{$view_data['code_data']}}"<?php } }?>>Konfirmasi</a>
																	<a class="dropdown-item @if($view_data['status'] != 'pending') disabled @endif" <?php if($view_data['status'] == 'pending'){ if($level_user['editregister'] == 'Yes'){ ?> btn="del_rejected_{{$view_data['code_data']}}"<?php } }?>>Rejected</a>
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