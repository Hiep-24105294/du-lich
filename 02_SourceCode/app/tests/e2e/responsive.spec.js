// Ma trận tương thích: 3 trình duyệt (project) × 3 khổ màn hình × 3 trang chính.
// Mỗi ô kiểm: HTTP 200, h1 hiển thị, không cuộn ngang, không lỗi JS/console, phần tử chính hoạt động.
import fs from 'node:fs';
import path from 'node:path';
import { test, expect } from '@playwright/test';
import { MATRIX_DIR, authFile, actual } from './helpers.js';

const VIEWPORTS = [
    { name: 'mobile', width: 375, height: 812 },
    { name: 'tablet', width: 768, height: 1024 },
    { name: 'desktop', width: 1280, height: 800 },
];
const PAGES = [
    { key: 'home', url: '/vi', check: async (page) => (await page.locator('main a[href*="/vi/di-san/"]').filter({ visible: true }).count()) > 0 },
// Vòng 2: DepthTabs đổi sang mẫu ARIA tablist/tab (sửa qa_7), không còn <nav>
    { key: 'site-detail', url: '/vi/di-san/hoang-thanh-thang-long', check: async (page) => page.locator('[role="tablist"] [role="tab"][href*="depth="]').first().isVisible() },
    {
        key: 'booking', url: '/vi/tuyen-tham-quan/dau-an-thang-long', auth: 'member',
        check: async (page) => {
            await page.locator('input[name="slot"]:not([disabled])').first().check();
            return page.locator('#booking form button[type="submit"]').isVisible();
        },
    },
];

for (const vp of VIEWPORTS) {
    for (const pg of PAGES) {
        test.describe(`${pg.key} @ ${vp.name}`, () => {
            test.use({ viewport: { width: vp.width, height: vp.height }, ...(pg.auth ? { storageState: authFile(pg.auth) } : {}) });
            test(`COMPAT ${pg.key} ${vp.width}x${vp.height}`, async ({ page, browserName }, ti) => {
                const errors = [];
                page.on('pageerror', (e) => errors.push(`pageerror: ${e.message}`));
                page.on('console', (m) => m.type() === 'error' && errors.push(`console: ${m.text()}`));
                const t0 = Date.now();
                const res = await page.goto(pg.url, { waitUntil: 'load' });
                const loadMs = Date.now() - t0;
                await page.waitForTimeout(800);
                const h1 = await page.locator('h1').first().isVisible();
                const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
                const ok = await pg.check(page).catch(() => false);
                const menuBtn = vp.width < 768 ? await page.locator('header button[aria-expanded]').first().isVisible().catch(() => false) : null;
                fs.mkdirSync(MATRIX_DIR, { recursive: true });
                await page.screenshot({ path: path.join(MATRIX_DIR, `${browserName}-${pg.key}-${vp.width}x${vp.height}.png`) });
                const row = { browser: browserName, page: pg.key, viewport: `${vp.width}x${vp.height}`, status: res.status(), h1, overflow, mainOk: ok, menuBtn, loadMs, errors };
                fs.writeFileSync(path.join(MATRIX_DIR, `${browserName}-${pg.key}-${vp.width}x${vp.height}.json`), JSON.stringify(row, null, 2));
                actual(ti, JSON.stringify(row));
                expect(res.status()).toBe(200);
                expect(h1).toBe(true);
                expect(overflow).toBeLessThanOrEqual(1);
                expect(ok).toBe(true);
                expect(errors).toEqual([]);
            });
        });
    }
}
