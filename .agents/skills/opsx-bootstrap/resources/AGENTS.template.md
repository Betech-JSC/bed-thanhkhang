# AGENTS.md (Project Rules & Multi-Agent Reflex)

> File quy tắc dự án cho Antigravity. Áp dụng mức ưu tiên cao nhất (Priority 1) cho workspace này.

---

## 🛑 1. NGUYÊN TẮC AN TOÀN TUYỆT ĐỐI (STRICT SAFE GUARD)

Mọi agent (kể cả Main Agent lẫn Subagents) BẮT BUỘC tuân thủ nghiêm ngặt:
1. **CẤM TỰ Ý XÓA FILE**: Tuyệt đối không xóa bất kỳ file nào nếu chưa có trong Implementation Plan được duyệt.
2. **CẤM TỰ Ý THAY ĐỔI / CHỈNH SỬA DATABASE**: Không drop table, truncate, seed đè data thật hay chạy migration phá hủy dữ liệu.
3. **CẤM TỰ Ý THAY ĐỔI CẤU TRÚC KIẾN TRÚC**: Không tự ý refactor cấu trúc thư mục mà chưa thống nhất.

---

## 🔍 2. NGUYÊN TẮC BẰNG CHỨNG 100% (EVIDENCE-BASED & ZERO-HALLUCINATION)

1. **BÁO CÁO 100% PHẢI CÓ BẰNG CHỨNG THẬT**:
   - Mọi kết luận PASS/FAIL phải đính kèm raw terminal test output hoặc ảnh chụp màn hình từ Playwright MCP.
2. **CẤM SUY LUẬN MÙ / PHỎNG ĐOÁN**: Tuyệt đối không dùng từ ngữ cảm tính "chắc là được", "có vẻ đã đúng".
3. **NGUYÊN TẮC XÁC THỰC (ZERO-ASSUMPTION)**: Thiếu API contract hoặc chưa rõ luồng: BẮT BUỘC hỏi người dùng.

---

## 🤖 3. CƠ CHẾ TỰ ĐỘNG SPAWN SUBAGENTS (BẮT BUỘC)

### ⚠️ QUY TẮC PHẢN XẠ BẮT BUỘC (MANDATORY REFLEX RULE)
Khi nhận bất kỳ yêu cầu nào có từ 2 bước trở lên, hoặc yêu cầu test, code, kiểm tra tính năng:
1. **Main Agent TUYỆT ĐỐI CẤM tự chạy lệnh test (`run_command`), cấm tự sửa code trên luồng chính.**
2. **HÀNH ĐỘNG BẮT BUỘC ĐẦU TIÊN**: Main Agent **PHẢI GỌI NGAY TOOL `invoke_subagent`** để đẩy việc sang Subagent:
   - Sử dụng `TypeName: "self"`
   - Đặt `Role` tương ứng: `"Backend Worker"`, `"Frontend Worker"`, `"Independent Tester"`, hoặc `"QA Verifier"`.
   - Viết `Prompt` rõ ràng cho Subagent thực hiện.
3. **Hiển thị trực quan**: Người dùng cần theo dõi tiến trình trực quan trên thanh `Subagents` của giao diện Antigravity. Việc tự làm trên luồng chính mà không gọi `invoke_subagent` là **VI PHẠM QUY TRÌNH NGHIÊM TRỌNG**.

---

## 📋 4. QUY TRÌNH OPENSPEC (`OPSX`), ARCHIVE SAFE GUARD & MÔ HÌNH HUB-AND-SPOKE

1. **Spec-Driven Development**: Mọi tính năng mới, chỉnh sửa đa file hoặc nâng cấp giao diện đều vận hành qua chu trình OpenSpec (`/opsx-propose` tạo đủ `proposal.md`, `specs/`, `design.md`, `tasks.md` -> `/opsx-apply` thực thi -> `/opsx-archive` đồng bộ spec).
2. **OpenSpec Archive Safe Guard**: Chỉ chạy `openspec archive` sau khi 100% task trong `tasks.md` đã hoàn tất `[x]`, có đầy đủ bằng chứng build/test/Playwright QA thật và được người dùng nghiệm thu. Tuyệt đối không dùng `rm` xóa thủ công thư mục change.
3. **Hệ Sinh Thái 6 Bộ Skill Cốt Lõi Vận Hành Theo Đoạn Hội Thoại Trung Tâm (`4-Step Hub-and-Spoke`)**:
   - **`/opsx-bootstrap`** (`.agents/skills/opsx-bootstrap/SKILL.md`): Pure Lightweight Installer — chỉ cài đặt và đồng bộ file trong 1–2 giây, không chạy gộp các tác vụ quét nặng gây tràn bộ nhớ.
   - **`/opsx-cms-sync`** (`.agents/skills/opsx-cms-sync/SKILL.md`): Hub điều phối Hệ thống CMS Dynamic A–Z đối chiếu Figma (Khám phá động AST các hệ thống đã có bên CMS, quét toàn bộ UI chưa connect CMS, kiểm toán toàn bộ điều hướng qua `audit-cms-sync.py` -> Trình bày Bảng Quyết Định `[+] Thêm gì / [-] Bỏ gì / [=] Giữ lại gì` chờ người dùng Confirm -> Xuất bộ Handoff Prompts cho các hội thoại con -> Quay về Hub nghiệm thu E2E).
   - **`/opsx-pixel-perfect`** (`.agents/skills/opsx-pixel-perfect-ui/SKILL.md`): Hub điều phối Giao diện 100% Pixel-Perfect đối chiếu Figma MCP hỗ trợ chạy song song từng trang (`--page Layout`, `--page Home`, `--page About`...).
   - **`/opsx-animation` / `/ui-skill`** (`.agents/skills/opsx-animation/SKILL.md` & `.agents/skills/ui-skill/SKILL.md`): Hub điều phối Hệ thống Animation với **4 Quy Tắc Vàng** (1 phần tử tối đa 1 animation, cấm `opacity: 0` trắng màn hình, nút qua lại `< / >` bắt buộc trượt ngang `translate-x`, giữ nguyên 100% `ProductGallery.vue` có hàng preview dưới).
   - **`/opsx-i18n-cms`** (`.agents/skills/opsx-i18n-cms/SKILL.md`): Hub điều phối Đa Ngôn Ngữ Backend CMS (`VI / EN` & Auto-Fallback `EN -> VI`, khóa ranh giới file `[LOCKED: resources/Frontend/**]` để chạy song song với `/opsx-i18n-fe`).
   - **`/opsx-i18n-fe`** (`.agents/skills/opsx-i18n-fe/SKILL.md`): Hub điều phối Đa Ngôn Ngữ Giao Diện Frontend chỉ quét các trang có trong thiết kế Figma (`--pages` / `--page`, khóa ranh giới file `[LOCKED: app/**, config/**, resources/Backend/**]` để chạy song song với `/opsx-i18n-cms`).

---

## 🎨 5. QUY TRÌNH DỰNG & KIỂM TOÁN UI TỪ FIGMA MCP THEO PHASE

1. **Dựng UI theo Phase**: Bắt buộc tuân thủ `.agents/skills/opsx-figma-workflow/SKILL.md` (chọn đúng kịch bản `greenfield-6-phases`, `home-first-incremental`, hoặc `batch-pages-sequential-qa`). Khi đọc thiết kế từ Figma MCP, luôn quét nông `depth: 2` trước rồi tách truy vấn theo từng `nodeId` section để chống tràn token và dùng `download_figma_images` tải đúng SVG/ảnh gốc.
2. **Kiểm toán giao diện & dữ liệu Hardcode CMS (`opsx-cms-sync`, `opsx-ui-audit` & `opsx-audit-handoff`)**: Khi chạy audit (`.agents/skills/opsx-cms-sync/SKILL.md`, `.agents/skills/opsx-audit-handoff/SKILL.md` hoặc `.agents/skills/opsx-ui-audit/SKILL.md`), bắt buộc kiểm tra toàn bộ CMS (Backend Controllers/Services/Models/Middleware & Frontend Pages/Components/Layouts/Composables) xem có đang hardcode và return các giá trị cố định hay lệch khóa Cache không (qua `audit-cms-sync.py` & `scan-cms-hardcoded.py`) -> liệt kê đầy đủ tọa độ `File:Dòng` và tiến hành remove để kết nối đúng nguồn dữ liệu động của CMS.
3. **Tiêu chuẩn thẩm mỹ & nghiệm thu thật**: Tuân thủ `.agents/skills/anti-ui-slop/SKILL.md` (*Quiet & High-Trust UI* — cấm spam icon trang trí hay emoji vô nghĩa) và `.agents/skills/web-design-guidelines/SKILL.md`. Mọi màn hình phải build xanh (`yarn frontend:build`) và được `QA Verifier` kiểm chứng qua Playwright MCP trên cả Desktop lẫn Mobile.

---

## 🧭 6. ĐIỀU HƯỚNG KIẾN TRÚC GRAPHIFY, BỘ 24+1 SKILLS SEO & VPS CI/CD

1. **Graphify AST First**: Trước khi tạo mới component/class/service hoặc grep diện rộng, bắt buộc đọc `graphify-out/graph.json` (và `GRAPH_REPORT.md` nếu có) theo `.agents/skills/graphify/SKILL.md` để tái sử dụng hoặc mở rộng tài nguyên sẵn có. Sau mỗi Phase thay đổi cấu trúc UI/file/route, chạy lại `graphify . --code-only` để cập nhật bản đồ AST (luôn giữ `/graphify-out` trong `.gitignore`).
2. **Bộ 24+1 Skills SEO & 18 Specialist Sub-Agents**: Khi triển khai hoặc kiểm toán Technical SEO, Schema JSON-LD, Sitemap, Core Web Vitals, E-commerce SEO, Content Brief hay Local/GEO SEO, kích hoạt orchestrator `.agents/skills/seo/SKILL.md` cùng 24 sub-skills `.agents/skills/seo-*` và 18 specialist sub-agents trong `.agents/agents/seo-*.md`.
3. **VPS & GitHub Actions CI/CD Deployment**: Khi đưa dự án lên VPS Production, cấu hình Nginx Subdomain + SSL Certbot và thiết lập GitHub Actions 2-Job (`ci-test-and-build` + `cd-deploy`), tuân thủ `.agents/skills/vps-cicd-deploy/SKILL.md` (hoặc gọi `/opsx-deploy`).
