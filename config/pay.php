<?php
/**
 * Created by PhpStorm.
 * User: 陈开坚(jianjian)
 * Date: 2019/8/7
 * Time: 23:10
 */

return [
    'alipay' => [
        'app_id'         => '2016092800612714',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwzu5vxcTO0nOjdFhc+CzIntGNxIqEwCRyQQjYSjrom9/umke7yhmbgah597zHL+sWEAjia0EfdROcqQWz7HaJPFMSo2FcF9BlUFx4BZ3Ux7rRrW0hptbfOt3ugjX9k8RhHoAzexsi5scbcmIFfSeOivwK3QrzcPG0junhK30wTPGUKtWm7e0FOvX/NC7KQvKNj0XxHdeoMyZJrqeNfaFPeRP9I/2AUTWLAZ/HlA2LRp+0zc5TfywFbomjOfDEFan4GA/gCpC/xqdGoHYJqcuIUEj4I0MrTR2X9BDjH0332UPxx1O5ABEuvySIlDpnrmU2a9D/19QS2N1GkELGUl08QIDAQAB',
        'private_key'    => 'MIIEogIBAAKCAQEAwCtxaxQ0FhHS1Fj0rhXqecLoq+hbqNlr38AJhZTPpdTLeSDnd2qICxizTifEm+jcrWmcpuv178SGziKmWYBaTmCv7GqG0W6Zw+vaGqW5Aes2UY1cqk7MJ+5/+yW1fdG4wWhx80W7ErFjBXlt23ZWAh4QCV8tFCx7aZdQyxf3+nNmsjjj1NoO26OGlEOEQwpBCqlb31QUNbM39NstweuWHo0IkFJc81SlRZn079wNJPtwMkC323ISFGtvHQy8qDz8sImNWQTCHt/Q5MtXBY2FHoqsUYX2r59knIqqJpFnfm+R/m9Dec1atGRRAHkvfflGqkz2+Fsx1YGyhYrsoxGgMwIDAQABAoIBAFA5EpUQDxpzgF8Tb7nI/mSsYHN7geBAO89hnNf+Ip1o8zRy7w4kE/0F57ylz2cC1DU0lKf+c2IP2fLt9iIA+jIkHO0GH5gIovpuWFOtgUlyxu9OozOZx5KX9yeCO3z18zoSfxd+sbUBBoR/h8QJZ6hkom83I1t7ol2XRKw0KlqL31BAqSXFdLbF9r6j7ZnDXYujXqd99XiEe3vHZpolP8zg7px8fpC9Xf744/DTMDjdrfkkmjLubPVhu+zpwmOwfKrrqnCRyGyUpFKQLrxVUg2aw3TO9HB+wpOSJF0VCuwBBFLLnGMHt5LUZOI9Bpo2clmPDQfEpR98bzMFXPJogoECgYEA3fdXhydGHILqLGca0qfEXoVMBZRn3C46P+31DkPOiB38Dq5ZiKXskmH5KDLDG2yzNgYK23+fIPrdqAfjRBVD+NfFOMuqB6N0Q237lIXx2BhRa/yqK/xNgkFiqOJ6dxWJMtwS1B3tcz+Xd3oGD4JTDVK4WLeV4rhOUkIR1oG4zMECgYEA3aKGLHT/23A2meINhygKgIPXFqgTcaxyimDeQm+ogOpHwKHbO3/QTfw4NRAyQ5WWSQ+7QH42+Emwf7XTZuAt1ROJGL+7FAadgGPvAnL+h5jEMZ3nxDsDoWCkY5joWahZybjquej1oouyflgRTFyA/KlWvmVvTMYIzefJhSUHhfMCgYBm1SJN0RogN15598JreaH2EFcp9pkvXpNJ7torI6rZUNrnikqcjhJNtuoRf4L4f6F/E8mP604zn0V1hUH61sdIz93k9CXvqABDBP8azfs/G/UMzF+iRR87i0ND7rjB1s5bK63la4AIdpublIKeSMhlUb9qCpN8F9rhLQ7KjAqrgQKBgBxQCrvbJmQt7Yuy6O9/GfhY3z0xs2ouEWPEBCCQiwIM66WcB5mONloAAl4k94bSsSQcMluGxBbrsvRdkefuc2xo1nam54sjXTNtkLbLYeCIj2eXOGhE8a3H3rgqbGQnsXCngrTFIbvmBKNHNM6AcqnUKBSieJliIvXKyl+L324pAoGAYE3B/wMPrBakmxZdhsgwHSRM+RiNxEVrKY/w/RxhFVfKR1V8vHI8iKT4FINybKMnTa+M8sTf9WMnkKixsCh5brNZjVdZQ0lGUU64U8wE3EuvBQkyv4y4QpoxBy2klxdTm/5vt+utIHAuz+0OxGfFu+PTY5Xpd0HlScq9wnLmA+0=',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];