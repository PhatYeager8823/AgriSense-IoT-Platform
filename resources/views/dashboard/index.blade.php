<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>AgriSense - Tổng quan</title>

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
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i><span>Tổng quan</span></a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Quản lý</div>
            <li class="nav-item">
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
                    <h5 class="m-0 font-weight-bold text-success">Hệ thống Giám sát Sâu bệnh Thông minh (PaaS Demo)</h5>
                </nav>

                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard Giám sát</h1>
                    </div>

                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tổng cảnh báo bệnh</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $detections->count() }} lượt</div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-bug fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Nhiệt độ hiện tại</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $latestSensor->temperature ?? 'N/A' }}°C
                                            </div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-thermometer-half fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Độ ẩm</div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                        {{ $latestSensor->humidity ?? 'N/A' }}%
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="progress progress-sm mr-2">
                                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $latestSensor->humidity ?? 0 }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-tint fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hệ thống</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">ONLINE</div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Biến thiên Môi trường (24h qua)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Tỷ lệ Loại bệnh</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small" id="chartLegend">
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto"><span>Copyright &copy; AgriSense 2026</span></div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>

    <script>
        // 1. DỮ LIỆU TỪ LARAVEL
        var dates = @json($sensorData->pluck('recorded_at')->map(function($date){ return $date->format('H:i'); })->reverse()->values());
        var temps = @json($sensorData->pluck('temperature')->reverse()->values());
        var humids = @json($sensorData->pluck('humidity')->reverse()->values());
        var allDetections = @json($detections);

        // --- 2. BỘ TỪ ĐIỂN DỊCH THUẬT (Dịch ngay tại đây cho chắc ăn) ---
        var dictionary = {
            'Healthy': 'Cây Khỏe Mạnh',
            'Early Blight': 'Nấm Đốm Vòng',
            'Late Blight': 'Nấm Sương Mai',
            'Leaf Miner': 'Sâu Vẽ Bùa',
            'Leaf_Miner': 'Sâu Vẽ Bùa', // Bắt trường hợp có gạch dưới
            'Yellow Leaf Curl Virus': 'Virus Xoăn Vàng Lá',
            'Mosaic Virus': 'Virus Khảm Lá',
            'Spider Mites': 'Nhện Đỏ',
            'Septoria': 'Đốm Lá Septoria',
            'Bacterial Spot': 'Đốm Vi Khuẩn'
        };

        // --- 3. BẢNG MÀU CHUẨN ---
        var colorMap = {
            'Cây Khỏe Mạnh': '#1cc88a',      // Xanh lá
            'Nấm Đốm Vòng': '#e74a3b',       // Đỏ
            'Nấm Sương Mai': '#f6c23e',      // Vàng cam
            'Sâu Vẽ Bùa': '#4e73df',         // Xanh dương
            'Virus Xoăn Vàng Lá': '#858796', // Xám đậm
            'Virus Khảm Lá': '#6f42c1',      // Tím
            'Nhện Đỏ': '#e83e8c',            // Hồng
            'Đốm Vi Khuẩn': '#36b9cc'        // Xanh ngọc
        };

        // --- 4. XỬ LÝ DỮ LIỆU ---
        var diseaseCounts = {};

        allDetections.forEach(function(item) {
            // Lấy tên gốc tiếng Anh
            var originalName = item.disease_name;

            // Dịch sang tiếng Việt (Nếu không có trong từ điển thì giữ nguyên)
            var vnName = dictionary[originalName] || originalName;

            if (diseaseCounts[vnName]) {
                diseaseCounts[vnName]++;
            } else {
                diseaseCounts[vnName] = 1;
            }
        });

        var pieLabels = Object.keys(diseaseCounts);
        var pieData = Object.values(diseaseCounts);

        // Ánh xạ màu theo tên tiếng Việt
        var pieColors = pieLabels.map(function(label) {
            return colorMap[label] || '#5a5c69'; // Mặc định màu xám nếu thiếu màu
        });

        // --- 5. VẼ BIỂU ĐỒ ---
        Chart.defaults.global.defaultFontFamily = 'Nunito';
        Chart.defaults.global.defaultFontColor = '#858796';

        // Biểu đồ đường (Giữ nguyên)
        var ctx = document.getElementById("myAreaChart");
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: "Nhiệt độ (°C)",
                    lineTension: 0.3,
                    borderColor: "#4e73df",
                    pointRadius: 0,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    data: temps,
                }, {
                    label: "Độ ẩm (%)",
                    lineTension: 0.3,
                    borderColor: "#1cc88a",
                    pointRadius: 0,
                    backgroundColor: "rgba(28, 200, 138, 0.05)",
                    data: humids,
                }],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{ gridLines: { display: false }, ticks: { maxTicksLimit: 7 } }],
                    yAxes: [{ ticks: { maxTicksLimit: 5, padding: 10 } }],
                },
                legend: { display: true }
            }
        });

        // Biểu đồ tròn (Đã sửa lỗi hiển thị)
        var ctxPie = document.getElementById("myPieChart");
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: pieColors,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                cutoutPercentage: 80,
                tooltips: {
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: true,
                    caretPadding: 10,
                },
            },
        });

        // Tạo chú thích (Legend) tiếng Việt
        var legendHtml = "";
        pieLabels.forEach(function(label, index) {
            var color = pieColors[index];
            legendHtml += '<span class="mr-2"><i class="fas fa-circle" style="color:' + color + '"></i> ' + label + '</span> ';
        });

        if (pieLabels.length === 0) {
            legendHtml = "Chưa có dữ liệu bệnh";
             // Vẽ demo 1 vòng tròn xám nếu trống
            new Chart(ctxPie, {type: 'doughnut', data: {datasets: [{data:[1], backgroundColor:['#eaecf4']}]}});
        }

        document.getElementById('chartLegend').innerHTML = legendHtml;

        // Auto reload 20s
        setTimeout(function(){ location.reload(); }, 20000);
    </script>
</body>
</html>
