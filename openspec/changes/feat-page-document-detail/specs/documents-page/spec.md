# Delta Spec: Documents Page (`documents-page`)

## ADDED Requirements

### Requirement: Navigation to Document Library from Documents Index
Trang Danh mục tài liệu (`/tai-lieu` và `/en/documents`) MUST tiếp tục giữ nguyên `product_categories[i].url` dạng `/san-pham?category=<slug>` (hoặc `/en/products?category=<slug>`) nhằm bảo toàn tương thích ngược và `DocumentsPageCmsTest.php`, đồng thời response từ `DocumentController@index()` MUST bổ sung liên kết điều hướng `documents_library_url` và `product_categories[i].library_url` trỏ tới `/tai-lieu/thu-vien` (hoặc `/en/documents/library`).

#### Scenario: Preserve existing category URL and expose library link
- **WHEN** DB có dữ liệu `ProductCategory` hợp lệ và người dùng truy cập `/tai-lieu`
- **THEN** `DocumentController@index()` vẫn trả `product_categories[i].url` dạng `/san-pham?category=<slug>`, đồng thời trả thêm `documents_library_url` (`/tai-lieu/thu-vien` ở `vi` hoặc `/en/documents/library` ở `en`) và `product_categories[i].library_url` để người dùng điều hướng từ `/tai-lieu` sang trang `/tai-lieu/thu-vien`.

#### Scenario: Keep index categories isolated from library documents
- **WHEN** bảng `posts` có 33 tài liệu thuộc `type = 'DOCUMENT'` dùng cho trang `/tai-lieu/thu-vien`
- **THEN** `DocumentController@index()` chỉ truy vấn `Post` thuộc `RESOURCE` / `CATALOGUE` cho khối `documents`, bảo đảm không ghi đè hoặc làm sai lệch danh sách 4 nhóm danh mục trên trang `/tai-lieu`.

