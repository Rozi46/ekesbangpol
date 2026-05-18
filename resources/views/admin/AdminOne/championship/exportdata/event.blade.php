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
                    <th style="width:200px; text-align: center;">Nomor Lomba</th>
                    <th style="width:400px; text-align: center;">Nama Lomba</th>
                    <th style="width:150px; text-align: center;">Waktu Pelaksanaan</th>
                    <th style="width:400px; text-align: center;">Nama Kejuaraan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0;?> @forelse($results['data'] as $view_data) <?php $no++ ;?>
                    <tr>
                        <td class="strtable" style="text-align:center;">{{$no}}</td>
                        <td class="strtable" >{{$view_data['code_event'] ?? 'Belum ditentukan'}} </td>    
                        @php
                            $jarak = isset($view_data['jarak']) ? number_format($view_data['jarak'], 0, ',', '') . ' M' : 'Belum ditentukan';
                            $gaya = $view_data['kategori']['nama_gaya'] ?? '';
                            $kelompok = $view_data['kelompok_umur']['code_kelompok'] ?? '';
                            $gender = $view_data['gender'] ?? '';
                            $hasil = trim("$jarak $gaya $kelompok $gender");
                        @endphp
                        <td class="strtable" style="text-align:center;">{{ $hasil }}</td>                    
                        <td class="strtable" style="text-align:center;">{{ !empty($view_data['tanggal']) ? \Carbon\Carbon::parse($view_data['tanggal'])->translatedFormat('d F Y') : 'Belum ditentukan' }}</td>
                        <td class="strtable" style="text-align:center;">{{ $view_data['championship']['nama_kejuaraan'] ?? 'Belum ditentukan' ?? 'Belum ditentukan'}}</td>
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