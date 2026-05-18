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
                    <th style="width:150px; text-align: center;">Kode Data</th>
                    <th style="width:400px; text-align: center;">Nama Kejuaraan</th>
                    <th style="width:400px; text-align: center;">Lokasi</th>
                    <th style="width:400px; text-align: center;">Waktu Pelaksanaan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0;?> @forelse($results['data'] as $view_data) <?php $no++ ;?>
                    <tr>
                        <td class="strtable" style="text-align:center;">{{$no}}</td>
                        <td class="strtable" style="text-align:center;">{{$view_data['code_data'] ?? 'Belum ditentukan'}} </td>                        
                        <td class="strtable" >{{$view_data['nama_kejuaraan'] ?? 'Belum ditentukan'}}</td>
                        <td class="strtable" style="text-align:center;">{{$view_data['lokasi'] ?? 'Belum ditentukan'}}</td>
                        <td class="strtable" style="text-align:center;">
                            {{ !empty($view_data['tanggal_mulai']) 
                                ? \Carbon\Carbon::parse($view_data['tanggal_mulai'])->translatedFormat('d F Y') 
                                : 'Belum ditentukan' 
                            }} 
                            s.d 
                            {{ !empty($view_data['tanggal_selesai']) 
                                ? \Carbon\Carbon::parse($view_data['tanggal_selesai'])->translatedFormat('d F Y') 
                                : 'Belum ditentukan' 
                            }}
                        </td>

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