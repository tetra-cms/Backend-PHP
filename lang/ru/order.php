<?php

return [
    'statuses' => [
        'PENDING_PAYMENT' => 'Ожидает оплаты',
        'IN_PROGRESS'      => 'В обработке',
        'ASSEMBLED'        => 'Собран',
        'IN_DELIVERY'      => 'Передан в доставку',
        'RECEIVED'         => 'Получен',
        'CANCELLED'        => 'Отменён',
    ],
    'payments' => [
        'CASH' => 'Наличный расчёт',
        'NON_CASH'      => 'Безналичный расчёт',
    ],
    'delivery' => [
        'PICKUP' => 'Самовывоз',
        'DELIVERY'      => 'Доставка',
    ],
];
