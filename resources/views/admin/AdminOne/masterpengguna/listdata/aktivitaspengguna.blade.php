@extends('admin.AdminOne.layout.assets')
@section('title', 'Aktivitas Pengguna')

@section('content')

            <div class="page_main">
                <div class="container-fluid text-left">
                    <div class="row">
                        <div class="col-md-12 bg_page_main hd" line="hd_action">
                            <div class="col-md-12 hd_page_main">Aktivitas Pengguna</div>
							<div class="col-md-12 bg_act_page_main">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-left">
										<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
										
										@if($level_user['exportactivityusers'] == 'Yes')
											<!-- <button type="button" class="btn btn-info" onclick="exportdata('activityusers')"><i class="fa fa-download"></i> Export Data</button> -->
											<button type="button" class="btn btn-info" onclick="exportdata({url:'/admin/exportactivityusers',btn:this})"><i class="fa fa-download"></i> Export Data</button>
										@endif
									</div>
								</div>
							</div>
                        </div>
						<div class="col-md-12 bg_page_main dt" line="form_action">
							<div class="col-md-12 bg_act_page_main page">
								<div class="row">
									<div class="col-xl-8 col_act_page_main text-left">
										Filter Tanggal Data <input type="text" name="datefilterstart" placeholder="Dari tanggal" style="width: 90px; text-align: padding-left: 0px; center; cursor: pointer;" readonly="" value="<?php echo Date::parse($datefilterstart)->format('d M Y'); ?>"/> 
										- <input type="text" name="datefilterend" placeholder="Sampai tanggal" style="width: 90px; text-align: center; padding-left: 0px; cursor: pointer;" readonly="" value="<?php echo Date::parse($datefilterend)->format('d M Y'); ?>" /> 
										<button type="button" class="btn btn-default filter" onclick="datefilter()">Filter</button>
									</div>
									<div class="col-xl-4 col_act_page_main text-right">
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
													<th style="min-width:250px; text-align: center;">Tanggal Aktivitas</th>
													<th style="min-width:150px; text-align: center;">Kode Pengguna</th>
													<th style="min-width:150px; text-align: center;">Nama Lengkap</th>
													<th style="min-width:250px; text-align: center;">Keterangan Aktivitas</th>
												</tr>
											</thead>
											<tbody>
												<?php $no = 0;?> @forelse($results['data'] as $view_data) 
													<?php 
														\Carbon\Carbon::setLocale('id');
														$no++ ;
														?>
													<tr>
														<td style="text-align:center;">{{$no}}</td>
														<td>{{ !empty($view_data['created_at']) ? \Carbon\Carbon::parse($view_data['created_at'])->translatedFormat('l, j F Y - H:i:s') : 'Belum ditentukan' }}</td>
														<td style="text-align:center;">{{ $view_data['user']['code_data'] ?? 'Belum ditentukan' }}</td>
														<td>{{ $view_data['user']['full_name'] }}</td>
														<td>{{ $view_data['activity'] ?? 'Belum ditentukan'}}</td>
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