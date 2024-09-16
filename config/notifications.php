<?php

return [

    'channels' => [
        'sms' => App\Notifications\Channels\SmsChannel::class,
        'sms_pay' => App\Notifications\Channels\SmsChannelPay::class,
    ],

];
