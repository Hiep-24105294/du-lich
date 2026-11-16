# deploy/vps – chạy DiSanSo trên VPS 180.93.2.63 cạnh các dự án khác

Luồng: push `main` → `.github/workflows/deploy-vps.yml` build hai image lên GHCR
(`ghcr.io/hiep-24105294/du-lich-web`, `…-analytics`) → SSH vào VPS chạy `deploy.sh`.

| Tệp                  | Vai trò                                                                 |
| -------------------- | ----------------------------------------------------------------------- |
| `compose.vps.yaml`   | Lớp phủ lên `compose.yaml`: không build, chỉ mở cổng web ở 127.0.0.1, giới hạn RAM |
| `apache-vps.conf`    | Prefork 2–8 tiến trình thay cho 20–150 của bản gốc (máy 2 GB RAM)       |
| `nginx-disanso.conf` | Site nginx của máy chủ, TLS Let's Encrypt qua webroot                   |
| `env.vps.example`   | Mẫu; `deploy.sh` sinh `.env` thật lần đầu với secret ngẫu nhiên          |
| `deploy.sh`          | Idempotent: clone, .env, nginx, chứng chỉ, kéo image, `compose up`      |

Trên VPS: mã ở `/srv/disanso`, secret ở `/srv/disanso/02_SourceCode/deploy/vps/.env`
(mật khẩu tài khoản mẫu là dòng `SEED_DEMO_PASSWORD`, chỉ dùng khi seed lần đầu).

Lệnh hay dùng (dưới root, trong `/srv/disanso/02_SourceCode`):

```bash
alias dc='docker compose -f compose.yaml -f deploy/vps/compose.vps.yaml --env-file deploy/vps/.env'
dc ps; dc logs -f app
dc exec -u www-data app php artisan app:backup-db     # sao lưu MySQL vào volume app_storage
```

Chỉ còn một việc làm tay một lần: thêm public key deploy vào `/root/.ssh/authorized_keys`
của VPS và đặt private key vào secret `DEPLOY_SSH_KEY` của kho.
