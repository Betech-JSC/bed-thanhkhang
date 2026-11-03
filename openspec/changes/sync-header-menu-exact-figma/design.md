# Design: Cấu Trúc Flex Phân Bổ Header Chuẩn Figma Node 2352:10717

## 1. Phân Tích Kiến Trúc Flex Box
Trong phiên bản trước, cấu trúc flex của Header:
```html
<div class="... flex items-center justify-between">
    <!-- Item 1 -->
    <div>Logo</div>
    <!-- Item 2 -->
    <nav>Nav 5 Tabs</nav>
    <!-- Item 3 -->
    <div>Right Actions</div>
</div>
```
Khi dùng 3 phần tử trong `justify-between`, trình duyệt phân bổ khoảng trống đều giữa Item 1 - Item 2 và Item 2 - Item 3:
- Khoảng cách giữa Logo và Nav: ~160px.
- Khoảng cách giữa Nav và Right Actions: ~160px.

Trong khi đó, thiết kế Figma chuẩn (Node `2352:10717`):
- Logo: `139px`
- Khoảng cách từ Logo đến Nav: `256px`
- Cụm Nav 5 Tabs: `484px`
- Khoảng cách từ Nav đến Right Actions: **đúng `64px`**
- Cụm Right Actions: `337px`
- Tổng chiều ngang nội dung: `139 + 256 + 484 + 64 + 337 = 1280px` (Khớp 100% với `1440px - 2 * 80px`).

## 2. Giải Pháp Tái Cấu Trúc Nhóm (Flex Grouping)
Gom cụm Nav và Right Actions vào một container duy nhất bên phải:

```html
<header class="fixed top-0 inset-x-0 z-[1000] bg-[#111322] antialiased transition-colors duration-200">
    <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-20 h-[64px] lg:h-[80px] xl:h-[111px] xl:py-8 flex items-center justify-between">
        <!-- 1. Bên Trái: Logo HH Maker -->
        <div class="flex items-center shrink-0">
            <Link :href="route('home')" class="inline-flex items-center">
                <img
                    src="/assets/images/header/figma-header-logo.svg"
                    alt="HH Maker Logo"
                    class="h-8 lg:h-[47px] w-auto lg:w-[139px] object-contain"
                />
            </Link>
        </div>

        <!-- 2. Bên Phải: Gom Nhóm Desktop Nav + Actions (gap 64px ở 1440px) -->
        <div class="hidden lg:flex items-center gap-8 xl:gap-[64px]">
            <!-- Nav 5 Tabs (w: 484px, gap 24px) -->
            <nav class="flex items-center gap-5 xl:gap-6 h-[47px]">
                ...
            </nav>

            <!-- Right Actions (w: 337px, gap 40px) -->
            <div class="flex items-center gap-8 xl:gap-10 shrink-0">
                ...
            </div>
        </div>

        <!-- 3. Mobile Controls (< 1024px) -->
        <div class="flex items-center gap-3 lg:hidden shrink-0">
            ...
        </div>
    </div>
</header>
```

## 3. Loại Bỏ Padding Ngang ở Nav Link
Trước:
```html
<Link class="h-[47px] px-1 text-base font-semibold leading-6 ... border-b-2">
```
Sau:
```html
<Link class="h-[47px] text-base font-semibold leading-6 ... border-b-2">
```
Việc bỏ `px-1` giúp viền `border-b-2` ôm trọn chiều rộng của text thay vì thừa 4px mỗi bên.
