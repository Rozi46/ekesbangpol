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
                    <th style="width:150px; text-align: center;">Nip</th>
                    <th style="width:250px; text-align: center;">Nama</th>
                    <th style="width:200px; text-align: center;">Gender</th>
                    <th style="width:250px; text-align: center;">Jabatan</th>
                    <th style="width:200px; text-align: center;">Email</th>
                    <th style="width:100px; text-align: center;">Nomor HP</th>
					<th style="width:100px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results['data'] as $view_data)
                    <tr>
                        <td class="strtable" style="text-align:center;">{{ $loop->iteration }}</td>
                        <td class="strtable" style="text-align:center;">{{ $view_data['nip'] }}</td>
						<td class="strtable">{{ $view_data['nama_pegawai'] }}</td>
                        <td class="strtable" style="text-align:center;">{{ $view_data['gender'] }}</td>
                        <td class="strtable">{{ $view_data['position']['jabatan'] }}</td>
                        <td class="strtable" style="text-align:center;">{{ $view_data['email'] }}</td>
                        <td class="strtable" style="text-align:center;">{{ $view_data['nomor_hp'] }}</td>
						<td class="strtable" style="text-align:center;">{{ $view_data['status_data'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td style="text-align:center; padding: 20px; background-color: #FFFFFF; cursor: default; font-weight: 600; font-size: 14px;" colspan="8">Tidak ada data yang tersedia</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>