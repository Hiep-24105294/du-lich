# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: admin.spec.js >> ADMIN – quản trị hệ thống >> TC-ADMIN-009 nhật ký audit ghi đăng nhập, đặt lịch, thanh toán, CRUD, kiểm duyệt
- Location: tests/e2e/admin.spec.js:245:5

# Error details

```
Error: expect(received).toBe(expected) // Object.is equality

Expected: true
Received: false
```

# Page snapshot

```yaml
- generic [ref=e3]:
  - link "Bỏ qua đến nội dung chính" [ref=e4] [cursor=pointer]:
    - /url: "#main"
  - complementary [ref=e5]:
    - link "DiSanSo – Trang chủ" [ref=e7] [cursor=pointer]:
      - /url: /vi
      - generic [ref=e8]: DiSanSo
    - navigation "Điều hướng quản trị" [ref=e14]:
      - paragraph [ref=e15]: Quản trị
      - list [ref=e16]:
        - listitem [ref=e17]:
          - link "Tổng quan" [ref=e18] [cursor=pointer]:
            - /url: /vi/quan-tri
        - listitem [ref=e21]:
          - link "Di sản" [ref=e22] [cursor=pointer]:
            - /url: /vi/quan-tri/di-san
        - listitem [ref=e25]:
          - link "Hiện vật" [ref=e26] [cursor=pointer]:
            - /url: /vi/quan-tri/hien-vat
        - listitem [ref=e29]:
          - link "Nhân vật" [ref=e30] [cursor=pointer]:
            - /url: /vi/quan-tri/nhan-vat
        - listitem [ref=e33]:
          - link "Thẻ" [ref=e34] [cursor=pointer]:
            - /url: /vi/quan-tri/the
        - listitem [ref=e37]:
          - link "Tuyến tham quan" [ref=e38] [cursor=pointer]:
            - /url: /vi/quan-tri/tuyen-tham-quan
        - listitem [ref=e41]:
          - link "Đặt lịch" [ref=e42] [cursor=pointer]:
            - /url: /vi/quan-tri/dat-lich
        - listitem [ref=e45]:
          - link "Kiểm duyệt đóng góp" [ref=e46] [cursor=pointer]:
            - /url: /vi/quan-tri/kiem-duyet
        - listitem [ref=e49]:
          - link "Thư viện media" [ref=e50] [cursor=pointer]:
            - /url: /vi/quan-tri/media
        - listitem [ref=e53]:
          - link "Chất lượng dữ liệu" [ref=e54] [cursor=pointer]:
            - /url: /vi/quan-tri/chat-luong-du-lieu
        - listitem [ref=e57]:
          - link "Người dùng" [ref=e58] [cursor=pointer]:
            - /url: /vi/quan-tri/nguoi-dung
        - listitem [ref=e61]:
          - link "Nhật ký" [ref=e62] [cursor=pointer]:
            - /url: /vi/quan-tri/nhat-ky
    - link "Về trang công khai" [ref=e66] [cursor=pointer]:
      - /url: /vi
  - generic [ref=e69]:
    - banner [ref=e70]:
      - generic [ref=e71]:
        - link "Chuyển ngôn ngữ sang English" [ref=e72] [cursor=pointer]:
          - /url: http://localhost:8080/en/quan-tri/nhat-ky
          - generic [ref=e75]: en
        - button "Chuyển sang giao diện tối" [ref=e76] [cursor=pointer]
        - generic [ref=e79]:
          - generic [ref=e80]: Quản trị viên
          - generic [ref=e81]: Quản trị hệ thống
        - button "Đăng xuất" [ref=e82] [cursor=pointer]
    - status
    - main [ref=e85]:
      - generic [ref=e87]:
        - paragraph [ref=e88]: Quản trị
        - heading "Nhật ký hệ thống" [level=1] [ref=e89]
        - paragraph [ref=e90]: Ghi nhận đăng nhập, thao tác quản trị, kiểm duyệt, thanh toán.
      - search [ref=e91]:
        - generic [ref=e92]:
          - generic [ref=e93]: Hành động
          - combobox "Hành động" [ref=e94]:
            - option "Tất cả" [selected]
            - option "admin.export"
            - option "admin.media.create"
            - option "admin.media.delete"
            - option "admin.quality.run"
            - option "admin.site.create"
            - option "admin.site.delete"
            - option "admin.site.update"
            - option "admin.user.update"
            - option "auth.login"
            - option "auth.login_failed"
            - option "booking.paid"
            - option "contribution.approved"
            - option "contribution.submitted"
        - generic [ref=e95]:
          - generic [ref=e96]: Người thực hiện
          - searchbox "Người thực hiện" [ref=e98]
        - generic [ref=e99]:
          - generic [ref=e100]: Từ ngày
          - textbox "Từ ngày" [ref=e101]
        - generic [ref=e102]:
          - generic [ref=e103]: Đến ngày
          - textbox "Đến ngày" [ref=e104]
        - button "Áp dụng" [ref=e106] [cursor=pointer]
      - generic [ref=e109]:
        - table [ref=e112]:
          - caption [ref=e113]: Nhật ký hệ thống
          - rowgroup [ref=e114]:
            - row [ref=e115]:
              - columnheader "Thời gian" [ref=e116]
              - columnheader "Người thực hiện" [ref=e117]
              - columnheader "Hành động" [ref=e118]
              - columnheader "Đối tượng" [ref=e119]
              - columnheader "IP" [ref=e120]
              - columnheader "Chi tiết" [ref=e121]
          - rowgroup [ref=e122]:
            - row [ref=e123]:
              - cell "18:34 18/10/2026" [ref=e124]
              - cell "Quản lý nghiệp vụ" [ref=e125]
              - cell [ref=e126]:
                - code [ref=e127]: admin.export
              - cell "—" [ref=e128]
              - cell "172.25.0.1" [ref=e129]
              - cell [ref=e130]:
                - code [ref=e131]: "{\"type\":\"views\"}"
            - row [ref=e132]:
              - cell "18:34 18/10/2026" [ref=e133]
              - cell "Quản lý nghiệp vụ" [ref=e134]
              - cell [ref=e135]:
                - code [ref=e136]: admin.export
              - cell "—" [ref=e137]
              - cell "172.25.0.1" [ref=e138]
              - cell [ref=e139]:
                - code [ref=e140]: "{\"type\":\"bookings\"}"
            - row [ref=e141]:
              - cell "18:34 18/10/2026" [ref=e142]
              - cell "Quản lý nghiệp vụ" [ref=e143]
              - cell [ref=e144]:
                - code [ref=e145]: admin.quality.run
              - cell "—" [ref=e146]
              - cell "172.25.0.1" [ref=e147]
              - cell [ref=e148]:
                - code [ref=e149]: "{\"checks\":11,\"errors\":0,\"warnings\":0}"
            - row [ref=e150]:
              - cell "18:34 18/10/2026" [ref=e151]
              - cell "Quản lý nghiệp vụ" [ref=e152]
              - cell [ref=e153]:
                - code [ref=e154]: admin.media.delete
              - 'cell "media #653" [ref=e155]'
              - cell "172.25.0.1" [ref=e156]
              - cell [ref=e157]:
                - code [ref=e158]: "{\"path\":\"media/image/chua-mot-cot-eqwr1w2n.png\"}"
            - row [ref=e159]:
              - cell "18:34 18/10/2026" [ref=e160]
              - cell "Quản lý nghiệp vụ" [ref=e161]
              - cell [ref=e162]:
                - code [ref=e163]: admin.media.create
              - 'cell "media #653" [ref=e164]'
              - cell "172.25.0.1" [ref=e165]
              - cell [ref=e166]:
                - code [ref=e167]: "{\"subject\":\"site#4\"}"
            - row [ref=e168]:
              - cell "18:34 18/10/2026" [ref=e169]
              - cell "Quản lý nghiệp vụ" [ref=e170]
              - cell [ref=e171]:
                - code [ref=e172]: admin.site.delete
              - 'cell "Di sản #57" [ref=e173]'
              - cell "172.25.0.1" [ref=e174]
              - cell [ref=e175]:
                - code [ref=e176]: "{\"slug\":\"qa-di-san-mnf1cs\"}"
            - row [ref=e177]:
              - cell "18:34 18/10/2026" [ref=e178]
              - cell "Quản lý nghiệp vụ" [ref=e179]
              - cell [ref=e180]:
                - code [ref=e181]: admin.site.create
              - 'cell "Di sản #57" [ref=e182]'
              - cell "172.25.0.1" [ref=e183]
              - cell [ref=e184]:
                - code [ref=e185]: "{\"slug\":\"qa-di-san-mnf1cs\"}"
            - row [ref=e186]:
              - cell "18:34 18/10/2026" [ref=e187]
              - cell "Quản lý nghiệp vụ" [ref=e188]
              - cell [ref=e189]:
                - code [ref=e190]: contribution.approved
              - 'cell "contribution #7" [ref=e191]'
              - cell "172.25.0.1" [ref=e192]
              - cell [ref=e193]:
                - code [ref=e194]: "{\"note\":\"Nội dung phù hợp – QA V5\"}"
            - row [ref=e195]:
              - cell "18:34 18/10/2026" [ref=e196]
              - cell "Khách tham quan" [ref=e197]
              - cell [ref=e198]:
                - code [ref=e199]: contribution.submitted
              - 'cell "contribution #7" [ref=e200]'
              - cell "172.25.0.1" [ref=e201]
              - cell [ref=e202]:
                - code [ref=e203]: "{\"type\":\"story\"}"
            - row [ref=e204]:
              - cell "18:34 18/10/2026" [ref=e205]
              - cell "Cụ. Trịnh Nam" [ref=e206]
              - cell [ref=e207]:
                - code [ref=e208]: auth.login
              - 'cell "Người dùng #5" [ref=e209]'
              - cell "172.25.0.1" [ref=e210]
              - cell [ref=e211]:
                - code [ref=e212]: —
            - row [ref=e213]:
              - cell "18:34 18/10/2026" [ref=e214]
              - cell "Khách tham quan" [ref=e215]
              - cell [ref=e216]:
                - code [ref=e217]: auth.login
              - 'cell "Người dùng #4" [ref=e218]'
              - cell "172.25.0.1" [ref=e219]
              - cell [ref=e220]:
                - code [ref=e221]: —
            - row [ref=e222]:
              - cell "18:34 18/10/2026" [ref=e223]
              - cell "Đối tác di tích" [ref=e224]
              - cell [ref=e225]:
                - code [ref=e226]: auth.login
              - 'cell "Người dùng #3" [ref=e227]'
              - cell "172.25.0.1" [ref=e228]
              - cell [ref=e229]:
                - code [ref=e230]: —
            - row [ref=e231]:
              - cell "18:34 18/10/2026" [ref=e232]
              - cell "Quản lý nghiệp vụ" [ref=e233]
              - cell [ref=e234]:
                - code [ref=e235]: auth.login
              - 'cell "Người dùng #2" [ref=e236]'
              - cell "172.25.0.1" [ref=e237]
              - cell [ref=e238]:
                - code [ref=e239]: —
            - row [ref=e240]:
              - cell "18:33 18/10/2026" [ref=e241]
              - cell "Quản trị viên" [ref=e242]
              - cell [ref=e243]:
                - code [ref=e244]: auth.login
              - 'cell "Người dùng #1" [ref=e245]'
              - cell "172.25.0.1" [ref=e246]
              - cell [ref=e247]:
                - code [ref=e248]: —
            - row [ref=e249]:
              - cell "18:32 18/10/2026" [ref=e250]
              - cell "Hệ thống" [ref=e251]
              - cell [ref=e252]:
                - code [ref=e253]: auth.login_failed
              - cell "—" [ref=e254]
              - cell "10.9.9.7" [ref=e255]
              - cell [ref=e256]:
                - code [ref=e257]: "{\"email\":\"ratetest@example.invalid\"}"
            - row [ref=e258]:
              - cell "18:32 18/10/2026" [ref=e259]
              - cell "Hệ thống" [ref=e260]
              - cell [ref=e261]:
                - code [ref=e262]: auth.login_failed
              - cell "—" [ref=e263]
              - cell "10.9.9.6" [ref=e264]
              - cell [ref=e265]:
                - code [ref=e266]: "{\"email\":\"ratetest@example.invalid\"}"
            - row [ref=e267]:
              - cell "18:32 18/10/2026" [ref=e268]
              - cell "Hệ thống" [ref=e269]
              - cell [ref=e270]:
                - code [ref=e271]: auth.login_failed
              - cell "—" [ref=e272]
              - cell "10.9.9.5" [ref=e273]
              - cell [ref=e274]:
                - code [ref=e275]: "{\"email\":\"ratetest@example.invalid\"}"
            - row [ref=e276]:
              - cell "18:32 18/10/2026" [ref=e277]
              - cell "Hệ thống" [ref=e278]
              - cell [ref=e279]:
                - code [ref=e280]: auth.login_failed
              - cell "—" [ref=e281]
              - cell "10.9.9.4" [ref=e282]
              - cell [ref=e283]:
                - code [ref=e284]: "{\"email\":\"ratetest@example.invalid\"}"
            - row [ref=e285]:
              - cell "18:32 18/10/2026" [ref=e286]
              - cell "Hệ thống" [ref=e287]
              - cell [ref=e288]:
                - code [ref=e289]: auth.login_failed
              - cell "—" [ref=e290]
              - cell "10.9.9.3" [ref=e291]
              - cell [ref=e292]:
                - code [ref=e293]: "{\"email\":\"ratetest@example.invalid\"}"
            - row [ref=e294]:
              - cell "18:32 18/10/2026" [ref=e295]
              - cell "Hệ thống" [ref=e296]
              - cell [ref=e297]:
                - code [ref=e298]: auth.login_failed
              - cell "—" [ref=e299]
              - cell "10.9.9.2" [ref=e300]
              - cell [ref=e301]:
                - code [ref=e302]: "{\"email\":\"ratetest@example.invalid\"}"
            - row [ref=e303]:
              - cell "18:32 18/10/2026" [ref=e304]
              - cell "Hệ thống" [ref=e305]
              - cell [ref=e306]:
                - code [ref=e307]: auth.login_failed
              - cell "—" [ref=e308]
              - cell "10.9.9.1" [ref=e309]
              - cell [ref=e310]:
                - code [ref=e311]: "{\"email\":\"ratetest@example.invalid\"}"
            - row [ref=e312]:
              - cell "18:32 18/10/2026" [ref=e313]
              - cell "Hệ thống" [ref=e314]
              - cell [ref=e315]:
                - code [ref=e316]: auth.login_failed
              - cell "—" [ref=e317]
              - cell "172.25.0.1" [ref=e318]
              - cell [ref=e319]:
                - code [ref=e320]: "{\"email\":\"ratetest@example.invalid\"}"
            - row [ref=e321]:
              - cell "18:32 18/10/2026" [ref=e322]
              - cell "Hệ thống" [ref=e323]
              - cell [ref=e324]:
                - code [ref=e325]: auth.login_failed
              - cell "—" [ref=e326]
              - cell "172.25.0.1" [ref=e327]
              - cell [ref=e328]:
                - code [ref=e329]: "{\"email\":\"ratetest@example.invalid\"}"
            - row [ref=e330]:
              - cell "18:32 18/10/2026" [ref=e331]
              - cell "Hệ thống" [ref=e332]
              - cell [ref=e333]:
                - code [ref=e334]: auth.login_failed
              - cell "—" [ref=e335]
              - cell "172.25.0.1" [ref=e336]
              - cell [ref=e337]:
                - code [ref=e338]: "{\"email\":\"ratetest@example.invalid\"}"
            - row [ref=e339]:
              - cell "18:32 18/10/2026" [ref=e340]
              - cell "Hệ thống" [ref=e341]
              - cell [ref=e342]:
                - code [ref=e343]: auth.login_failed
              - cell "—" [ref=e344]
              - cell "172.25.0.1" [ref=e345]
              - cell [ref=e346]:
                - code [ref=e347]: "{\"email\":\"ratetest@example.invalid\"}"
            - row [ref=e348]:
              - cell "18:32 18/10/2026" [ref=e349]
              - cell "Hệ thống" [ref=e350]
              - cell [ref=e351]:
                - code [ref=e352]: auth.login_failed
              - cell "—" [ref=e353]
              - cell "172.25.0.1" [ref=e354]
              - cell [ref=e355]:
                - code [ref=e356]: "{\"email\":\"ratetest@example.invalid\"}"
            - row [ref=e357]:
              - cell "12:30 18/10/2026" [ref=e358]
              - cell "Quản trị viên" [ref=e359]
              - cell [ref=e360]:
                - code [ref=e361]: admin.user.update
              - 'cell "Người dùng #2" [ref=e362]'
              - cell "127.0.0.1" [ref=e363]
              - cell [ref=e364]:
                - code [ref=e365]: "{\"is_active\":true}"
            - row [ref=e366]:
              - cell "11:30 18/10/2026" [ref=e367]
              - cell "Cô. Tào Khai" [ref=e368]
              - cell [ref=e369]:
                - code [ref=e370]: booking.paid
              - 'cell "booking #3" [ref=e371]'
              - cell "127.0.0.1" [ref=e372]
              - cell [ref=e373]:
                - code [ref=e374]: "{\"code\":\"BK-NQXFTIRO\",\"amount_vnd\":1950000}"
            - row [ref=e375]:
              - cell "10:30 18/10/2026" [ref=e376]
              - cell "Quản lý nghiệp vụ" [ref=e377]
              - cell [ref=e378]:
                - code [ref=e379]: admin.site.update
              - 'cell "Di sản #1" [ref=e380]'
              - cell "127.0.0.1" [ref=e381]
              - cell [ref=e382]:
                - code [ref=e383]: "{\"fields\":[\"visiting_hours\"]}"
            - row [ref=e384]:
              - cell "09:30 18/10/2026" [ref=e385]
              - cell "Quản lý nghiệp vụ" [ref=e386]
              - cell [ref=e387]:
                - code [ref=e388]: auth.login
              - cell "—" [ref=e389]
              - cell "127.0.0.1" [ref=e390]
              - cell [ref=e391]:
                - code [ref=e392]: "{\"email\":\"manager@disanso.test\"}"
        - navigation "Phân trang" [ref=e393]:
          - paragraph [ref=e394]: Hiển thị 1–30 trên 31
          - list [ref=e395]:
            - listitem [ref=e396]:
              - generic [ref=e397]: Trước
            - listitem [ref=e398]:
              - link "1" [ref=e399] [cursor=pointer]:
                - /url: http://localhost:8080/vi/quan-tri/nhat-ky?page=1
            - listitem [ref=e400]:
              - link "2" [ref=e401] [cursor=pointer]:
                - /url: http://localhost:8080/vi/quan-tri/nhat-ky?page=2
            - listitem [ref=e402]:
              - link "Sau" [ref=e403] [cursor=pointer]:
                - /url: http://localhost:8080/vi/quan-tri/nhat-ky?page=2
```

# Test source

```ts
  152 |                 source_name: 'Nhóm DiSanSo – ảnh kiểm thử', source_url: 'https://example.org/qa', author: 'V5 Kiểm thử', license: 'cc_by_sa',
  153 |                 rights_holder: 'Nhóm DiSanSo', captured_at: '2026-10-01', provenance_note: 'Ảnh tạo tự động phục vụ kiểm thử', is_cover: '0',
  154 |                 file: filePart('anh-kiem-thu.png', 'image/png', fs.readFileSync(fx('anh-kiem-thu.png'))),
  155 |             },
  156 |         });
  157 |         const { page: p } = await getPage(page.request, '/vi/di-san/chua-mot-cot');
  158 |         const m = p.props.site.media.images.find((x) => x.caption === caption);
  159 |         state.mediaId = m?.id;
  160 |         await page.goto(`/vi/quan-tri/media?q=${encodeURIComponent(caption)}`);
  161 |         await shot(page, 'TC-MEDIA-001');
  162 |         const file = m ? await page.request.get(m.url) : null;
  163 |         const d = m ? await send(page.request, 'DELETE', `/vi/quan-tri/media/${m.id}`, {}, { accept: 'html' }) : { status: '-' };
  164 |         actual(ti, `POST /quan-tri/media → ${r.status}; ảnh #${m?.id} trên trang công khai: license=${m?.license_label}, source=${m?.source_name}, author=${m?.author}, rights_holder=${m?.rights_holder}; tệp ${m?.url} → ${file?.status()}; DELETE → ${d.status}.`);
  165 |         expect(m).toBeTruthy();
  166 |         expect(m.source_name).toContain('DiSanSo');
  167 |         expect(m.license).toBe('cc_by_sa');
  168 |     });
  169 | 
  170 |     test('TC-MEDIA-002 tệp văn bản đổi đuôi .jpg bị từ chối (kiểm nội dung MIME)', async ({ page }, ti) => {
  171 |         await page.goto('/vi');
  172 |         const r = await send(page.request, 'POST', '/vi/quan-tri/media', null, {
  173 |             multipart: { subject_type: 'site', subject_id: '1', media_type: 'image', caption_vi: 'Giả ảnh', source_name: 'QA', license: 'simulated', file: filePart('gia-anh.jpg', 'image/jpeg', fs.readFileSync(fx('gia-anh.jpg'))) },
  174 |         });
  175 |         actual(ti, `HTTP ${r.status}; lỗi file: ${JSON.stringify(r.body?.errors?.file)}.`);
  176 |         await showText(page, 'TC-MEDIA-002', 'Upload gia-anh.jpg (nội dung văn bản)', `HTTP ${r.status}\n${JSON.stringify(r.body, null, 2)}`);
  177 |         expect(r.status).toBe(422);
  178 |         expect(r.body.errors.file).toBeTruthy();
  179 |     });
  180 | 
  181 |     test('TC-MEDIA-003 tệp ảnh 21 MB (vượt giới hạn 20 MB) bị từ chối, không lỗi máy chủ', async ({ page }, ti) => {
  182 |         await page.goto('/vi');
  183 |         const png = fs.readFileSync(fx('anh-kiem-thu.png'));
  184 |         const big = Buffer.concat([png, Buffer.alloc(21 * 1024 * 1024 - png.length, 0)]);
  185 |         const r = await send(page.request, 'POST', '/vi/quan-tri/media', null, {
  186 |             multipart: { subject_type: 'site', subject_id: '1', media_type: 'image', caption_vi: 'Ảnh quá lớn', source_name: 'QA', license: 'simulated', file: filePart('qua-lon.png', 'image/png', big) },
  187 |         });
  188 |         const text = typeof r.body === 'string' ? r.body.slice(0, 300) : JSON.stringify(r.body, null, 2);
  189 |         actual(ti, `Gửi 21 MB → HTTP ${r.status}; ${typeof r.body === 'object' ? `lỗi file: ${JSON.stringify(r.body?.errors?.file ?? r.body?.message)}` : 'phản hồi không phải JSON'}.`);
  190 |         await showText(page, 'TC-MEDIA-003', 'Upload ảnh 21 MB', `HTTP ${r.status}\n${text}`);
  191 |         expect(r.status).toBe(422);
  192 |         expect(r.body?.errors?.file).toBeTruthy();
  193 |     });
  194 | 
  195 |     test('TC-MEDIA-004 thiếu nguồn và giấy phép bị từ chối (bắt buộc provenance)', async ({ page }, ti) => {
  196 |         await page.goto('/vi');
  197 |         const r = await send(page.request, 'POST', '/vi/quan-tri/media', null, {
  198 |             multipart: { subject_type: 'site', subject_id: '1', media_type: 'image', caption_vi: 'Thiếu nguồn', file: filePart('anh.png', 'image/png', fs.readFileSync(fx('anh-kiem-thu.png'))) },
  199 |         });
  200 |         actual(ti, `HTTP ${r.status}; khóa lỗi: ${Object.keys(r.body?.errors ?? {}).join(', ')}.`);
  201 |         await showText(page, 'TC-MEDIA-004', 'Upload thiếu source_name, license', `HTTP ${r.status}\n${JSON.stringify(r.body, null, 2)}`);
  202 |         expect(r.status).toBe(422);
  203 |         expect(r.body.errors.source_name && r.body.errors.license).toBeTruthy();
  204 |     });
  205 | 
  206 |     test('TC-ADMIN-006 chạy kiểm định chất lượng dữ liệu hiển thị báo cáo lỗi/cảnh báo', async ({ page }, ti) => {
  207 |         await page.goto('/vi/quan-tri/chat-luong-du-lieu');
  208 |         const r = await send(page.request, 'POST', '/vi/quan-tri/chat-luong-du-lieu/kiem-tra', {}, { accept: 'html' });
  209 |         const { page: p } = await getPage(page.request, '/vi/quan-tri/chat-luong-du-lieu');
  210 |         await page.reload();
  211 |         await shot(page, 'TC-ADMIN-006');
  212 |         const rep = p.props.report;
  213 |         actual(ti, `POST kiểm tra → ${r.status}; báo cáo ${rep?.generated_at}: ${rep?.summary?.errors} lỗi, ${rep?.summary?.warnings} cảnh báo, ${rep?.checks?.length} phép kiểm (${rep?.checks?.map((c) => c.code).slice(0, 6).join(', ')}…); analyticsUp=${p.props.analyticsUp}.`);
  214 |         expect(rep?.checks?.length).toBeGreaterThan(0);
  215 |     });
  216 | 
  217 |     test('TC-ADMIN-007 xuất CSV đặt lịch tải về đúng định dạng', async ({ page }, ti) => {
  218 |         await page.goto('/vi');
  219 |         const r = await page.request.get('/vi/quan-tri/thong-ke/xuat?type=bookings');
  220 |         const csv = await r.text();
  221 |         const lines = csv.trim().split('\n');
  222 |         const v = await page.request.get('/vi/quan-tri/thong-ke/xuat?type=views');
  223 |         actual(ti, `HTTP ${r.status()}, Content-Type=${r.headers()['content-type']}, Content-Disposition=${r.headers()['content-disposition']}; ${lines.length - 1} dòng dữ liệu; tiêu đề: ${lines[0]}; type=views → ${v.status()}.`);
  224 |         await showText(page, 'TC-ADMIN-007', 'GET /vi/quan-tri/thong-ke/xuat?type=bookings', `${r.headers()['content-type']}\n${r.headers()['content-disposition']}\n\n${lines.slice(0, 8).join('\n')}`);
  225 |         expect(r.status()).toBe(200);
  226 |         expect(r.headers()['content-type']).toContain('text/csv');
  227 |     });
  228 | 
  229 |     test('TC-ADMIN-008 bảng điều khiển hiển thị KPI và biểu đồ', async ({ page }, ti) => {
  230 |         const { page: p } = await getPage(page.request, '/vi/quan-tri');
  231 |         await page.goto('/vi/quan-tri');
  232 |         await page.waitForTimeout(1500);
  233 |         const canvases = await page.locator('canvas').count();
  234 |         const k = p.props.kpis;
  235 |         actual(ti, `KPI: sites=${k.sites}, artifacts=${k.artifacts}, figures=${k.figures}, bookings30d=${k.bookings30d}, revenue30d=${k.revenue30d}, pendingContributions=${k.pendingContributions}; ${canvases} biểu đồ canvas.`);
  236 |         await shot(page, 'TC-ADMIN-008');
  237 |         expect(k.sites).toBeGreaterThan(0);
  238 |         expect(canvases).toBeGreaterThan(0);
  239 |     });
  240 | });
  241 | 
  242 | test.describe.serial('ADMIN – quản trị hệ thống', () => {
  243 |     test.use({ storageState: authFile('admin') });
  244 | 
  245 |     test('TC-ADMIN-009 nhật ký audit ghi đăng nhập, đặt lịch, thanh toán, CRUD, kiểm duyệt', async ({ page }, ti) => {
  246 |         const { page: p } = await getPage(page.request, '/vi/quan-tri/nhat-ky?per_page=100');
  247 |         const actions = [...new Set(p.props.logs.data.map((l) => l.action))];
  248 |         await page.goto('/vi/quan-tri/nhat-ky');
  249 |         await shot(page, 'TC-ADMIN-009');
  250 |         const need = ['auth.login', 'booking.held', 'booking.paid'];
  251 |         actual(ti, `${p.props.logs.total} bản ghi; hành động trang đầu: ${actions.join(', ')}.`);
> 252 |         expect(need.every((a) => p.props.actions?.some?.((x) => (x.code ?? x.value ?? x) === a) || actions.includes(a))).toBe(true);
      |                                                                                                                          ^ Error: expect(received).toBe(expected) // Object.is equality
  253 |     });
  254 | 
  255 |     test('TC-ADMIN-010 quản trị không thể tự hạ quyền hoặc khóa chính mình', async ({ page }, ti) => {
  256 |         await page.goto('/vi');
  257 |         const me = (await getPage(page.request, '/vi/tai-khoan')).page.props.auth.user;
  258 |         const r = await send(page.request, 'PATCH', `/vi/quan-tri/nguoi-dung/${me.id}`, { role_id: 4, is_active: false }, { headers: { Referer: 'http://localhost:8080/vi/quan-tri/nguoi-dung' } });
  259 |         const after = (await getPage(page.request, '/vi/tai-khoan')).page.props.auth.user;
  260 |         await page.goto('/vi/quan-tri/nguoi-dung');
  261 |         await shot(page, 'TC-ADMIN-010');
  262 |         actual(ti, `PATCH /nguoi-dung/${me.id} {role member, inactive} → ${r.status} ${JSON.stringify(r.body?.errors ?? r.body?.message ?? '')}; vai trò sau đó: ${after.role}.`);
  263 |         expect(after.role).toBe('admin');
  264 |     });
  265 | });
  266 | 
  267 | test.describe.serial('TOUR – contributor quản lý tuyến của mình', () => {
  268 |     test.use({ storageState: authFile('contributor') });
  269 | 
  270 |     test('TC-TOUR-003 contributor tạo tuyến mới + khung giờ rồi xóa', async ({ page }, ti) => {
  271 |         await page.goto('/vi');
  272 |         const slug = `qa-tuyen-${rnd()}`;
  273 |         const { page: form } = await getPage(page.request, '/vi/quan-tri/tuyen-tham-quan/moi');
  274 |         const c = await send(page.request, 'POST', '/vi/quan-tri/tuyen-tham-quan', {
  275 |             slug, duration_minutes: 180, difficulty: 'de', price_vnd: 300000, default_capacity: 15, status: 'draft',
  276 |             translations: { vi: { name: `Tuyến kiểm thử ${slug}`, description: 'Mô tả' }, en: { name: 'QA route' } },
  277 |             stops: [{ site_id: form.props.sites[0].id, stop_order: 1, dwell_minutes: 60 }],
  278 |         }, { accept: 'html' });
  279 |         const s = await send(page.request, 'POST', `/vi/quan-tri/tuyen-tham-quan/${slug}/khung-gio`, { starts_at: '2026-12-01 08:00', duration_minutes: 180, capacity: 15, guide_name: 'QA', language: 'vi' }, { accept: 'html' });
  280 |         const { page: ed } = await getPage(page.request, `/vi/quan-tri/tuyen-tham-quan/${slug}/sua`);
  281 |         await page.goto(`/vi/quan-tri/tuyen-tham-quan/${slug}/sua`);
  282 |         await shot(page, 'TC-TOUR-003');
  283 |         const d = await send(page.request, 'DELETE', `/vi/quan-tri/tuyen-tham-quan/${slug}`, {}, { accept: 'html' });
  284 |         const after = await page.request.get(`/vi/quan-tri/tuyen-tham-quan/${slug}/sua`);
  285 |         actual(ti, `Tạo tuyến → ${c.status}; thêm khung giờ → ${s.status}; form sửa có ${ed?.props?.route?.slots?.length} khung giờ, ${ed?.props?.route?.stops?.length} điểm dừng; DELETE → ${d.status}; mở lại form → ${after.status()}.`);
  286 |         expect(ed.props.route.slots.length).toBe(1);
  287 |         expect(after.status()).toBe(404);
  288 |     });
  289 | });
  290 | 
```