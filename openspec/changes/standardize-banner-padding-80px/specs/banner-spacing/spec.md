# Spec Delta: banner-spacing

## Purpose

Chuẩn hóa hệ thống khoảng cách đệm (padding) và nhịp điệu không gian 80px cho các thành phần Banner Hero và CTA Banner nhằm đảm bảo tính đồng bộ thị giác trên toàn bộ hệ thống giao diện HH Maker.

## ADDED Requirements

### Requirement: Chuẩn hóa đệm thẻ Hero trang Quy trình
Hệ thống SHALL áp dụng khoảng cách đệm 80px (`lg:p-20`) cho thẻ Hero Card trên Desktop (viewport $\ge$ 1024px) tại thành phần `ProcessHero.vue`, đồng thời căn chỉnh vị trí nút cuộn xoay 360° cách mép phải và đáy 80px (`lg:right-20 lg:bottom-20`).

#### Scenario: Hiển thị ProcessHero trên Desktop
- **WHEN** người dùng truy cập trang Quy trình trên màn hình Desktop (viewport $\ge$ 1024px)
- **THEN** thẻ Hero Card có khoảng đệm trong đạt 80px và nút xoay 360° được định vị cách lề phải và lề đáy 80px.

### Requirement: Chuẩn hóa đệm ngang khung nổi bật trang Dịch vụ
Hệ thống SHALL áp dụng khoảng cách đệm ngang 80px (`lg:px-20`) cho khung minh họa bài viết nổi bật trên Desktop tại thành phần `ServiceHero.vue`, thay thế giá trị cố định 64px (`lg:px-[64px]`).

#### Scenario: Hiển thị ServiceHero trên Desktop
- **WHEN** người dùng truy cập trang Dịch vụ trên màn hình Desktop (viewport $\ge$ 1024px)
- **THEN** khung minh họa bài viết nổi bật có khoảng đệm ngang 80px, tạo không gian cân đối với ảnh render 3D và các cột thông tin.

### Requirement: Chuẩn hóa nhịp điệu đệm đáy khối CTA Banner
Hệ thống SHALL áp dụng khoảng cách đệm đáy 80px (`lg:pb-20`) cho khối Process CTA Banner trên Desktop tại thành phần `ProcessCtaBanner.vue`, đồng bộ với token nhịp điệu section 80px trước chân trang Footer.

#### Scenario: Hiển thị ProcessCtaBanner trên Desktop
- **WHEN** người dùng cuộn đến khối CTA Banner ở cuối trang Quy trình trên màn hình Desktop
- **THEN** khoảng cách đệm đáy của section đạt chính xác 80px, tạo khoảng cách nhịp điệu thị giác mượt mà nối tiếp đến Footer.
