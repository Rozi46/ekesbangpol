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
                    <th style="width:250px; text-align: center;">Jabatan</th>
					<th style="width:70px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results['data'] as $view_data)
                    <tr>
                        <td class="strtable" style="text-align:center;">{{ $loop->iteration }}</td>
                        <td class="strtable" style="text-align:center;">{{$view_data['code_data']}}</td>
						<td class="strtable" >{{$view_data['jabatan']}}</td>
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