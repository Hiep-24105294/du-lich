# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: rbac.spec.js >> RBAC – khách vãng lai >> TC-RBAC-001 khách vào /vi/quan-tri bị chuyển tới trang đăng nhập
- Location: tests/e2e/rbac.spec.js:8:5

# Error details

```
Error: expect(received).toContain(expected) // indexOf

Expected value: 403
Received array: [302, 303]
```

# Page snapshot

```yaml
- main [ref=e2]:
  - paragraph [ref=e3]: "403"
  - paragraph [ref=e4]: Bạn không có quyền truy cập trang này.
  - paragraph [ref=e5]: You do not have permission to access this page.
  - link "Về trang chủ" [ref=e6] [cursor=pointer]:
    - /url: /vi
```

# Test source

```ts
  1   | // Phân quyền: truy cập trái phép trang/API quản trị, sửa dữ liệu của người khác, IPN giả mạo.
  2   | import { test, expect, request as pwRequest } from '@playwright/test';
  3   | import { authFile, actual, shot, send, getPage, showText } from './helpers.js';
  4   | 
  5   | const asRole = (role) => ({ storageState: authFile(role) });
  6   | 
  7   | test.describe('RBAC – khách vãng lai', () => {
  8   |     test('TC-RBAC-001 khách vào /vi/quan-tri bị chuyển tới trang đăng nhập', async ({ page }, ti) => {
  9   |         const res = await page.request.get('/vi/quan-tri', { maxRedirects: 0 });
  10  |         await page.goto('/vi/quan-tri');
  11  |         actual(ti, `GET /vi/quan-tri → ${res.status()} Location ${res.headers()['location'] ?? '-'}; trình duyệt dừng ở ${new URL(page.url()).pathname} (tiêu đề "${await page.title()}").`);
  12  |         await shot(page, 'TC-RBAC-001');
> 13  |         expect([302, 303]).toContain(res.status());
      |                            ^ Error: expect(received).toContain(expected) // indexOf
  14  |         expect(page.url()).toContain('/vi/dang-nhap');
  15  |     });
  16  | 
  17  |     test('TC-RBAC-002 khách POST đặt lịch (không đăng nhập) bị chặn', async ({ page }, ti) => {
  18  |         await page.goto('/vi');
  19  |         const r = await send(page.request, 'POST', '/vi/dat-lich/3', { quantity: 1, contact_name: 'Khách', contact_phone: '0912345678', contact_email: 'a@b.vn' });
  20  |         expect(r.status).toBe(401);
  21  |         actual(ti, `POST /vi/dat-lich/3 (Accept JSON) → ${r.status} ${JSON.stringify(r.body).slice(0, 80)}`);
  22  |         await showText(page, 'TC-RBAC-002', 'Khách POST /vi/dat-lich/3', `HTTP ${r.status}\n${JSON.stringify(r.body, null, 2)}`);
  23  |     });
  24  | });
  25  | 
  26  | test.describe('RBAC – thành viên', () => {
  27  |     test.use(asRole('member'));
  28  | 
  29  |     test('TC-RBAC-003 member vào /vi/quan-tri nhận 403 thân thiện', async ({ page }, ti) => {
  30  |         const res = await page.goto('/vi/quan-tri');
  31  |         expect(res.status()).toBe(403);
  32  |         await expect(page.getByText('Không có quyền truy cập')).toBeVisible();
  33  |         const leak = /(Exception|vendor\/|stack|\.php)/i.test(await page.content());
  34  |         actual(ti, `HTTP ${res.status()}, tiêu đề "Không có quyền truy cập", lộ thông tin kỹ thuật: ${leak ? 'có' : 'không'}.`);
  35  |         expect(leak).toBe(false);
  36  |         await shot(page, 'TC-RBAC-003');
  37  |     });
  38  | 
  39  |     test('TC-RBAC-004 member gọi PATCH kiểm duyệt đóng góp (API quản trị) nhận 403', async ({ page }, ti) => {
  40  |         await page.goto('/vi');
  41  |         const r = await send(page.request, 'PATCH', '/vi/quan-tri/kiem-duyet/1', { status: 'approved', review_note: 'tự duyệt' });
  42  |         const { page: c } = await getPage(page.request, '/vi/tai-khoan');
  43  |         expect(r.status).toBe(403);
  44  |         actual(ti, `PATCH /vi/quan-tri/kiem-duyet/1 → ${r.status}; đóng góp #1 không đổi trạng thái.`);
  45  |         await showText(page, 'TC-RBAC-004', 'member PATCH /vi/quan-tri/kiem-duyet/1', `HTTP ${r.status}\n${JSON.stringify(r.body, null, 2)}\n(user: ${c.props.auth.user.email})`);
  46  |     });
  47  | 
  48  |     test('TC-RBAC-005 member xem đặt lịch của người khác (đoán mã) bị chặn', async ({ page, browser }, ti) => {
  49  |         const other = await browser.newContext(asRole('member2'));
  50  |         await other.request.get('/vi');
  51  |         const created = await send(other.request, 'POST', '/vi/dat-lich/10', { quantity: 1, contact_name: 'Thành viên 1', contact_phone: '0912345678', contact_email: 'member1@disanso.test' });
  52  |         const code = created.location.split('/').pop();
  53  |         await other.close();
  54  |         const res = await page.goto(`/vi/dat-lich/${code}`);
  55  |         expect([403, 404]).toContain(res.status());
  56  |         actual(ti, `member1 tạo ${code}; user@ GET /vi/dat-lich/${code} → ${res.status()}.`);
  57  |         await shot(page, 'TC-RBAC-005');
  58  |     });
  59  | });
  60  | 
  61  | test.describe('RBAC – contributor', () => {
  62  |     test.use(asRole('contributor'));
  63  | 
  64  |     test('TC-RBAC-006 contributor vào /vi/quan-tri/di-san nhận 403', async ({ page }, ti) => {
  65  |         const res = await page.goto('/vi/quan-tri/di-san');
  66  |         expect(res.status()).toBe(403);
  67  |         actual(ti, `GET /vi/quan-tri/di-san → ${res.status()}, hiển thị trang 403.`);
  68  |         await shot(page, 'TC-RBAC-006');
  69  |     });
  70  | 
  71  |     test('TC-RBAC-007 contributor sửa tuyến của người khác bị 403 (GET form và PUT)', async ({ page }, ti) => {
  72  |         const res = await page.goto('/vi/quan-tri/tuyen-tham-quan/dau-an-thang-long/sua');
  73  |         const put = await send(page.request, 'PUT', '/vi/quan-tri/tuyen-tham-quan/dau-an-thang-long', { slug: 'dau-an-thang-long', duration_minutes: 60, difficulty: 'de', price_vnd: 1, default_capacity: 5, status: 'archived', translations: { vi: { name: 'Bị sửa' } }, stops: [{ site_id: 1 }] });
  74  |         const del = await send(page.request, 'DELETE', '/vi/quan-tri/tuyen-tham-quan/dau-an-thang-long');
  75  |         expect(res.status()).toBe(403);
  76  |         expect(put.status).toBe(403);
  77  |         expect(del.status).toBe(403);
  78  |         actual(ti, `GET form → ${res.status()}, PUT → ${put.status}, DELETE → ${del.status}.`);
  79  |         await shot(page, 'TC-RBAC-007');
  80  |     });
  81  | 
  82  |     test('TC-RBAC-008 contributor chỉ thấy tuyến và đặt lịch thuộc tuyến của mình', async ({ page }, ti) => {
  83  |         const { page: tours } = await getPage(page.request, '/vi/quan-tri/tuyen-tham-quan');
  84  |         const { page: bk } = await getPage(page.request, '/vi/quan-tri/dat-lich');
  85  |         const rows = tours.props.routes.data ?? tours.props.routes;
  86  |         const owners = [...new Set(rows.map((r) => r.owner?.name))];
  87  |         const bookingRoutes = [...new Set((bk.props.bookings.data ?? []).map((b) => b.slot?.route?.slug))];
  88  |         const own = ['phat-giao-kinh-bac', 'huong-ve-dat-to', 'con-duong-di-san-cham', 'sai-gon-ky-uc'];
  89  |         expect(rows.every((r) => own.includes(r.slug))).toBe(true);
  90  |         expect(bookingRoutes.every((s) => own.includes(s))).toBe(true);
  91  |         expect(bk.props.canChangeStatus).toBeFalsy();
  92  |         actual(ti, `Thấy ${rows.length} tuyến (${rows.map((r) => r.slug).join(', ')}), chủ: ${owners.join(', ')}; đặt lịch thuộc tuyến: ${bookingRoutes.join(', ') || '(không có)'}; canChangeStatus=${bk.props.canChangeStatus}.`);
  93  |         await page.goto('/vi/quan-tri/tuyen-tham-quan');
  94  |         await shot(page, 'TC-RBAC-008');
  95  |     });
  96  | 
  97  |     test('TC-RBAC-009 contributor xóa di sản qua API quản trị nhận 403', async ({ page }, ti) => {
  98  |         await page.goto('/vi');
  99  |         const r = await send(page.request, 'DELETE', '/vi/quan-tri/di-san/hoang-thanh-thang-long');
  100 |         const still = await page.request.get('/api/v1/sites/hoang-thanh-thang-long');
  101 |         expect(r.status).toBe(403);
  102 |         expect(still.status()).toBe(200);
  103 |         actual(ti, `DELETE /vi/quan-tri/di-san/hoang-thanh-thang-long → ${r.status}; di sản vẫn tồn tại (API ${still.status()}).`);
  104 |         await showText(page, 'TC-RBAC-009', 'contributor DELETE di sản', `HTTP ${r.status}\n${JSON.stringify(r.body)}\nGET /api/v1/sites/hoang-thanh-thang-long → ${still.status()}`);
  105 |     });
  106 | });
  107 | 
  108 | test.describe('RBAC – manager', () => {
  109 |     test.use(asRole('manager'));
  110 | 
  111 |     test('TC-RBAC-010 manager vào trang quản lý người dùng và nhật ký (chỉ admin) nhận 403', async ({ page }, ti) => {
  112 |         const u = await page.goto('/vi/quan-tri/nguoi-dung');
  113 |         const st1 = u.status();
```