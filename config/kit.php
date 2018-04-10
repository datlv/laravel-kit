<?php

return [
    'middleware' => [
        'backend' => ['web', 'role:sys.admin'],
        'backend_tools' => ['web', 'role:sys.admin'],
    ],
    'pretty_routes' => [
        /**
         * The methods to hide.
         */
        'hide_methods' => [
            'HEAD',
        ],
    ],
    // My Data Migrate
    'migrations' => [],

    'importers' => [],

    // Data Manipulation
    'data_manipulations' => [],

    // Đăng ký các menu
    'menus' => [
        'backend.sidebar.tools.pretty_routes' => [
            'priority' => 2,
            'url' => 'route:backend.tools.pretty_routes',
            'label' => 'Routes',
            'icon' => 'fa-sitemap',
            'active' => 'backend/tools/pretty_routes',
        ],
        'backend.sidebar.tools.phpinfo' => [
            'priority' => 3,
            'url' => 'route:backend.tools.phpinfo',
            'label' => 'PHP Info',
            'icon' => 'info-sign',
            'active' => 'backend/tools/phpinfo',
        ],
        'backend.sidebar.tools.writeable' => [
            'priority' => 4,
            'url' => 'route:backend.tools.writeable',
            'label' => 'trans:backend.check_dir',
            'icon' => 'fa-check-square-o',
            'active' => 'backend/tools/writeable',
        ],
        'backend.sidebar.tools.system_info' => [
            'priority' => 5,
            'url' => 'route:backend.tools.system_info',
            'label' => 'System Informations',
            'icon' => 'fa-puzzle-piece',
            'active' => 'backend/tools/system_info',
        ],
    ],
    'writeable_paths' => [
        'storage:' => 'Thư mục không public',
        "storage:settings" => 'Lưu cấu hình',
        "data:" => 'Custom Storage',
        "data:files" => 'Thư mục lưu file upload không public',
        "data:tmp" => 'Thư mục lưu tạm không public',
        "upload:" => 'Upload chính',
        "upload:images" => 'Hình ảnh',
    ],
];
