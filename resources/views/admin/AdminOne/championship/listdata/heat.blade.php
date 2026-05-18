@extends('admin.AdminOne.layout.assets')
@section('title', 'Seri Lomba')

@section('content')

            <div class="page_main">
                <div class="container-fluid text-left">
                    <div class="row">
                        <div class="col-md-12 bg_page_main hd" line="hd_action">
                            <div class="col-md-12 hd_page_main">Seri Lomba</div>
							<div class="col-md-12 bg_act_page_main">
								<div class="row">
									<div class="col-xl-12 col_act_page_main text-left">
										<button type="button" class="btn btn-secondary" onclick="BackPage()"><i class="fa fa-chevron-left"></i> Kembali</button>
										
										<!-- @if($level_user['exportheat'] == 'Yes')<button type="button" class="btn btn-info" onclick="exportdata('heat')"><i class="fa fa-download"></i> Export Data</button>@endif -->
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
													<th style="min-width:50px; text-align: center;">Kode Data</th>
													<th style="min-width:200px; text-align: center;">Nama Kejuaraan</th>
													<th style="min-width:200px; text-align: center;">Nomor Lomba</th>
													<th style="min-width:150px; text-align: center;">Nomor Seri</th>
												</tr>
											</thead>
											<tbody>
												<?php $no = 0;?> @forelse($results['data'] as $view_data) 
                                                    <?php 
                                                        $no++ ;
                                                    ?>
													<tr>
														<td style="text-align:center;">{{ $no }}</td>
														<td style="text-align:center;">{{ $view_data['code_data'] ?? 'Belum ditentukan' }}</td>
														<td style="text-align:center;">{{ $view_data['event']['championship']['nama_kejuaraan'] ?? 'Belum ditentukan' }}</td>
														<td style="text-align:left;">{{ $view_data['event']['code_event'] ?? 'Belum ditentukan' }}</td>
														<td style="text-align:center;">{{ number_format($view_data['nomor_seri'] ?? 0, 0,"",".") }}</td>
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