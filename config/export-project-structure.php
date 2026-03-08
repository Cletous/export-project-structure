<?php

return [
    /** Define the root folder to store the exports */
    'exports_directory' => 'exports',

    /** Define the targets to export */
    'targets' => [
        'models' => 'app/Models',
        'controllers' => 'app/Http/Controllers',
        'services' => 'app/Services',
        'actions' => 'app/Actions',
        'events' => 'app/Events',
        'requests' => 'app/Http/Requests',
        'jobs' => 'app/Jobs',
        'policies' => 'app/Policies',
        'seeders' => 'database/seeders',
        'pages' => 'resources/js/Pages',
        'hooks' => 'resources/js/hooks',
        'components' => 'resources/js/components',
        'views' => 'resources/views',
        'livewire' => 'app/Livewire',
        'mail' => 'app/Mail',
        'routes' => 'routes',
        'config' => 'config',
    ],
];