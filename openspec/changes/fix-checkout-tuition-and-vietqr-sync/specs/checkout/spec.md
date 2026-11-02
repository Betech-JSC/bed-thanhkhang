# Checkout Tuition & VietQR Sync Specification

## Requirements

### Requirement 1: Accurate Price Calculation on Checkout Page
- The checkout page MUST recalculate the final tuition price using the fetched schedule's base price and selected discount.
- The checkout page MUST NOT use the raw `price` query parameter from URL as the authoritative source of truth.
- The VietQR image URL MUST contain the exact recalculated `final_price`.
- The display amount for bank transfer MUST match the recalculated `final_price` and the order summary.

### Requirement 2: Show Page Selection Integrity
- Switching schedules on `Courses/Show.vue` MUST reset or align selected discounts to the active schedule.
- The price passed in the navigation query MUST match the calculated price of the selected schedule.

### Requirement 3: Server-side Verification
- Backend `ContactController` MUST verify and compute the valid tuition fee for the course schedule before saving the contact record.
