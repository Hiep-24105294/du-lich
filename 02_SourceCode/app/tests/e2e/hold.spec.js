// Hết hạn giữ chỗ (2 pha). Pha create: tạo phiếu giữ chỗ; sau đó chỉnh hold_expires_at qua MySQL và chạy
// `php artisan app:expire-holds`; pha verify: kiểm trạng thái expired và chỗ được trả.
import fs from 'node:fs';
import path from 'node:path';
import { test, expect } from '@playwright/test';
import { AUTH_DIR, authFile, actual, shot, getPage, send } from './helpers.js';

const PHASE = process.env.E2E_HOLD_PHASE;
const FILE = path.join(AUTH_DIR, 'hold.json');
test.skip(!PHASE, 'Chỉ chạy theo pha (E2E_HOLD_PHASE=create|verify)');
test.use({ storageState: authFile('member2') });

const slotOf = async (req, id) => (await getPage(req, '/vi/tuyen-tham-quan/dau-an-thang-long')).page.props.route.slots.find((s) => s.id === id);

test('TC-BOOK-010 phiếu giữ chỗ quá hạn tự chuyển expired và trả chỗ', async ({ page }, ti) => {
    if (PHASE === 'create') {
        await page.goto('/vi');
        const before = await slotOf(page.request, 8);
        const r = await send(page.request, 'POST', '/vi/dat-lich/8', { quantity: 4, contact_name: 'Hết hạn', contact_phone: '0912345678', contact_email: 'member1@disanso.test' });
        const code = r.location.split('/').pop();
        const held = await slotOf(page.request, 8);
        fs.writeFileSync(FILE, JSON.stringify({ code, before: before.available, held: held.available }));
        return;
    }
    const s = JSON.parse(fs.readFileSync(FILE, 'utf8'));
    const b = (await getPage(page.request, `/vi/dat-lich/${s.code}`)).page.props.booking;
    const after = await slotOf(page.request, 8);
    await page.goto(`/vi/dat-lich/${s.code}`);
    await shot(page, 'TC-BOOK-010');
    actual(ti, `${s.code}: chỗ trống ${s.before} → giữ 4 → ${s.held}; sau khi đặt hold_expires_at về quá khứ + app:expire-holds: status=${b.status}, can_pay=${b.can_pay}, chỗ trống ${after.available}.`);
    expect(b.status).toBe('expired');
    expect(after.available).toBe(s.before);
});
