@extends('admin.AdminOne.layout.assets')
@section('title', 'Dashboard Administrasi')

<style>
    .dashboard-wrapper {
    background: #f8fafc;
    min-height: 100vh;
}

.sidebar {
    width: 95px;
    background: white;
    border-right: 1px solid #e2e8f0;
    min-height: 100vh;
}

.logo {
    font-size: 24px;
    font-weight: bold;
    color: #2563eb;
    text-align: center;
}

.menu-item {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    color: #64748b;
    margin-bottom: 12px;
    text-decoration: none;
}

.menu-item.active {
    background: #2563eb;
    color: white;
}

.main-content {
    padding-left: 30px;
}

.hero-card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,.05);
}

.dashboard-card {
    background: white;
    padding: 24px;
    border-radius: 24px;
    box-shadow: 0 10px 25px rgba(0,0,0,.04);
}

.task-list {
    list-style: none;
    padding: 0;
}

.task-list li {
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}
</style>


@section('content')



    <div class="page_main">
        <div class="container-fluid text-left">
            <div class="row">
                <div class="col-md-12 bg_con_dash">
                    <div class="col-md-12 hd_page_main">Dashboard</div>
                </div>


                <!-- Content -->
                <div class="col main-content p-4">

                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold">Good Morning, Admin</h4>
                            <p class="text-muted">Monitoring dashboard Kesbangpol today</p>
                        </div>

                        <button class="btn btn-primary rounded-pill px-4">
                            + Tambah Data
                        </button>
                    </div>

                    <!-- Hero -->
                    <div class="hero-card p-4 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h3 class="fw-bold">Monitoring Organisasi Kemasyarakatan</h3>
                                <p>Kota Bukittinggi Dashboard Analytics</p>
                                <button class="btn btn-dark rounded-pill px-4">
                                    Lihat Laporan
                                </button>
                            </div>
                            <div class="col-md-5 text-end">
                                <img src="{{ asset('image/setting/maintenance.jpg') }}" width="250">
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="dashboard-card">
                                <small>Total Ormas</small>
                                <h3>348</h3>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="dashboard-card">
                                <small>Pending Verifikasi</small>
                                <h3>27</h3>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="dashboard-card">
                                <small>Surat Masuk</small>
                                <h3>89</h3>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="dashboard-card">
                                <small>Surat Keluar</small>
                                <h3>65</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Chart + Task -->
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="dashboard-card">
                                <h6>Grafik Pendaftaran Ormas</h6>
                                <canvas id="chartOrmas" height="120"></canvas>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="dashboard-card">
                                <h6>Task Monitoring</h6>
                                <ul class="task-list">
                                    <li>✔ Verifikasi Berkas</li>
                                    <li>✔ Review Surat Masuk</li>
                                    <li>⏳ Approval Kegiatan</li>
                                    <li>⏳ Arsip Dokumen</li>
                                </ul>
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



