# DiSanSo – Nền tảng số hóa và diễn giải di sản văn hóa đa ngữ

Sản phẩm học thuật – phục vụ mục đích đào tạo. Học phần Lập trình ứng dụng web trong du lịch 2 (CSE703073), đề tài ĐT-04, nhóm DiSanSo, Đại học Phenikaa.

## Mô tả

DiSanSo số hóa hồ sơ di sản, hiện vật và nhân vật lịch sử; tổ chức lớp diễn giải theo hai trục **ngôn ngữ** (vi, en) và **mức chuyên sâu** (1 phổ thông, 2 chuyên sâu, 3 học thuật); cung cấp thư viện ảnh có siêu dữ liệu nguồn gốc – giấy phép, thuyết minh âm thanh, bản đồ, dòng thời gian, tìm kiếm ngữ nghĩa theo thẻ, tuyến tham quan có đặt lịch giữ chỗ và thanh toán thử nghiệm, đóng góp cộng đồng có kiểm duyệt, khu quản trị và API mở (OpenAPI 3.1, JSON-LD schema.org, GeoJSON, iCalendar).

Dữ liệu mẫu: 56 di sản, 62 hiện vật, 43 nhân vật, 453 bản diễn giải, 652 media, 365 sự kiện, 8 tuyến, 80 khung giờ (1.711 bản ghi du lịch).

## Kiến trúc

```
Trình duyệt (Vue 3.5 + Inertia 2, 2 theme, Leaflet, Chart.js)
        │  HTTP(S), cookie phiên + X-XSRF-TOKEN
        ▼
[app] Apache + PHP 8.3 + Laravel 12   ── HTTP nội bộ, X-Analytics-Token, timeout 2 s ──►  [analytics] FastAPI + scikit-learn
   Middleware → Controllers/FormRequest → Services → Eloquent Models                       TF-IDF, gợi ý thẻ, kiểm định dữ liệu
        │  PDO, truy vấn tham số hóa                                                              │ SQLAlchemy (chỉ đọc)
        ▼                                                                                         ▼
[db] MySQL 8.0 – 28 bảng nghiệp vụ + 8 bảng hạ tầng (utf8mb4, InnoDB, FULLTEXT, khóa dòng)
```

Đơn khối mô-đun ba tầng (trình bày – nghiệp vụ – truy cập dữ liệu) kèm một dịch vụ Python tách rời; khi dịch vụ Python lỗi, tìm kiếm tự chuyển sang FULLTEXT MySQL.

## Cài đặt nhanh (Docker)

Yêu cầu: Docker Desktop (Windows/macOS) hoặc Docker Engine + Compose v2; cổng 8080, 8001, 3307 trống.

```bash
cd 02_SourceCode
cp .env.example .env          # Windows: copy .env.example .env
docker compose up --build     # lần đầu 5–10 phút: build, migrate, seed
```

- Web: http://localhost:8080 (→ /vi) · Quản trị: http://localhost:8080/vi/quan-tri
- Kiểm tra sống: http://localhost:8080/healthz · OpenAPI: http://localhost:8080/api/v1/openapi.json
- Dịch vụ Python: http://localhost:8001/docs · MySQL: localhost:3307 (disanso / mật khẩu trong `.env`)

Tài khoản mẫu (mật khẩu = `SEED_DEMO_PASSWORD`, mặc định `DiSan@2026`): `admin@disanso.test`, `manager@disanso.test`, `partner@disanso.test` (đối tác), `user@disanso.test` (thành viên).

Dừng: `docker compose down` · Xóa sạch dữ liệu: `docker compose down -v`.

## Cấu trúc thư mục

```
02_SourceCode/
├── compose.yaml, .env.example
├── app/                         # Laravel 12 + Inertia + Vue
│   ├── app/Http/{Controllers,Middleware,Requests,Resources}
│   ├── app/Services/            # Booking, Payment(+SandboxGateway), Search, ContentLocalizer, JsonLd, GeoJson, Ical, Audit…
│   ├── app/Models/ (+Concerns)  # 28 model, trait HasInterpretations, HasMedia, HasTags, Translatable
│   ├── app/Policies, app/Console/Commands (app:backup-db, app:expire-holds, app:is-seeded)
│   ├── database/migrations/     # 11 migration
│   ├── database/seeders/, database/data/*.json   # dữ liệu nguồn song ngữ có trích dẫn
│   ├── resources/js/{Pages,Components,Layouts,Composables}  # 42 trang, 50 component
│   ├── resources/css/app.css    # token 2 theme
│   ├── resources/openapi/openapi.json
│   ├── lang/{vi,en}.json, lang/{vi,en}/*.php
│   ├── routes/{web,api,console}.php
│   ├── tests/Feature (PHPUnit), tests/e2e (Playwright)
│   └── docker/{Dockerfile,entrypoint.sh,php.ini}
└── python-service/              # FastAPI
    ├── app/{main,db,text,vectorize,search,tags,quality,stats}.py
    ├── kiem_dinh_du_lieu.py     # CLI kiểm định dữ liệu
    └── tests/                   # pytest
```

## Biến môi trường

| Biến | Ý nghĩa | Mặc định |
| --- | --- | --- |
| APP_ENV / APP_DEBUG / APP_URL | Môi trường, gỡ lỗi, URL gốc | production / false / http://localhost:8080 |
| APP_KEY | Khóa mã hóa (để trống: entrypoint tự sinh và lưu) | – |
| DB_DATABASE / DB_USERNAME / DB_PASSWORD / DB_ROOT_PASSWORD | MySQL | disanso / disanso / (đặt trong .env) |
| APP_FORWARD_PORT / ANALYTICS_FORWARD_PORT / DB_FORWARD_PORT | Cổng ánh xạ ra máy chủ | 8080 / 8001 / 3307 |
| ANALYTICS_TOKEN | Token dùng chung app ↔ Python | (đặt trong .env) |
| PAYMENT_SANDBOX_SECRET | Khóa HMAC của cổng sandbox | (đặt trong .env) |
| SEED_DEMO_PASSWORD | Mật khẩu tài khoản mẫu | DiSan@2026 |
| SESSION_SECURE_COOKIE | Cookie chỉ qua HTTPS | false (true khi go-live) |
| BOOKING_HOLD_MINUTES | Thời gian giữ chỗ | 10 |
| LOG_LEVEL | Mức nhật ký | info |

Không đưa `.env`, mật khẩu, khóa API vào kho; khi triển khai thật phải đổi mọi bí mật.

## Kiểm thử

```bash
# PHPUnit (image runtime không có phpunit → mount mã nguồn có vendor dev; DB test disanso_test)
docker run --rm -u $(id -u):$(id -g) --network 02_sourcecode_default \
  -v "$PWD/app":/var/www/html -w /var/www/html -e HOME=/tmp --entrypoint php 02_sourcecode-app artisan test
#   kết quả 18/10/2026: 126 passed, 1 skipped (pcntl)

# pytest (từ python-service)
docker run --rm -v "$PWD/python-service":/src -w /src --entrypoint sh 02_sourcecode-analytics \
  -c "pip install -q -r requirements-dev.txt && python -m pytest -q"     # 56 passed

# Playwright ba trình duyệt (hệ thống đang chạy ở 8080)
cd app && npx playwright install chromium firefox webkit && npx playwright test

# Kiểm định chất lượng dữ liệu
docker compose exec analytics python kiem_dinh_du_lieu.py
```

Cài vendor dev cho PHPUnit lần đầu: `docker run --rm -u $(id -u):$(id -g) -v "$PWD/app":/app -w /app composer:2 install`.

## Vận hành

- Sao lưu: tự động lúc khởi động và 02:00 hằng ngày, giữ 7 bản trong volume `app_storage` (`storage/app/backups`); chạy tay: `docker compose exec -u www-data app php artisan app:backup-db`.
- Nhật ký: `docker compose logs -f app` (Apache), `storage/logs/laravel.log` (ứng dụng), bảng `audit_logs` (nghiệp vụ, xem tại /vi/quan-tri/nhat-ky).
- Chi tiết: `04_HuongDanSuDung/HuongDan_CaiDat_QuanTri.md`.

## Thành viên

| Họ tên | MSSV | Vai trò |
| --- | --- | --- |
| <Họ tên 1> (Trưởng nhóm) | <MSSV1> | V1 Trưởng nhóm – quản trị cấu hình; V6 Nghiệp vụ và tài liệu |
| <Họ tên 2> | <MSSV2> | V2 Kiến trúc sư dữ liệu |
| <Họ tên 3> | <MSSV3> | V3 Phát triển máy chủ |
| <Họ tên 4> | <MSSV4> | V4 Phát triển giao diện; V5 Kiểm thử và triển khai |

Giảng viên hướng dẫn: TS. Nguyễn Văn Tánh. Giấy phép và thư viện bên thứ ba: xem `00_INFO.md` Mục 10.
