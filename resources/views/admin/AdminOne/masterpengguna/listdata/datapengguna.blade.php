@extends('admin.AdminOne.layout.assets')
@section('title', 'Data Pengguna')

@section('content')

            <div class="page_main">
                <div class="container-fluid text-left">
                    <div class="row">
                        <div class="col-md-12 bg_page_main hd" line="hd_action">
                            <div class="col-md-12 hd_page_main">Data Pengguna</div>
							<div class="col-md-12 bg_act_page_main">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-left">
										<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
										@if($level_user['newusers'] == 'Yes')<a load="true" href="/admin/newusers"><button type="button" class="btn btn-primary" id="btnTambah"><i class="fa fa-plus"></i> Tambah Data</button></a>@endif
										
										@if($level_user['exportusers'] == 'Yes')<button type="button" class="btn btn-info" onclick="exportdata({url:'/admin/exportlistusers',btn:this})"><i class="fa fa-download"></i> Export Data</button>@endif
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
													<th style="width:50px; text-align: center;">No</th>
													<?php if($res_user['tipe_user'] == 'Super User'){?>
														<th style="min-width:100px;">Nama Perusahaan</th>
													<?php } ?>
													<th style="width:70px;">Photo</th>
													<th style="min-width:100px;">Nama Pengguna</th>
													<th style="min-width:100px;">Level Pengguna</th>
													<th style="min-width:100px;">Email Pengguna</th>
													<th style="min-width:100px;">Masuk Terakhir</th>
													<th style="min-width:100px; text-align: center;">Status</th>
												</tr>
											</thead>
											<tbody>
												<?php $no = 0;?> @forelse($results['data'] as $view_data) 
													<?php
														\Carbon\Carbon::setLocale('id');
														$no++ ;
													?>
													<script>
														function ViewData{{$no}}() {
															loadingpage(2000);
															window.location.href = "/admin/viewusers?d={{$view_data['id']}}";
														}
													</script>
													<tr onclick="ViewData{{$no}}()">
														<td style="text-align:center;">{{$no}}</td>
														<?php if($res_user['tipe_user'] == 'Super User'){?>
															<td>{{$view_data['company']['nama_company']}}</td>
														<?php } ?>
														<td class="text-center">
															<img 
																src="{{ $view_data['image'] == 'no_img' ? asset('/themes/admin/AdminOne/image/no_image.jpg') : asset('/themes/admin/AdminOne/image/upload/'.$view_data['image']) }}" 
																class="table-avatar"
																onerror="this.onerror=null;this.src='/themes/admin/AdminOne/image/no_image.jpg';"
															>
														</td>
														<td>{{$view_data['full_name']}} <br><div class="table-badge">{{$view_data['code_data']}}</div></td>
														<td>{{$view_data['level_admin']['level_name']}}</td>
														<td>{{$view_data['email']}}</td>															
														<td><?php if($view_data['created_at'] != $view_data['updated_at']){?> {{\Carbon\Carbon::parse($view_data['updated_at'])->translatedFormat('l, j F Y - H:i:s')}}<?php }else{echo "Belum Ada Aktivitas";} ?> </td>
														<td style="text-align:center;">
															@if($view_data['status_data'] == 'Aktif')
																<div class="alert alert-success" style="margin: 0 auto; display: inline-block; text-align: center; font-size: 14px; padding: 2px 10px;">
																	<strong>{{ $view_data['status_data'] ?? 'Belum Ditentukan'}}</strong>
																</div>
															@else
																<div class="alert alert-danger" style="margin: 0 auto; display: inline-block; text-align: center; font-size: 14px; padding: 2px 10px;">
																	<strong>{{ $view_data['status_data'] ?? 'Belum Ditentukan'}}</strong>
																</div>
															@endif
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

@endsection