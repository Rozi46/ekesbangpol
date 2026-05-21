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
                    <th style="width:200px; text-align: center;">Kode Data</th>
                    <th style="width:250px; text-align: center;">Photo Berita</th>
                    <th style="width:250px; text-align: center;">Judul Berita</th>
                    <th style="width:250px; text-align: center;">Isi Berita</th>
                    <th style="width:100px; text-align: center;">Sumber Berita</th>
					<th style="width:70px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results['data'] as $view_data)
                    <tr>
                        <td class="strtable" style="text-align:center;">{{ $loop->iteration }}</td>
                        <td class="strtable" style="text-align:center;">{{$view_data['code_data']}}</td>
                        <td style="text-align:center; vertical-align:middle;">
                            @if(!empty($view_data['tumb_berita']))
                                <img src="{{ public_path('/image/post/' . $view_data['tumb_berita']) }}" width="80" height="80">
                            @else
                                Tidak ada foto
                            @endif
                        </td>
						<td class="strtable" >{{$view_data['judul_berita']}}</td>
                        @php
                            $isiBerita = strip_tags( preg_replace('/<\/p>\s*<p>/i', "\n", $view_data['isi_berita']) );
                        @endphp
                        <td class="strtable" style="white-space: pre-line;">{{ trim($isiBerita) }}</td>
                        <td class="strtable" style="text-align:center;">{{$view_data['sumber_berita']}}</td>
						<td class="strtable" style="text-align:center;">{{$view_data['status_data']}}</td>
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