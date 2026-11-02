/**
 * Service sinh mẫu tin nhắn xác nhận ghi danh học viên và tiện ích copy clipboard
 */

export function formatVND(value) {
    if (value === null || value === undefined || value === '') return '0₫'
    const num = Number(value)
    if (isNaN(num)) return '0₫'
    return new Intl.NumberFormat('vi-VN').format(num) + '₫'
}

/**
 * Sinh văn bản mẫu tin nhắn Zalo/FB đầy đủ thông tin chuẩn chỉnh
 * @param {Object} student 
 * @param {Object} courseClass 
 * @returns {string}
 */
export function generateStudentConfirmationMessage(student = {}, courseClass = {}) {
    const studentName = student.name || 'Học viên'
    const phone = student.phone || '---'
    const courseTitle = student.course_title || courseClass.course_title || courseClass.name || 'Khóa học FUNKY'
    const classCode = student.class_code || courseClass.class_code || 'Chưa xếp lớp'
    const startDate = courseClass.start_date || student.start_date || 'Ban tổ chức sẽ thông báo sớm'
    const scheduleText = courseClass.schedule_text || student.schedule_text || 'Đang cập nhật'
    const zaloUrl = courseClass.zalo_group_url || student.zalo_group_url || ''

    const finalPrice = Number(student.final_price || 0)
    const paidAmount = Number(student.paid_amount || 0)
    const debtAmount = Number(student.debt_amount !== undefined ? student.debt_amount : Math.max(0, finalPrice - paidAmount))
    
    let paymentStatusText = student.payment_status || ''
    if (!paymentStatusText) {
        if (debtAmount === 0 && paidAmount > 0) {
            paymentStatusText = 'ĐÃ THANH TOÁN ĐỦ'
        } else if (paidAmount > 0) {
            paymentStatusText = `ĐÃ ĐÓNG CỌC (${formatVND(paidAmount)})`
        } else {
            paymentStatusText = 'CHƯA THANH TOÁN'
        }
    }

    let installmentDetails = ''
    if (Number(student.paid_installment_1) > 0 || Number(student.paid_installment_2) > 0) {
        const d1 = Number(student.paid_installment_1 || 0)
        const d2 = Number(student.paid_installment_2 || 0)
        installmentDetails = `  • Đợt 1: ${formatVND(d1)}\n  • Đợt 2: ${formatVND(d2)}\n`
    }

    let message = `🎉 THÔNG BÁO XÁC NHẬN GHI DANH KHÓA HỌC 🎉\n`
    message += `----------------------------------------\n`
    message += `Kính gửi: Bạn ${studentName}\n`
    message += `SĐT: ${phone}\n\n`
    message += `FUNKY xin trân trọng xác nhận thông tin ghi danh của bạn:\n`
    message += `📚 Khóa học: ${courseTitle}\n`
    message += `🏷️ Mã lớp: ${classCode}\n`
    message += `📅 Khai giảng: ${startDate}\n`
    message += `⏰ Lịch học: ${scheduleText}\n\n`
    message += `💰 THÔNG TIN HỌC PHÍ & THANH TOÁN:\n`
    if (student.original_price && Number(student.original_price) > finalPrice) {
        message += `- Học phí niêm yết: ${formatVND(student.original_price)}\n`
    }
    if (student.discount_amount && Number(student.discount_amount) > 0) {
        message += `- Ưu đãi đã áp dụng: -${formatVND(student.discount_amount)}\n`
    }
    message += `- Tổng học phí: ${formatVND(finalPrice)}\n`
    message += `- Đã thanh toán: ${formatVND(paidAmount)}\n`
    if (installmentDetails) {
        message += installmentDetails
    }
    if (debtAmount > 0) {
        message += `- Số tiền còn thiếu (công nợ): ⚠️ ${formatVND(debtAmount)}\n`
    } else {
        message += `- Tình trạng công nợ: ✅ Đã hoàn tất 100%\n`
    }
    message += `- Tình trạng: ${paymentStatusText}\n\n`

    message += `🔗 THAM GIA NHÓM ZALO LỚP HỌC:\n`
    if (zaloUrl) {
        message += `👉 Link nhóm Zalo: ${zaloUrl}\n`
        message += `(Bạn vui lòng bấm vào link trên để tham gia nhóm lớp nhận tài liệu, giáo trình và thông báo từ Mentor nhé!)\n\n`
    } else {
        message += `(Link nhóm Zalo của lớp sẽ được gửi tới bạn qua Zalo/SMS trước buổi học đầu tiên.)\n\n`
    }

    message += `----------------------------------------\n`
    message += `📞 Hỗ trợ học viên: Nếu cần hỗ trợ thêm thông tin, bạn có thể nhắn lại tin nhắn này hoặc liên hệ hotline FUNKY.\n`
    message += `Chúc bạn có những buổi học bổ ích và gặt hái nhiều kết quả tuyệt vời cùng FUNKY! ❤️`

    return message
}

/**
 * Tiện ích sao chép vào Clipboard có fallback document.execCommand
 * @param {string} text 
 * @returns {Promise<boolean>}
 */
export async function copyToClipboard(text) {
    if (!text) return false
    try {
        if (typeof navigator !== 'undefined' && navigator.clipboard && navigator.clipboard.writeText) {
            await navigator.clipboard.writeText(text)
            return true
        }
    } catch (e) {
        console.warn('navigator.clipboard.writeText failed, trying fallback execCommand:', e)
    }

    try {
        const textarea = document.createElement('textarea')
        textarea.value = text
        textarea.style.position = 'fixed'
        textarea.style.left = '-999999px'
        textarea.style.top = '-999999px'
        textarea.style.opacity = '0'
        document.body.appendChild(textarea)
        textarea.focus()
        textarea.select()
        const successful = document.execCommand('copy')
        document.body.removeChild(textarea)
        return successful
    } catch (err) {
        console.error('Fallback copyToClipboard failed:', err)
        return false
    }
}
