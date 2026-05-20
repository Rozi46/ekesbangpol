@extends('admin.AdminOne.layout.assets')
@section('title', 'Data Perusahaan')

@section('content')

            <div class="page_main">
                <div class="container-fluid text-left">
                    <div class="row">
                        <div class="col-md-12 bg_page_main hd" line="hd_action">
                            <div class="col-md-12 hd_page_main">Data Perusahaan</div>
							<div class="col-md-12 bg_act_page_main">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-left">
										<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
                                        <a load="true" href="newcompany"><button type="button" class="btn btn-primary" id="btnTambah"><i class="fa fa-plus"></i> Tambah Data</button></a>
									</div>
								</div>
							</div>
                        </div>
						<div class="col-md-12 bg_page_main dt" line="form_action">
						<!-- <div class="col-md-12 bg_page_main dt"> -->
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
													<th style="width:50px; text-align: center;">No</th>
													<th style="width:70px;">Logo</th>
													<th style="min-width:100px;">Nama</th>
													<th style="min-width:100px;">Jenis</th>
													<th style="min-width:100px;">Alamat</th>
													<th style="min-width:100px;">Email</th>
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
                                                            $('[btn="del_data_{{$view_data['id']}}"]').click(function(){
                                                                if($('[btn="del_data_{{$view_data['id']}}"]').click){
                                                                    $('div[data-model="confirmasi"]').modal({backdrop: false});
                                                                    $('div[data-model="confirmasi"] .modal-body').html('<div class="alert alert-danger">Anda yakin untuk menghapus company {{$view_data['nama_company']}}.</div>');
                                                                    $('button[btn-action="action-confirmasi"]').remove();
                                                                    $('button[btn-action="close-confirmasi"]').before('<button type="button" class="btn btn-primary btn-sm" btn-action="action-confirmasi">Yakin</button>');
                                                                    $('button[btn-action="action-confirmasi"]').click(function(){
                                                                        if($('button[btn-action="action-confirmasi"]').click){
																			$('button[btn-action="action-confirmasi"]').remove();
																			$('button[btn-action="close-confirmasi"]').remove();
                                                                            loadingpage(20000);
                                											window.location.href = "/admin/deletecompany?d={{$view_data['id']}}";
                                                                        }
                                                                    });
                                                                }
                                                            });
														});
													</script>
													<tr>
														<td style="text-align:center;">{{$no}}</td>
                                                        <td>
                                                            <img 
                                                                src="{{ $view_data['foto'] ? asset('/image/company/'.$view_data['foto']) : asset('/setting/icon.png') }}" 
                                                                alt="Logo" 
																class="table-avatar"
															>
                                                        </td>
														<td>{{$view_data['nama_company']}} <br> <div class="table-badge">{{$view_data['code_data']}}</div></td>
														<td>{{$view_data['jenis']}} </td>
														<td>{{$view_data['alamat']}} </td>
														<td>{{$view_data['email']}} </td>
														<td class="colright" style="text-align:center;">
															<div class="dropdown dropleft">
																<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Atur</button>
																<div class="dropdown-menu">
																	<h5 class="dropdown-header">Pengaturan Data</h5>
																	<a load="true" class="dropdown-item" href="/admin/viewcompany?d={{$view_data['id']}}">Lihat/Ubah Data</a>
																	<a class="dropdown-item @if($listdata['count_used'][$view_data['id']] > 0) disabled @endif" <?php if($listdata['count_used'][$view_data['id']] == 0){ { ?> btn="del_data_{{$view_data['id']}}"<?php } }?>>Hapus Data</a>
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