# Spec: Legacy FunkyStyle Routes and Views Cleanup

## Overview
Dọn dẹp các route và view di sản của template mẫu FunkyStyle, chuyển tiếp trực tiếp controller sang view chuẩn và loại bỏ các wrapper không cần thiết.

## Requirements

### Requirement: Direct Controller Rendering
Controllers MUST render standard pages directly without intermediary wrappers:
- `HomeController::index` MUST render `Home/Index`.
- `ProductController::show` MUST render `Product/Show`.
- `HistoryController::index` MUST render `About/Index`.
- Route `/ve-chung-toi` MUST render `About/Index`.

### Requirement: Removal of Intermediary Wrappers
The following wrapper files MUST be deleted:
- `resources/Frontend/js/Pages/Home.vue`
- `resources/Frontend/js/Pages/About.vue`
- `resources/Frontend/js/Pages/Products/Index.vue`
- `resources/Frontend/js/Pages/Products/Show.vue`

### Requirement: Pruning Legacy Routes
Legacy routes MUST be cleanly removed from `routes/frontend.php` and `routes/backend.php`:
- `prizes` (PrizeController)
- `projects` (ProjectController)
- `feedbacks` (FeedbackController)
- `gallery` (galleries.index)

### Requirement: Removal of Dead View Directories & Files
The following files and directories MUST be safely removed:
- `resources/Frontend/js/Pages/Prizes/`
- `resources/Frontend/js/Pages/Projects/`
- `resources/Frontend/js/Pages/Feedback/`
- `resources/Frontend/js/Pages/Jobs/`
- `resources/Frontend/js/Pages/Gallery.vue`
- `resources/Frontend/js/Pages/Route.vue`
- `resources/Frontend/js/Pages/Users/Profile/Components/FruitJourneyBanner.vue`

### Requirement: Non-Regression of Core Pages
The core application pages MUST continue to function and render properly:
- Home (`/`)
- Product Catalog (`/san-pham`)
- Product Detail (`/san-pham/:slug`)
- About Us (`/ve-chung-toi` / `/about-us`)
- Services (`/dich-vu`)
- Process (`/quy-trinh`)
- Contact (`/lien-he` / `/contact`)
- Policy (`/chinh-sach` / `/policies`)
