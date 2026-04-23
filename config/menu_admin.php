<?php

return [

    [
        'text'  => 'Dashboard',
        'route' => 'admin.dashboard',
        'icon'  => 'fas fa-fw fa-tachometer-alt',
    ],

    [
        'text'  => 'Services',
        'route' => 'admin.services.index',
        'icon'  => 'fas fa-fw fa-concierge-bell',
    ],

    [
        'text'  => 'Applications',
        'route' => 'admin.applications.index',
        'icon'  => 'fas fa-fw fa-file-alt',
    ],

    // [
    //     'text'  => 'Payouts',
    //     'route' => 'admin.payouts.index',
    //     'icon'  => 'fas fa-fw fa-money-bill-wave',
    // ],

    [
        'text'  => 'Agents',
        'route' => 'admin.agents.index',
        'icon'  => 'fas fa-fw fa-users',
    ],
    [
        'text'    => 'Gifts',
        'icon'    => 'fas fa-gift',
        'submenu' => [
            [
                'text' => 'All Gifts',
                'url'  => 'admin/gifts',
                'icon' => 'fas fa-list',
            ],
            [
                'text' => 'Eligibility',
                'route'  => 'admin.gifts.eligibility.hub',
                'icon' => 'fas fa-users',
            ],
        ],
    ],

    [
        'text'  => 'Pages',
        'route' => 'admin.pages.index',
        'icon'  => 'fas fa-fw fa-file-contract',
    ],

];
