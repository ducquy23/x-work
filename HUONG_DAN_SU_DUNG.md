# HƯỚNG DẪN SỬ DỤNG HỆ THỐNG QUẢN LÝ CÔNG VIỆC XWORK

## 📋 TỔNG QUAN

Hệ thống quản lý công việc XWORK được xây dựng trên Laravel 10 và Filament PHP 3, cung cấp đầy đủ các tính năng quản lý công việc, dự án, phòng ban và báo cáo KPI.

## 🚀 CÁC BƯỚC KHỞI ĐỘNG

### 1. Chạy Migrations và Seeders

```bash
php artisan migrate
php artisan db:seed
```

Lệnh này sẽ:
- Tạo tất cả các bảng trong database
- Tạo 5 cấp độ phân quyền: Nhân viên, Trưởng phòng, Giám đốc DA, Ban điều hành, Chủ tịch

### 2. Tạo tài khoản Admin đầu tiên

```bash
php artisan make:filament-user
```

Hoặc tạo trực tiếp trong database:
- Email: admin@xwork.com
- Password: password (sau đó đổi mật khẩu)

### 3. Gán quyền cho User

Sau khi đăng nhập vào hệ thống tại `/admin`, bạn có thể:
- Vào menu **Shield** → **Roles** để quản lý vai trò
- Vào menu **Người dùng** để gán vai trò cho từng user

## 📁 CẤU TRÚC HỆ THỐNG

### 1. QUẢN LÝ NGƯỜI DÙNG (User Resource)
**Đường dẫn:** `/admin/users`

**Tính năng:**
- ✅ Quản lý thông tin người dùng (tên, email, phòng ban, chức vụ)
- ✅ Upload avatar
- ✅ Gán vai trò (5 cấp độ phân quyền)
- ✅ Quản lý trạng thái (Hoạt động/Không hoạt động)
- ✅ Tìm kiếm và lọc theo phòng ban, vai trò, trạng thái

**5 Cấp độ phân quyền:**
1. **Nhân viên** (nhan-vien) - Xem và thực hiện công việc được giao
2. **Trưởng phòng** (truong-phong) - Quản lý phòng ban và nhân viên
3. **Giám đốc DA** (giam-doc-da) - Quản lý dự án
4. **Ban điều hành** (ban-dieu-hanh) - Xem báo cáo và quản lý toàn bộ
5. **Chủ tịch** (chu-tich) - Toàn quyền

### 2. QUẢN LÝ PHÒNG BAN (Department Resource)
**Đường dẫn:** `/admin/departments`

**Tính năng:**
- ✅ Tạo và quản lý phòng ban
- ✅ Xem số lượng nhân viên và dự án của mỗi phòng ban
- ✅ Mô tả chi tiết phòng ban

### 3. QUẢN LÝ DỰ ÁN (Project Resource)
**Đường dẫn:** `/admin/projects`

**Tính năng:**
- ✅ Tạo dự án với thông tin: tên, mô tả, phòng ban
- ✅ Quản lý thời gian: ngày bắt đầu, ngày kết thúc
- ✅ Trạng thái dự án: Lập kế hoạch, Đang thực hiện, Tạm dừng, Hoàn thành, Đã hủy
- ✅ Xem số lượng công việc của mỗi dự án

### 4. QUẢN LÝ CÔNG VIỆC (Task Resource)
**Đường dẫn:** `/admin/tasks`

**Tính năng chính:**
- ✅ **Tạo & Giao việc:**
  - Tiêu đề, mô tả
  - Gán cho người thực hiện
  - Độ ưu tiên: Khẩn, Cao, Trung bình, Thấp
  - Hạn hoàn thành (Deadline)
  - Trạng thái: Mới, Đang thực hiện, Đang xem xét, Hoàn thành, Đã hủy

- ✅ **Quản lý tiến độ:**
  - Thanh % hoàn thành (0-100%)
  - Tự động đếm ngày trễ hạn
  - Hiển thị màu cảnh báo cho công việc trễ hạn

- ✅ **Chi tiết công việc (Relation Managers):**
  - **Checklist:** Danh sách công việc con, đánh dấu hoàn thành
  - **Bình luận:** Trao đổi về công việc
  - **File đính kèm:** Upload hình ảnh, video, PDF/DOC (tối đa 10MB)

### 5. DASHBOARD (Tổng quan)
**Đường dẫn:** `/admin`

**Widgets hiển thị:**
- 📊 **Tasks Overview:** Thống kê tổng công việc, đang thực hiện, hoàn thành, trễ hạn
- 📈 **Tasks Chart:** Biểu đồ tròn hiển thị công việc theo trạng thái
- ⚠️ **Overdue Tasks:** Bảng danh sách công việc trễ hạn cần xử lý ngay

### 6. KHÔNG GIAN CÁ NHÂN (Profile)
**Đường dẫn:** `/admin/profile`

**Tính năng:**
- ✅ Xem và chỉnh sửa thông tin cá nhân
- ✅ Upload avatar
- ✅ Xem danh sách công việc được giao
- ✅ Xem nhật ký hoạt động (sẽ tích hợp Activity Log)

### 7. LỊCH LÀM VIỆC (Calendar)
**Đường dẫn:** `/admin/tasks-calendar`

**Tính năng:**
- ✅ Xem công việc theo lịch: Ngày/Tuần/Tháng
- ✅ Màu sắc theo độ ưu tiên:
  - 🔴 Đỏ: Khẩn
  - 🟠 Cam: Cao
  - 🔵 Xanh: Trung bình
  - ⚫ Xám: Thấp
- ✅ Click vào sự kiện để xem chi tiết công việc
- ✅ Kéo thả để đổi deadline (sẽ phát triển thêm)

### 8. BÁO CÁO & KPI
**Đường dẫn:** `/admin/reports`

**Tính năng:**
- ✅ **KPI Dashboard:**
  - Tỷ lệ hoàn thành công việc
  - Tỷ lệ đúng hạn
  - Số công việc trễ hạn
  - KPI trung bình của nhân viên

- ✅ **Hiệu suất theo phòng ban:**
  - Số nhân viên, số dự án
  - Tổng công việc, đã hoàn thành
  - Tỷ lệ hoàn thành

- ✅ **Phân tích theo độ ưu tiên:**
  - Biểu đồ cột hiển thị số lượng công việc theo từng mức độ ưu tiên

## 🔐 PHÂN QUYỀN VỚI FILAMENT SHIELD

### Cấu hình Shield

Filament Shield đã được cấu hình sẵn trong `config/filament-shield.php`. 

### Tạo Permissions

```bash
php artisan shield:generate --all
```

Lệnh này sẽ tạo permissions cho:
- Tất cả Resources (User, Department, Project, Task)
- Tất cả Pages (Dashboard, Profile, Calendar, Reports)
- Tất cả Widgets

### Gán quyền cho Roles

1. Vào menu **Shield** → **Roles**
2. Chọn role cần chỉnh sửa
3. Chọn các permissions phù hợp với từng cấp độ

**Gợi ý phân quyền:**
- **Nhân viên:** Chỉ xem và cập nhật công việc được giao
- **Trưởng phòng:** Quản lý phòng ban, xem báo cáo phòng ban
- **Giám đốc DA:** Quản lý dự án, xem báo cáo dự án
- **Ban điều hành:** Xem tất cả báo cáo, quản lý toàn bộ
- **Chủ tịch:** Toàn quyền

## 📝 GHI CHÚ QUAN TRỌNG

### 1. File Upload
- Avatar: Lưu trong `storage/app/public/avatars`
- Task Files: Lưu trong `storage/app/public/task-files`
- Cần chạy: `php artisan storage:link` để tạo symbolic link

### 2. Activity Log
Hệ thống đã tích hợp Spatie Activity Log. Để sử dụng:
- Thêm trait `LogsActivity` vào các Models cần theo dõi
- Xem nhật ký trong database table `activity_log`

### 3. Media Management
Đã tích hợp Filament Curator để quản lý media. Có thể sử dụng trong các form upload file.

## 🛠️ CÁC LỆNH HỮU ÍCH

```bash
# Tạo permissions cho Shield
php artisan shield:generate --all

# Tạo user mới
php artisan make:filament-user

# Clear cache
php artisan optimize:clear

# Tạo symbolic link cho storage
php artisan storage:link

# Chạy migrations
php artisan migrate

# Chạy seeders
php artisan db:seed
```

## 📚 TÀI LIỆU THAM KHẢO

- [Filament Documentation](https://filamentphp.com/docs)
- [Filament Shield Documentation](https://github.com/bezhanSalleh/filament-shield)
- [Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Filament FullCalendar](https://github.com/saade/filament-fullcalendar)

## ✅ CHECKLIST HOÀN THÀNH

- [x] Quản lý Người dùng & Phân quyền (5 cấp độ)
- [x] Quản lý Phòng Ban & Dự án
- [x] Dashboard với widgets
- [x] Không gian Cá nhân (Profile)
- [x] Quản lý Công việc đầy đủ tính năng
- [x] Lịch làm việc (Calendar)
- [x] Báo cáo & KPI
- [x] Cấu hình Filament Shield

## 🎯 BƯỚC TIẾP THEO (Tùy chọn)

1. Tích hợp Activity Log để hiển thị nhật ký hoạt động
2. Thêm tính năng nhắc nhở tự động (Notifications)
3. Tích hợp Google Calendar
4. Thêm tính năng export báo cáo (Excel/PDF)
5. Thêm tính năng tìm kiếm nâng cao
6. Thêm tính năng filter theo nhiều tiêu chí

---

**Chúc bạn sử dụng hệ thống hiệu quả! 🚀**

