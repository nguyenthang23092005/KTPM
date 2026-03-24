<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ADMIN – Quản lý hồ sơ nhân viên</title>
<link rel="stylesheet" href="{{ asset('resources/css/style.css') }}">
</head>
<body>

<div class="container">
    <!-- 1. Thanh menu bar chính -->
    <div class="menu-bar">
        <div>Tiện ích</div>
        <div>Công việc</div>
        <div>Nhân sự</div>
        <div>Tuyển dụng</div>
        <div>Đơn từ</div>
        <div>Yêu cầu</div>
        <div>Chấm công</div>
        <div>Đánh giá</div>
        <div>Tiền lương</div>
        <div>Tài sản</div>
        <div>Báo cáo</div>
        <div>Cấu hình</div>
        <div>Hệ thống</div>
    </div>

    <!-- 2. Sidebar danh sách nhân viên -->
    <div class="sidebar">
        <select>
            <option value="">Chọn trạng thái</option>
            <option>Đang làm</option>
            <option>Tạm nghỉ</option>
            <option>Nghỉ việc</option>
        </select>
        <input type="text" placeholder="Tìm kiếm theo tên/MNV">
        <ul class="employee-list">
            <li onclick="selectEmployee(this)">
                <img src="https://via.placeholder.com/35" alt="avatar">
                <div>
                    <div>Nguyễn Văn A</div>
                    <small>NV001</small>
                    <span class="badge dang-lam">Đang làm</span>
                </div>
            </li>
            <li onclick="selectEmployee(this)">
                <img src="https://via.placeholder.com/35" alt="avatar">
                <div>
                    <div>Trần Thị B</div>
                    <small>NV002</small>
                    <span class="badge tam-nghi">Tạm nghỉ</span>
                </div>
            </li>
        </ul>
    </div>

    <!-- 3. Main chi tiết nhân viên -->
    <div class="main">
        <div class="detail-top">
            <img src="https://via.placeholder.com/150x180" alt="ảnh nhân viên">
            <div class="detail-form">
                <div><label>Họ tên</label><input type="text"></div>
                <div><label>Mã nhân viên</label><input type="text"></div>
                <div><label>Phòng ban</label><input type="text"></div>
                <div><label>Chức vụ</label><input type="text"></div>
                <div><label>CCCD</label><input type="text"></div>
                <div><label>Ngày sinh</label><input type="date"></div>
                <div><label>Giới tính</label><input type="text"></div>
                <div><label>Tình trạng hôn nhân</label><input type="text"></div>
                <div><label>Quê quán</label><input type="text"></div>
                <div><label>SĐT</label><input type="text"></div>
                <div><label>Email</label><input type="email"></div>
                <div><label>Địa chỉ hiện tại</label><input type="text"></div>
                <div><label>Ngày làm việc</label><input type="date"></div>
                <div><label>Trạng thái</label><input type="text" class="badge dang-lam"></div>
                <div><label>Ảnh chân dung</label><input type="file"></div>
                <div><label>Dân tộc</label><input type="text"></div>
                <div><label>Tôn giáo</label><input type="text"></div>
                <div><label>Quốc tịch</label><input type="text"></div>
                <div><label>CV</label><button>Xem</button></div>
                <div><label>Hợp đồng lao động</label><button>Xem</button></div>
                <div><label>Ghi chú</label><input type="text"></div>
            </div>
        </div>

        <!-- Tabs ngang -->
        <div class="tabs">
            <button class="tab-btn active" onclick="openTab(event,'thongtin')">Thông tin chung</button>
            <button class="tab-btn" onclick="openTab(event,'hopdong')">Hợp đồng lao động</button>
            <button class="tab-btn" onclick="openTab(event,'kinhnghiem')">Kinh nghiệm</button>
            <button class="tab-btn" onclick="openTab(event,'bangcap')">Bằng cấp</button>
            <button class="tab-btn" onclick="openTab(event,'baohiem')">Bảo hiểm</button>
            <button class="tab-btn" onclick="openTab(event,'giadinh')">Quan hệ gia đình</button>
            <button class="tab-btn" onclick="openTab(event,'hoso')">Hồ sơ</button>
            <button class="tab-btn" onclick="openTab(event,'daotao')">Đào tạo</button>
            <button class="tab-btn" onclick="openTab(event,'nangluc')">Năng lực</button>
            <button class="tab-btn" onclick="openTab(event,'ketqua')">Kết quả đánh giá</button>
            <button class="tab-btn" onclick="openTab(event,'lichsu')">Lịch sử điều chuyển</button>
        </div>

        <!-- Nội dung tab -->
        <div id="thongtin" class="tab-content active"><p>Thông tin chung nhân viên...</p></div>
        <div id="hopdong" class="tab-content"><p>Hợp đồng lao động...</p></div>
        <div id="kinhnghiem" class="tab-content"><p>Kinh nghiệm làm việc...</p></div>
        <div id="bangcap" class="tab-content"><p>Bằng cấp...</p></div>
        <div id="baohiem" class="tab-content"><p>Bảo hiểm...</p></div>
        <div id="giadinh" class="tab-content"><p>Quan hệ gia đình...</p></div>
        <div id="hoso" class="tab-content"><p>Hồ sơ nhân viên...</p></div>
        <div id="daotao" class="tab-content"><p>Đào tạo nhân viên...</p></div>
        <div id="nangluc" class="tab-content"><p>Năng lực nhân viên...</p></div>
        <div id="ketqua" class="tab-content"><p>Kết quả đánh giá...</p></div>
        <div id="lichsu" class="tab-content"><p>Lịch sử điều chuyển...</p></div>
    </div>
</div>

<script>
function openTab(evt, tabName) {
    document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    evt.currentTarget.classList.add('active');
}

function selectEmployee(el) {
    alert('Đã chọn: ' + el.querySelector('div div').innerText);
}
</script>
</body>
</html>
