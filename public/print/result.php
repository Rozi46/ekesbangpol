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
            $get_data = curlget($url_api."v1/printresult?token=".$key_token."&u=".$admin_login."&code_data=".$print_code);
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

                        $get_data = curlget($url_api."v1/printresult?token={$key_token}&u={$admin_login}&code_data={$print_code}&tipe_data=group");
                        $get_data = json_decode($get_data, TRUE);
                        $this->getdata = $get_data['results'];

                        // dd($this->getdata);
                    }

                    function Header()
                    {
                        // ===== Logo =====
                        // if (!empty($this->getdata['detail_perusahaan']['foto'])) {
                        //     $img = __DIR__ . '/../themes/admin/AdminOne/image/public/' . $this->getdata['detail_perusahaan']['foto'];
                        //     if (file_exists($img)) {
                        //         $this->Image($img, 10, 6, 22);
                        //     }
                        // }
                        
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
                        if (empty($this->getdata['events'])) {
                            $this->SetFont('Arial','',10);
                            $this->Cell(0,8,'Tidak ada data hasil',0,1,'C');
                            return;
                        }

                        foreach ($this->getdata['events'] as $event) {
                            // ===== Judul Event =====
                            $this->Ln(3);
                            $this->SetFont('Arial','B',11);
                            $this->Cell(0,6,strtoupper($event['code_event'] ?? ''),0,1,'L');

                            // ===== Info Event =====
                            $this->SetFont('Arial','',9);

                            $kategori = is_array($event['kategori'] ?? null)
                                ? ($event['kategori']['nama_gaya'] ?? '')
                                : ($event['kategori'] ?? '');

                            $gender = $event['gender'] ?? '';
                            $jarak  = number_format($event['jarak'] ?? 0, 0,"",".");                            

                            $this->Cell(0,5,
                                $kategori . '  ' . $gender . '  ' . $jarak . ' M ',
                            0,1,'L');

                            $this->Ln(1);

                            // ===== Header Tabel =====
                            $this->SetFont('Arial','B',9);
                            $this->SetWidths([15,50,40,40,25,25]); // total lebar 195

                            $this->RowHeader([
                                ['Rank','C'],
                                ['Name','C'],
                                ['Club','C'],
                                ['City','C'],
                                ['Best Time','C'],
                                ['Result Time','C']
                            ],6);

                            // ===== Isi Result =====
                            $this->SetFont('Arial','',9);

                            if (!empty($event['result'])) {
                                foreach ($event['result'] as $r) {
                                    $bestTime = $r['heat_line']['best_time'] 
                                        ?? $r['heatLine']['best_time'] 
                                        ?? '';

                                    $this->Row([
                                        [$r['ranking'] ?? '', 'C'],
                                        [$r['atlet']['nama'] ?? '', 'L'],
                                        [$r['atlet']['club']['nama_club'] ?? '', 'L'],
                                        [$r['atlet']['club']['kota_asal'] ?? '', 'L'],
                                        [$bestTime, 'C'],
                                        [$r['hasil'] ?? '', 'C'],
                                    ]);
                                }

                            } else {
                                $this->Cell(0,6,'Tidak ada hasil',1,1,'C');
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
                    $title = 'Result | AQUA TRACK';
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