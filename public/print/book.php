<?php

    namespace App\Http\Controllers;

    require '../../vendor/autoload.php';
    require '../fpdf/MultiCellTable.php';
    require __DIR__ . '/../../resources/views/admin/AdminOne/layout/function.blade.php';

    use Illuminate\Http\{Request, Response};
    use Illuminate\Support\Facades\{Http, Route, Session, Hash};
    use Illuminate\Support\Carbon;
    use Jenssegers\Date\Date;
    use Artisan;
    use Cookie;
    use JWTAuth;
    use FPDF;
    use MultiCellTable;


    if(isset($_REQUEST['token'])){
    	$url_api = $_REQUEST['api'].'/';        
    	$admin_login = $_REQUEST['u'];
        $key_token = $_REQUEST['token'];
        $print_code = $_REQUEST['print_code'];

        function curlget($url){
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            $output = curl_exec($ch); 
            curl_close($ch);      
            return $output;
        }

        $get_user = curlget($url_api."v1/viewadminlogin?token=".$key_token."&u=".$admin_login);
        $get_user = json_decode($get_user, TRUE);
        
        if($get_user['status_message'] == 'failed'){
            echo "<meta http-equiv='refresh' content='0;/'>";
        }else{
            $get_data = curlget($url_api."v1/printbook?token=".$key_token."&u=".$admin_login."&code_data=".$print_code);
            $get_data = json_decode($get_data, TRUE);  
            $getdata = $get_data['results'];
        
            if($get_data['status_message'] == 'failed'){
                echo "<meta http-equiv='refresh' content='0;/'>";
            }else{
                class PDF extends MultiCellTable
                {  
                    protected $getdata;

                    function __construct()
                    {
                        parent::__construct();

                        $url_api     = $_REQUEST['api'] . '/';
                        $admin_login = $_REQUEST['u'];
                        $key_token   = $_REQUEST['token'];
                        $print_code  = $_REQUEST['print_code'];

                        $get_data = curlget($url_api."v1/printbook?token={$key_token}&u={$admin_login}&code_data={$print_code}&tipe_data=group");
                        $get_data = json_decode($get_data, TRUE);
                        $this->getdata = $get_data['results'];

                        // dd($this->getdata);
                    }

                    function Header()
                    {
                        // ===== Logo =====
                        $img = null;

                        // Prioritas: logo championship
                        if (!empty($this->getdata['championship']['logo'])) {
                            $pathChamp = __DIR__ . '/../themes/admin/AdminOne/image/upload/' . $this->getdata['championship']['logo'];
                            if (file_exists($pathChamp)) {
                                $img = $pathChamp;
                            }
                        }

                        // Fallback: foto perusahaan
                        if (!$img && !empty($this->getdata['detail_perusahaan']['foto'])) {
                            $pathCompany = __DIR__ . '/../themes/admin/AdminOne/image/public/' . $this->getdata['detail_perusahaan']['foto'];
                            if (file_exists($pathCompany)) {
                                $img = $pathCompany;
                            }
                        }

                        // Tampilkan jika ada
                        if ($img) {
                            $this->Image($img, 10, 6, 18);
                        }

                        // ===== Nama Event / Kejuaraan =====
                        $this->SetFont('Arial','B',14);
                        $this->Cell(0,7,strtoupper($this->getdata['championship']['nama_kejuaraan'] ?? 'HASIL PERTANDINGAN'),0,1,'C');

                        // ===== Lokasi + Tanggal =====
                        $this->SetFont('Arial','',10);
                        $this->Cell(0,6,
                            ($this->getdata['championship']['lokasi'] ?? ''),
                        0,1,'C');
                        $this->Cell(0,6,
                            (date('j F Y',strtotime($this->getdata['championship']['tanggal_mulai'])) ?? '') .
                            ' - ' .
                            (date('j F Y',strtotime($this->getdata['championship']['tanggal_selesai'])) ?? '') ,
                        0,1,'C');

                        $this->Ln(2);

                        // Garis
                        $y = $this->GetY();
                        $this->Line(10,$y,200,$y);
                        $this->Ln(3);
                    }

                    function Content()
                    {
                        foreach ($this->getdata['events'] as $tanggal => $events) {

                            // JUDUL HARI
                            $this->Ln(4);
                            $this->SetFont('Arial','B',10);
                            $this->Cell(0,6,
                                'HARI '.strtoupper(
                                    \Carbon\Carbon::parse($tanggal)
                                    ->locale('id')
                                    ->translatedFormat('l, d F Y')
                                ),
                            0,1,'C');

                            foreach ($events as $event) {

                                $kodeEvent = $event['code_event'];
                                $gaya      = $event['kategori']['nama_gaya'] ?? '';
                                $jarak     = number_format($event['jarak'] ?? 0, 0,"",".");

                                $genderRaw = strtolower($event['gender'] ?? '');
                                if ($genderRaw == 'laki-laki' || $genderRaw == 'male' || $genderRaw == 'm') {
                                    $gender = 'PA';
                                } elseif ($genderRaw == 'perempuan' || $genderRaw == 'female' || $genderRaw == 'f') {
                                    $gender = 'PI';
                                } else {
                                    $gender = '';
                                }
                                // $gender    = $event['gender'];
                                // $gender = strtolower($event['gender'] ?? '') == 'laki-laki' ? 'PA' : 'PI';

                                $kelompok  = $event['kelompok_umur']['nama_kelompok'] ?? '';                                

                                // JUDUL EVENT
                                $this->Ln(2);
                                $this->SetFont('Arial','B',10);
                                $this->Cell(0,6,
                                    // "EVENT {$kodeEvent} {$gender} - {$jarak} M {$gaya}",
                                    "EVENT {$kodeEvent}",
                                0,1,'L');

                                // KU + GENDER
                                // $this->SetFont('Arial','B',10);
                                // $this->Cell(0,5,
                                //     "{$kelompok} {$gender}",
                                // 0,1,'L');

                                // LOOP SERI (HEAT)
                                $heats = collect($event['heat_lines'])
                                    ->groupBy('code_heat');

                                foreach ($heats as $seri => $lines) {

                                    $this->SetFont('Arial','B',9);
                                    // $this->Cell(0,5,"SERI : ".$lines->first()['heat']['nomor_seri'],0,1,'L');
                                    $seriNomor = $lines->first()['heat']['nomor_seri'] ?? '';
                                    $this->Cell(0,5,"SERI : ".number_format($seriNomor ?? 0, 0,"","."),0,1,'L');
                                    


                                    // HEADER TABEL
                                    $this->SetFont('Arial','B',9);

                                    $this->SetWidths([12,45,12,12,12,32,30,20,20]);

                                    $this->RowHeader([
                                        ['LINE','C'],
                                        ['NAMA','C'],
                                        ['PA/PI','C'],
                                        ['YOB','C'],
                                        ['AGE','C'],
                                        ['CLUB','C'],
                                        ['KOTA','C'],
                                        ['BEST TIME','C'],
                                        ['HASIL','C'],
                                    ],6);

                                    // DATA ATLET
                                    $this->SetFont('Arial','',8);

                                    foreach ($lines as $l) {

                                        $atlet = $l['atlet'] ?? [];
                                        $club  = $atlet['club'] ?? [];

                                        $result = $l['result'][0]['hasil'] ?? '';

                                        $this->Row([
                                            [number_format($l['line_number'] ?? 0, 0,"","."), 'C'],
                                            [$atlet['nama'] ?? '', 'L'],
                                            [strtolower($atlet['gender'] ?? '') == 'laki-laki' ? 'PA' : 'PI' ?? '', 'C'],
                                            [substr($atlet['tanggal_lahir'] ?? '',0,4), 'C'],
                                            [$event['kelompok_umur']['code_kelompok'] ?? '', 'C'],
                                            [$club['nama_club'] ?? '', 'L'],
                                            [$club['kota_asal'] ?? '', 'L'],
                                            [$l['best_time'] ?? '', 'C'],
                                            [$result, 'C'],
                                        ]);
                                    }

                                    $this->Ln(2);
                                }
                            }
                        }
                    }

                    function Footer()
                    {
                        $this->SetY(-10);
                        $this->SetFont('Arial','I',8);
                        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
                    }
                }

                try {
                    $pdf = new PDF('P', 'mm', 'A4');
                    $pdf->SetMargins(5,5,10); // kiri, atas, kanan
                    $pdf->SetAutoPageBreak(true, 10); // Margin bawah=10
                    // $pdf->SetAutoPageBreak(false);
                    $title = 'Book | AQUA TRACK';
                    $pdf->SetTitle($title);
                    $pdf->SetAuthor('AQUA TRACK');	
                    $pdf->AliasNbPages();
                    $pdf->AddPage();
                    $pdf->Content();
                    $pdf->Output('I','Print-'.$print_code.'.pdf');
                } catch (Exception $e) {
                    echo "Error generating PDF: " . $e->getMessage();
                }
            }
        }
    }else{
        echo "<meta http-equiv='refresh' content='0;/'>";
    }


?>