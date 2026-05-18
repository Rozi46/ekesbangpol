<html>
    <head>
        <title>Export to Excel</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <style> .strtable{ mso-number-format:\@; } table tr th,table tr td{border: 1px solid #000;} </style>
    </head>
    <body>
        <table class="table_view table-striped table-hover">
            <thead>
                <tr>
                    <th style="width:40px; text-align: center;">No</th>
                    <th style="width:150px; text-align: center;">Tanggal Pendaftaran</th>
                    <th style="width:150px; text-align: center;">Nomor Pendaftaran</th>
                    <th style="width:200px; text-align: center;">Nama Atlet</th>
                    <th style="width:100px; text-align: center;">Club</th>
                    <th style="width:200px; text-align: center;">Kejuaran</th>
                    <th style="width:200px; text-align: center;">Nomor Lomba</th>
                    <th style="width:150px; text-align: center;">Kelompok Umur</th>
                    <th style="width:150px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0;?> @forelse($results['data'] as $view_data) <?php $no++ ;?>
                    <tr>
                        <td class="strtable" style="text-align:center;">{{$no}}</td>
                        <td class="strtable" style="text-align:center;">{{ !empty($view_data['submitted_at']) ? \Carbon\Carbon::parse($view_data['submitted_at'])->translatedFormat('l, j F Y - H:i:s') : 'Belum ditentukan' }}</td>                        
                        <td class="strtable" style="text-align:center;">{{$view_data['code_data'] ?? 'Belum ditentukan'}}</td>
                        <td class="strtable" style="text-align:center;">{{$listdata['detail_atlet'][$view_data['code_data']]['nama'] ?? 'Belum ditentukan'}}</td>
                        <td class="strtable" style="text-align:center;">{{$listdata['detail_club'][$view_data['code_data']]['nama_club'] ?? 'Belum ditentukan'}}</td>
                        <td class="strtable" style="text-align:center;">{{$listdata['detail_champion'][$view_data['code_data']]['nama_kejuaraan'] ?? 'Belum ditentukan'}}</td>
                        <td class="strtable" style="text-align:center;"> 
                            @foreach($listdata['detail_event'][$view_data['code_data']] ?? [] as $event)
                                <div>{{ $event['code_event'] }}</div>
                            @endforeach
                        </td>
                        <td class="strtable" style="text-align:center;">
                            @foreach($listdata['detail_event'][$view_data['code_data']] ?? [] as $event)
                                <div>{{ $event['kelompok_umur']['code_kelompok'] }}</div>
                            @endforeach
                        </td>
                        <td class="strtable" style="text-align:center;">{{$view_data['status'] ?? 'Belum Ditentukan'}}</td>
                    </tr>
                @empty
                    <tr>
                        <td style="text-align:center; padding: 20px; background-color: #FFFFFF; cursor: default; font-weight: 600; font-size: 14px;" colspan="20">Tidak ada data yang tersedia</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>