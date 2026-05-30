# CLAUDE.md — Point-of-Sale (Pure PHP MVC)

Hướng dẫn cho Claude Code khi làm việc trong repo này. **Mọi session đọc file này trước khi bắt đầu.**

## Tổng quan
- POS viết bằng **PHP 8.1 thuần**, custom MVC tự xây trong `Core/`. **Không** dùng Laravel/Symfony.
- DB **MySQL 8** qua **PDO** (collation `utf8mb4_vietnamese_ci`). Phụ thuộc Composer tối thiểu: `vlucas/phpdotenv`.
- Frontend: Bootstrap + vanilla JS, view PHP render qua layout (`{{content}}`).
- Đang trong dự án **nâng `Core/` thành một lightweight framework** rồi cải thiện dần các tầng trên (xem "Roadmap").

## Chạy local
```bash
composer install
cp .env.example .env   # cấu hình DB_DSN, DB_USER, DB_PASSWORD, APP_ENV, APP_DEBUG
php migrations.php     # chạy migrations
php -S localhost:8000 -t public
# hoặc: docker-compose up
```

## Kiến trúc (theo code thực tế)
- `Core/` — framework lõi: `Application` (bootstrap, event, `Application::$app` global), `Router`/`Route`, `Controller`, `Request`/`Response`, `Database` (PDO), `Model`/`DBModel`/`UserModel` (base), `Validator`, `Session`, `Middleware`, `View`, `FormRequest`, `Form/`.
- `Controllers/` → `Services/` (business logic) → `Models/` (entity + DB). `Routes/` định nghĩa route, `Middlewares/` (Auth/Admin), `Common/` (`Query`, `Pagination`), `Auth/AuthUser`, `Exception/`.
- `migrations/` (mXXXX_*.php) + `migrations.php` (runner). `views/` (layouts/page/admin). Entry: `public/index.php`.

## Quy ước code (BẮT BUỘC tuân theo)
1. **SQL luôn param-bound** — không nối chuỗi giá trị vào SQL. Dùng Query Builder param-bound trong `Common/Query.php`; mẫu chuẩn: `Services/ProductService::getProductById()`.
2. **Escape ở output, không sanitize ở input** — dùng helper `e()` trong view; không mã hoá dữ liệu lúc nhận request.
3. **CSRF** cho mọi route đổi trạng thái (POST/PUT/PATCH/DELETE web).
4. **PSR-12**; type-hint đầy đủ (tham số + return). Không để dead code/commented code.
5. **Migrate có alias tương thích ngược** — khi đổi API Core, giữ tên cũ làm wrapper `@deprecated` để app không gãy; code MỚI dùng API mới.
6. Không commit secrets (`.env` đã gitignore). Cấu hình môi trường qua `APP_ENV`/`APP_DEBUG`.

## ⚠️ RULE: Verify sau khi code (áp dụng cho MỌI task)
Vòng làm việc mỗi task: **Code → Verify → Fix tới khi PASS → Commit.**

Sau khi code xong một task, **trước khi báo hoàn thành / commit**, BẮT BUỘC:
1. Spawn một **verify-subagent** rà lại **logic + cú pháp + bảo mật** trên diff — dùng skill `/code-review` và `/security-review`, hoặc `Agent` (`code-reviewer` / `general-purpose`).
2. Chạy `php -l` cho mọi file đổi; và khi đã có tooling: `composer lint`, `composer analyse`, `composer test`.
3. Verify-agent trả **PASS/FAIL kèm finding (file:line)**. Sửa hết finding rồi verify lại **tới khi PASS** mới commit.

**Checklist verify (cố định):**
1. **Logic:** đúng mục tiêu + acceptance của task; edge case, null, transaction/rollback.
2. **Bảo mật:** không còn SQL nối chuỗi (param-bound); output escape `e()`; CSRF cho route đổi trạng thái; không lộ secrets.
3. **Cú pháp/Static:** `php -l` sạch; PHPStan không lỗi; PSR-12 sạch.
4. **Test:** test liên quan xanh; logic mới có test.
5. **Nhất quán:** code mới dùng API Core mới (không dùng alias `@deprecated`); naming nhất quán.
6. **Tương thích ngược:** alias đã hứa vẫn còn; app vẫn boot (`php -S` smoke test).

## Roadmap (mỗi task = một session; tôn trọng cột "Phụ thuộc")
Foundation: **T01** CLAUDE.md+rule (xong) · **T02** secrets/env · **T03** tooling+khung test.
Phase 1 (Core→framework): **T04** gom DB connection · **T05** Query Builder param-bound · **T06** DBModel trên builder · **T07** Router params+verbs+middleware pipeline · **T08** Request/Response (JSON, `json()`, fix typo) · **T09** Validator hợp nhất (bỏ 3 bản trùng) · **T10** Middleware pipeline+CSRF+Session hardening · **T11** View gom render + helper escape · **T12** Error/Exception handler tập trung.
Phase 2 (quality): **T13** sửa bug (`findProductByKeyWord` fatal, rule cú pháp `['min'>=20]`, `COUNT FROM users` sai bảng) · **T14** gỡ trùng lặp + magic numbers (size price, status const, pagination) · **T15** validation rules + null-safety · **T16** UUID + type hints.
Phase 3 (test/CI): **T17** unit tests · **T18** integration tests · **T19** GitHub Actions, xoá `config.yml`.
Phase 4 (features): **T20** tồn kho · **T21** analytics · **T22** thanh toán (gateway abstraction) · **T23** REST API + filter tìm kiếm.

> Plan chi tiết (mục tiêu/files/acceptance từng task): `~/.claude/plans/t-i-c-n-b-n-review-calm-elephant.md`.
