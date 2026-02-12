<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>IoT Farm - Quản lý Thiết bị & Môi trường</title>

    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        .custom-switch-lg .custom-control-label::before { width: 3rem; border-radius: 1rem; }
        .custom-switch-lg .custom-control-label::after { border-radius: 1rem; }
        .custom-switch-lg .custom-control-input:checked ~ .custom-control-label::before { background-color: #1cc88a; border-color: #1cc88a; }
    </style>
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
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('iot.index') }}">
                    <i class="fas fa-fw fa-tractor"></i><span>Nông trại IoT</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('camera.index') }}">
                    <i class="fas fa-fw fa-video"></i><span>Camera AI</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('ai.consultant') }}">
                    <i class="fas fa-fw fa-user-md"></i>
                    <span>Tư vấn Chuyên gia AI</span></a>
            </li>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <h5 class="m-0 font-weight-bold text-success">Hệ thống Điều khiển & Giám sát Môi trường</h5>
                </nav>

                <div class="container-fluid">

                    <div class="row">

                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4 h-100">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-cogs"></i> Bảng Điều khiển (IoT Control)
                                    </h6>
                                    <span class="badge badge-success">Chế độ: Bán tự động</span>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div>
                                            <h5 class="font-weight-bold text-gray-800">Máy bơm tưới</h5>
                                            <small>Trạng thái: <span id="pumpStatus" class="text-danger">Đang tắt</span></small>
                                        </div>
                                        <div class="custom-control custom-switch custom-switch-lg">
                                            <input type="checkbox" class="custom-control-input" id="switchPump" onchange="toggleDevice('pump')">
                                            <label class="custom-control-label" for="switchPump"></label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div>
                                            <h5 class="font-weight-bold text-gray-800">Quạt thông gió</h5>
                                            <small>Trạng thái: <span id="fanStatus" class="text-success font-weight-bold">Đang bật</span></small>
                                        </div>
                                        <div class="custom-control custom-switch custom-switch-lg">
                                            <input type="checkbox" class="custom-control-input" id="switchFan" onchange="toggleDevice('fan')">
                                            <label class="custom-control-label" for="switchFan"></label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="font-weight-bold text-gray-800">Đèn sưởi đêm</h5>
                                            <small>Trạng thái: <span id="lightStatus" class="text-danger">Đang tắt</span></small>
                                        </div>
                                        <div class="custom-control custom-switch custom-switch-lg">
                                            <input type="checkbox" class="custom-control-input" id="switchLight" onchange="toggleDevice('light')">
                                            <label class="custom-control-label" for="switchLight"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4 h-100 border-left-warning">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-warning">
                                        <i class="fas fa-robot"></i> Trợ lý AI Khuyến nghị
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if(isset($latestDetection) && $latestDetection->disease_name != 'Healthy')
                                        <div class="alert alert-warning">
                                            <h5 class="alert-heading font-weight-bold">
                                                <i class="fas fa-exclamation-triangle"></i> Phát hiện: {{ $latestDetection->disease_name_vi }}
                                            </h5>
                                            <p class="mb-1">Thời gian: {{ $latestDetection->detected_at->format('H:i d/m/Y') }}</p>
                                            <hr>
                                            <p class="mb-0 font-weight-bold">Đề xuất xử lý:</p>
                                            <ul class="pl-3 mb-0 mt-2">
                                                @if(str_contains($latestDetection->disease_name, 'Early Blight') || str_contains($latestDetection->disease_name, 'Sương mai'))
                                                    <li><strong>TƯỚI TIÊU:</strong> Nên <span class="text-danger">TẮT MÁY BƠM</span> ngay để giảm độ ẩm.</li>
                                                    <li><strong>MÔI TRƯỜNG:</strong> Bật quạt thông gió để làm khô lá.</li>
                                                    <li><strong>CAN THIỆP:</strong> Cắt tỉa các lá vàng đốm nâu.</li>
                                                @elseif(str_contains($latestDetection->disease_name, 'Héo') || str_contains($latestDetection->disease_name, 'Wilt'))
                                                    <li><strong>TƯỚI TIÊU:</strong> Cây thiếu nước, hãy BẬT MÁY BƠM nhẹ.</li>
                                                    <li><strong>KIỂM TRA:</strong> Xem lại nấm rễ.</li>
                                                @else
                                                    <li>Cách ly cây bệnh ra khỏi khu vực trồng chung.</li>
                                                    <li>Theo dõi diễn biến trong 24h tới.</li>
                                                @endif
                                            </ul>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a href="{{ route('camera.index') }}" class="btn btn-warning btn-icon-split">
                                                <span class="icon text-white-50"><i class="fas fa-search"></i></span>
                                                <span class="text">Xem ảnh chi tiết bệnh</span>
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <div class="mb-3">
                                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                            </div>
                                            <h4 class="text-success font-weight-bold">Cây trồng khỏe mạnh!</h4>
                                            <p class="text-gray-600 mb-0">Hệ thống không phát hiện dấu hiệu bất thường.</p>
                                            <hr class="w-50 mx-auto mt-3">
                                            <small class="text-muted">Tiếp tục duy trì chế độ tưới tiêu tiêu chuẩn.</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-clipboard-list"></i> Nhật ký Môi trường (Real-time Log)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Thời gian đo</th>
                                            <th>Nhiệt độ (°C)</th>
                                            <th>Độ ẩm không khí (%)</th>
                                            <th>Trạng thái môi trường</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sensorData as $data)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($data->recorded_at)->format('H:i:s d/m/Y') }}</td>
                                            <td>
                                                <span class="{{ $data->temperature > 35 ? 'text-danger font-weight-bold' : '' }}">
                                                    {{ $data->temperature }}°C
                                                </span>
                                            </td>
                                            <td>{{ $data->humidity }}%</td>
                                            <td>
                                                @if($data->soil_moisture < 45)
                                                    <span class="badge badge-danger p-2"><i class="fas fa-tint-slash"></i> Đất quá khô ({{$data->soil_moisture}}%)</span>
                                                @elseif($data->temperature >= 30)
                                                    <span class="badge badge-warning p-2"><i class="fas fa-temperature-high"></i> Nóng ({{$data->temperature}}°C)</span>
                                                @elseif($data->temperature < 16)
                                                    <span class="badge badge-info p-2"><i class="fas fa-snowflake"></i> Lạnh ({{$data->temperature}}°C)</span>
                                                @else
                                                    <span class="badge badge-success p-2"><i class="fas fa-check"></i> Lý tưởng</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $sensorData->links() }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; AgriSense Cloud Platform 2026</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

    <script>
    // 1. LẤY DỮ LIỆU TỪ LARAVEL (Đã được Python gửi lên DB)
    // Lấy dòng dữ liệu mới nhất
    // Code cũ của bạn có thể gây lỗi nếu DB trống. Hãy thay bằng đoạn này:
    var latestData = {
        temp: {{ $currentStatus['temperature'] }},
        humid: {{ $currentStatus['humidity'] }},
        soil: {{ $currentStatus['soil_moisture'] }},
        pump: {{ $currentStatus['pump_status'] }},
        fan: {{ $currentStatus['fan_status'] }},
        heater: {{ $currentStatus['heater_status'] }}
    };

    $(document).ready(function() {
        console.log("Dữ liệu mới nhất:", latestData);

        // --- CẬP NHẬT TRẠNG THÁI THIẾT BỊ (THEO PYTHON) ---

        // 1. Máy Bơm (Dựa trên giá trị DB gửi về)
        if (latestData.pump == 1) {
            setDeviceState('pump', true, 'ĐANG BẬT (Auto)');
        } else {
            setDeviceState('pump', false, 'Đang tắt');
        }

        // 2. Quạt Thông Gió
        if (latestData.fan == 1) {
            setDeviceState('fan', true, 'ĐANG BẬT (Làm mát)');
        } else {
            setDeviceState('fan', false, 'Đang tắt');
        }

        // 3. Đèn Sưởi
        if (latestData.heater == 1) {
            setDeviceState('light', true, 'ĐANG BẬT (Sưởi ấm)');
        } else {
            setDeviceState('light', false, 'Đang tắt');
        }
    });

    // Hàm cập nhật giao diện switch và nhãn
    function setDeviceState(deviceName, isOn, statusText) {
        // Tên ID trong HTML: switchPump, switchFan, switchLight
        // Tên ID nhãn: pumpStatus, fanStatus, lightStatus

        // Chữ cái đầu viết hoa cho ID (pump -> Pump)
        let capName = deviceName.charAt(0).toUpperCase() + deviceName.slice(1);

        let switchId = '#switch' + capName;
        let labelId = '#' + deviceName + 'Status'; // Ví dụ: #lightStatus

        if (isOn) {
            $(switchId).prop('checked', true);
            $(labelId).html(statusText);
            $(labelId).removeClass('text-danger').addClass('text-success font-weight-bold blink-animation');
        } else {
            $(switchId).prop('checked', false);
            $(labelId).html(statusText);
            $(labelId).removeClass('text-success font-weight-bold blink-animation').addClass('text-danger');
        }
    }

    // Hiệu ứng nhấp nháy
    $('<style>.blink-animation { animation: blinker 1.5s linear infinite; } @keyframes blinker { 50% { opacity: 0.5; } }</style>').appendTo('head');

    // Reload trang sau 5s để cập nhật nhanh hơn
    setTimeout(function(){ location.reload(); }, 5000);
    </script>
</body>
</html>
