# Tasks — Optimize Core → Lightweight Framework

Roadmap tối ưu repo Point-of-sale: giữ custom MVC, **nâng `Core/` thành một lightweight framework**, rồi cải thiện chất lượng/test/features. Mỗi task làm trong **một session riêng**.

## Cách dùng
- Mở session mới, nói: *"làm task Txx theo `Docs/Tasks/Txx.md`"*.
- Mỗi task có: **Bối cảnh · Mục tiêu · Files · Phụ thuộc · Done (acceptance) · Verify**.
- Tôn trọng cột **Phụ thuộc** — không bắt đầu task khi task phụ thuộc chưa xong.

## ⚠️ Quy trình bắt buộc mỗi task
Vòng: **Code → Verify → Fix tới khi PASS → Commit.** Chi tiết rule + checklist 6 mục ở [`CLAUDE.md`](../../CLAUDE.md). Tóm tắt: sau khi code, spawn verify-agent (`/code-review` + `/security-review` hoặc Agent `code-reviewer`), chạy `php -l` + `composer lint`/`analyse`/`test`, sửa hết finding rồi mới commit.

## Quy ước migrate
Khi đổi API trong `Core/`, **giữ method cũ làm wrapper `@deprecated`** để app không gãy; code mới dùng API mới. Làm theo PR nhỏ để lịch sử commit rõ ràng.

## Danh sách task

| Task | Tên | Phase | Phụ thuộc | Status |
|------|-----|-------|-----------|--------|
| [T01](T01.md) | Tạo CLAUDE.md + Rule verify | Foundation | — | ✅ Done |
| [T02](T02.md) | Secrets & env | Foundation | — | ✅ Done |
| [T03](T03.md) | Tooling chất lượng + khung test | Foundation | — | ✅ Done |
| [T04](T04.md) | Gom tầng kết nối DB | 1 · Core | T03 | ✅ Done |
| [T05](T05.md) | Query Builder param-bound | 1 · Core | T04 | ✅ Done |
| [T06](T06.md) | DBModel trên Query Builder | 1 · Core | T05 | ✅ Done |
| [T07](T07.md) | Router: params + verbs + pipeline | 1 · Core | T03 | ✅ Done |
| [T08](T08.md) | Request / Response (web + API) | 1 · Core | T07 | ✅ Done |
| [T09](T09.md) | Validator hợp nhất | 1 · Core | T03 | ✅ Done |
| [T10](T10.md) | Middleware pipeline + CSRF + Session | 1 · Core | T07 | ✅ Done |
| [T11](T11.md) | View gom render + helper escape | 1 · Core | T08, T10 | ✅ Done |
| [T12](T12.md) | Error/Exception handler tập trung | 1 · Core | T04, T07 | ✅ Done |
| [T13](T13.md) | Sửa bug xác nhận được | 2 · Quality | T05, T09 | ✅ Done |
| [T14](T14.md) | Gỡ trùng lặp & magic numbers | 2 · Quality | T06 | ✅ Done |
| [T15](T15.md) | Validation rules + null-safety | 2 · Quality | T09 | ✅ Done |
| [T16](T16.md) | UUID + type hints + naming | 2 · Quality | T06 | ✅ Done |
| [T17](T17.md) | Unit tests | 3 · Test/CI | T05, T07, T09, T14 | ⬜ |
| [T18](T18.md) | Integration tests | 3 · Test/CI | T06, T13 | ⬜ |
| [T19](T19.md) | GitHub Actions CI | 3 · Test/CI | T03, T17 | ⬜ |
| [T20](T20.md) | Tồn kho (inventory) | 4 · Feature | T06, T15 | ⬜ |
| [T21](T21.md) | Báo cáo & analytics | 4 · Feature | T05 | ⬜ |
| [T22](T22.md) | Thanh toán thật | 4 · Feature | T07, T20 | ⬜ |
| [T23](T23.md) | REST API + filter tìm kiếm | 4 · Feature | T07, T08, T13 | ⬜ |

## Thứ tự đề xuất
T01 → T02 → T03 → (T04→T12 Core) → (T13→T16 quality) → (T17→T19 test/CI) → (T20→T23 features).
