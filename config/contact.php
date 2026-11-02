<?php

return [
    'types' => [
        'CONTACT_FORM' => [
            'title' => 'Liên hệ',
            'columns' => [
                'email',
            ],
            'all_columns' => [
                'Họ và tên',
                'Số điện thoại',
                'Email',
                'Nội dung cần hỗ trợ',
                'Service',
            ],
            'rules' => [
                'Số điện thoại' => 'nullable|regex:/(0)[0-9]/|not_regex:/[a-z]/|min:9|max:12',
                'Email' => 'required',
            ],
            'route' => 'contacts',
        ],
    ],
    'message' => [
        'new_contact' => 'Bạn nhận được liên hệ mới',
        'success_form' => 'Chúc mừng bạn gửi form thành công',
    ],
    'email_urls' => [
        'url' => 'Xem chi tiết liên hệ',
        'job_url' => 'Xem chi tuyển dụng',
        'service_url' => 'Xem chi dịch vụ',
    ],
    'html_string' => [
        'File CV 1',
        'CV 1'
    ],
    'send_email_default' => false
];
