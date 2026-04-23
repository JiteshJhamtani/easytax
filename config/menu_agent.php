<?php

return [

    [
        'text'  => 'Dashboard',
        'route' => 'agent.dashboard',
        'icon'  => 'fas fa-fw fa-tachometer-alt',
    ],

    [
        'text'  => 'New Application',
        'url' => 'services/itr-filing',
        'icon'  => 'fas fa-fw fa-plus-circle',
    ],
    [
        'text'  => 'More Application',
        'route' => 'services.index',
        'icon'  => 'fas fa-fw fa-plus-circle',
    ],

    [
        'text'  => 'My Applications',
        'route' => 'agent.applications.index',
        'icon'  => 'fas fa-fw fa-file-alt',
    ],

    // [
    //     'text'  => 'My Commissions',
    //     'route' => 'agent.commissions',
    //     'icon'  => 'fas fa-fw fa-coins',
    // ],

    // [
    //     'text'  => 'My Payouts',
    //     'route' => 'agent.payouts',
    //     'icon'  => 'fas fa-fw fa-wallet',
    // ],

];
