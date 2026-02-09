<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Camera AI - Lịch sử Giám sát</title>

    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-leaf"></i></div>
                <div class="sidebar-brand-text mx-3">AgriSense <sup>vn</sup></div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i><span>Tổng quan</span></a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Quản lý</div>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('iot.index') }}">
                    <i class="fas fa-fw fa-tractor"></i><span>Nông trại IoT</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('camera.index') }}">
                    <i class="fas fa-fw fa-video"></i><span>Camera AI</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('ai.consultant') }}">
                    <i class="fas fa-fw fa-user-md"></i><span>Tư vấn Chuyên gia AI</span></a>
            </li>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <h5 class="m-0 font-weight-bold text-success">Lịch sử Chụp & Phân tích Ảnh</h5>
                </nav>

                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách ảnh đã chụp</h6>
                            <a href="{{ route('ai.consultant') }}" class="btn btn-sm btn-success shadow-sm">
                                <i class="fas fa-arrow-right fa-sm text-white-50"></i> Qua trang Tư vấn Bệnh
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Thời gian</th>
                                            <th>Hình ảnh thực tế</th>
                                            <th>Kết quả Chẩn đoán</th>
                                            <th>Độ chính xác (Confidence)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detections as $item)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($item->detected_at)->format('H:i:s d/m/Y') }}</td>
                                            <td class="text-center">
                                                <a href="{{ $item->image_url }}" target="_blank">
                                                    <img src="{{ $item->image_url }}" height="80" style="border-radius: 5px; border: 1px solid #ddd; padding: 2px;">
                                                </a>
                                            </td>
                                            <td>
                                                @if($item->disease_name == 'Healthy' || $item->disease_name == 'Cây Khỏe Mạnh')
                                                    <span class="badge badge-success p-2" style="font-size: 0.9rem;">
                                                        <i class="fas fa-check-circle"></i> {{ $item->disease_name_vi }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger p-2" style="font-size: 0.9rem;">
                                                        <i class="fas fa-exclamation-triangle"></i> {{ $item->disease_name_vi }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <div class="d-flex align-items-center">
                                                    <span class="mr-2 font-weight-bold">{{ round($item->confidence * 100) }}%</span>
                                                    <div class="progress flex-grow-1" style="height: 15px;">
                                                        <div class="progress-bar {{ $item->confidence > 0.8 ? 'bg-success' : ($item->confidence > 0.5 ? 'bg-warning' : 'bg-danger') }}"
                                                             role="progressbar"
                                                             style="width: {{ $item->confidence * 100 }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-3 d-flex justify-content-center">{{ $detections->links() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto"><span>Copyright &copy; AgriSense Cloud Platform 2026</span></div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

</body>
</html>
