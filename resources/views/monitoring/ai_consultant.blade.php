<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Phòng khám Cây trồng AI</title>
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .chat-container { display: flex; flex-direction: column; height: 75vh; }
        .chat-box { flex: 1; overflow-y: auto; background: #f8f9fc; padding: 20px; border-bottom: 1px solid #e3e6f0; }
        .message-bot { background: #e1f5fe; padding: 12px 18px; border-radius: 15px; border-bottom-left-radius: 0; margin-bottom: 15px; color: #333; max-width: 80%; float: left; clear: both; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        .message-user { background: #1cc88a; color: white; padding: 12px 18px; border-radius: 15px; border-bottom-right-radius: 0; margin-bottom: 15px; text-align: right; display: inline-block; float: right; clear: both; max-width: 80%; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        .disease-item { cursor: pointer; transition: all 0.2s; border-left: 4px solid transparent; }
        .disease-item:hover { background-color: #f1f3f9; border-left: 4px solid #1cc88a; }
        .typing { font-style: italic; color: #888; font-size: 0.85rem; display: none; margin-bottom: 10px; clear: both; float: left; margin-left: 10px;}
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
            <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-fw fa-tachometer-alt"></i><span>Tổng quan</span></a></li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Quản lý</div>
            <li class="nav-item"><a class="nav-link" href="{{ route('iot.index') }}"><i class="fas fa-fw fa-tractor"></i><span>Nông trại IoT</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('camera.index') }}"><i class="fas fa-fw fa-video"></i><span>Camera AI</span></a></li>
            <li class="nav-item active"><a class="nav-link" href="{{ route('ai.consultant') }}"><i class="fas fa-fw fa-user-md"></i><span>Tư vấn Chuyên gia AI</span></a></li>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <h5 class="m-0 font-weight-bold text-success"><i class="fas fa-robot"></i> Trợ lý Nông nghiệp Thông minh</h5>
                </nav>

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card shadow mb-4" style="height: 75vh;">
                                <div class="card-header py-3 bg-success text-white">
                                    <h6 class="m-0 font-weight-bold">Bệnh phát hiện hôm nay</h6>
                                </div>
                                <div class="card-body p-0" style="overflow-y: auto;">
                                    <div class="list-group list-group-flush">
                                        @if($detectedDiseases->isEmpty())
                                            <div class="p-4 text-center text-muted">
                                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i><br>Hệ thống sạch bệnh!
                                            </div>
                                        @else
                                            @foreach($detectedDiseases as $disease)
                                            <div class="list-group-item disease-item p-3" onclick="askForDisease('{{ $disease->disease_name_vi }}')">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $disease->image_url }}" class="rounded-circle mr-3" width="50" height="50" style="object-fit: cover; border: 2px solid #ddd;">
                                                    <div>
                                                        <h6 class="mb-0 font-weight-bold text-dark">{{ $disease->disease_name_vi }}</h6>
                                                        <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $disease->total_count }} lần</small>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-primary ml-auto"><i class="fas fa-angle-right"></i></button>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card shadow mb-4 chat-container">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-bottom-success">
                                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-comments"></i> Tư vấn trực tuyến với Gemini 2.5</h6>

                                    <button class="btn btn-sm btn-light text-danger font-weight-bold" onclick="clearHistoryServer()">
                                        <i class="fas fa-trash"></i> Xóa lịch sử vĩnh viễn
                                    </button>
                                </div>

                                <div id="chatHistory" class="chat-box">
                                    @if($chatHistory->isEmpty())
                                        <div class="message-bot">Xin chào! Tôi là Trợ lý AgriSense. Hãy chọn bệnh hoặc đặt câu hỏi nhé! 🌱</div>
                                    @else
                                        @foreach($chatHistory as $chat)
                                            <div style="clear:both; display:block; width:100%;">
                                                <div class="message-user">{{ $chat->user_message }}</div>
                                            </div>
                                            <div style="clear:both; display:block; width:100%;">
                                                <div class="message-bot">
                                                    <strong><i class="fas fa-robot"></i> AgriBot:</strong><br>{!! $chat->bot_response !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="typing" id="typingIndicator"><i class="fas fa-circle-notch fa-spin"></i> AI đang soạn tin...</div>

                                <div class="card-footer bg-white">
                                    <div class="input-group">
                                        <input type="text" id="userInput" class="form-control bg-light border-0 small" placeholder="Nhập câu hỏi..." style="height: 50px;">
                                        <div class="input-group-append">
                                            <button class="btn btn-success" type="button" id="btnSend" onclick="sendCustomQuestion()"><i class="fas fa-paper-plane"></i> Gửi</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="sticky-footer bg-white"><div class="container my-auto"><div class="copyright text-center my-auto"><span>Copyright &copy; AgriSense 2026</span></div></div></footer>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

    <script>
        // Lấy ngữ cảnh cuối cùng từ PHP (nếu có lịch sử) để chat tiếp
        var currentContext = "{{ $chatHistory->last()->context ?? '' }}";

        // Scroll xuống cuối khi mới vào trang
        $(document).ready(function() { scrollToBottom(); });

        function askForDisease(diseaseName) {
            currentContext = diseaseName;
            appendMessage("Tư vấn giúp tôi về bệnh: " + diseaseName, 'user');
            callAiApi({ disease: diseaseName });
        }

        function sendCustomQuestion() {
            var question = $('#userInput').val().trim();
            if (question === "") return;
            appendMessage(question, 'user');
            $('#userInput').val('');
            callAiApi({ question: question, context: currentContext });
        }

        function callAiApi(dataPayload) {
            $('#typingIndicator').show();
            scrollToBottom();
            dataPayload._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: "{{ route('ask.ai') }}",
                type: "POST",
                data: dataPayload,
                success: function(response) {
                    $('#typingIndicator').hide();
                    // Cập nhật context mới nếu server trả về
                    if(response.context) currentContext = response.context;
                    appendMessage(response.answer, 'bot');
                },
                error: function() {
                    $('#typingIndicator').hide();
                    appendMessage("Lỗi kết nối!", 'bot text-danger');
                }
            });
        }

        // --- HÀM XÓA LỊCH SỬ TRÊN SERVER ---
        function clearHistoryServer() {
            if(!confirm("Bạn có chắc muốn xóa toàn bộ lịch sử trò chuyện vĩnh viễn không?")) return;

            $.ajax({
                url: "{{ route('clear.chat') }}",
                type: "POST",
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#chatHistory').html('<div class="message-bot">Đã xóa sạch dữ liệu. Mời bạn bắt đầu lại.</div>');
                    currentContext = ''; // Reset ngữ cảnh
                }
            });
        }

        function appendMessage(text, type) {
            var className = (type === 'user') ? 'message-user' : 'message-bot';
            var icon = (type === 'bot') ? '<strong><i class="fas fa-robot"></i> AgriBot:</strong><br>' : '';
            $('#chatHistory').append(`<div style="clear:both; display:block; width:100%;"><div class="${className}">${icon}${text}</div></div>`);
            scrollToBottom();
        }

        function scrollToBottom() {
            var chatBox = document.getElementById("chatHistory");
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        $('#userInput').keypress(function(e){ if(e.which == 13) $('#btnSend').click(); });
    </script>
</body>
</html>
