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
																	$('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-danger">Anda yakin untuk menghapus data {{$view_data['code_data']}}.</div>');
																	$('button[btn-action="action-confirmasi"]').remove();
																	$('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>');
																	$('button[btn-action="action-confirmasi"]').click(function(){
																		if($('button[btn-action="action-confirmasi"]').click){
																			$('button[btn-action="action-confirmasi"]').remove();
																			$('button[btn-action="close-confirmasi"]').remove();
																			loadingpage(20000);
																			window.location.href = "/admin/deleteheatline?d={{$view_data['code_data']}}";
																		}
																	});
																}
															});
														});
													</script>
													<tr>
														<td style="text-align:center;">{{ $no }}</td>
														<td style="text-align:center;">{{ $view_data['code_data'] ?? 'Belum ditentukan' }}</td>
														<td style="text-align:center;">{{ $view_data['event']['championship']['nama_kejuaraan'] ?? 'Belum ditentukan' }}</td>
														<td style="text-align:center;">{{ $view_data['event']['code_event'] ?? 'Belum ditentukan' }}</td>
														<td style="text-align:center;">{{ number_format($view_data['heat_line']['heat']['nomor_seri'] ?? 0, 0,"",".") }}</td>														
														<td style="text-align:left;">{{ $view_data['atlet']['nama'] ?? 'Belum ditentukan' }}<br><div class="alert alert-success" style="margin: 0 auto; display: inline-block; text-align: center; font-size: 14px; padding: 2px 10px;"><strong>{{ $view_data['atlet']['club']['nama_club'] ?? 'Belum ditentukan' }}</strong></div></td>
														<td style="text-align:center;">{{ number_format($view_data['heat_line']['line_number'] ?? 0, 0,"",".") }}</td>
														<td style="text-align:center;">{{ $view_data['heat_line']['best_time'] ?? 'Belum ditentukan' }}</td>
														<td style="text-align:center;">{{ $view_data['hasil'] ?? '00:00.00' }}</td>
                                                        <td style="text-align:center;">
															<img src="{{ $view_data['foto'] ? asset('/themes/admin/AdminOne/image/upload/'.$view_data['foto']) : asset('/themes/admin/AdminOne/image/no_image.png') }}" class="preview-foto" data-id="{{$view_data['id']}}" style="width:150px;height:100px;object-fit:cover;cursor:pointer;border-radius:6px;border:1px solid #ddd;">
                                                        </td>
														<td style="text-align:left;">{{ $view_data['catatan'] ?? '' }}</td>
														<td class="@if(($view_data['ranking'] ?? 0) == 1) rank-1 @elseif(($view_data['ranking'] ?? 0) == 2) rank-2 @elseif(($view_data['ranking'] ?? 0) == 3) rank-3 @endif" style="text-align:center;">{{ number_format($view_data['ranking'] ?? 0, 0,"",".") }}</td>
														<td style="text-align:center;">{{ number_format($view_data['poin'] ?? 0, 0,"",".") }}</td>

														<td class="colright" style="text-align:center;">
															<div class="dropdown dropleft">
																<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Atur</button>
																<div class="dropdown-menu">
																	<h5 class="dropdown-header">Pengaturan Data</h5>
																	<a load="true" class="dropdown-item" href="/admin/viewresult?d={{$view_data['code_data']}}&code_championship={{$view_data['event']['championship']['code_data']}}&code_event={{$view_data['event']['code_data']}}">Lihat/Ubah Data</a>
																	<a class="dropdown-item @if($view_data['status_data'] = 'Finish') disabled @endif @if($level_user['deleteheatline'] == 'No') disabled @endif" <?php if($view_data['status_data'] == 'Finish'){ { ?> btn="del_data_{{$view_data['code_data']}}"<?php } }?>>Hapus Data</a>
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