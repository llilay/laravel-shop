<?php

return [
    'alipay' => [
        'app_id'         => '2016092200566735',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwdMO95UzzuV9nNvSpiBskDrm4u/nMsW0yuR/FdyrctcAcvY6UADfMJ03yDeofvFhCDEoocfapMUo0fnOzbFPXOI2FTLT+5lmmto2CpCgnIgJ58lsKnVUI6qFTscCwIIkCLP+/YlrSCXeXRIX5aDanqFdxkkQc77NY3diI7aDbSEf+a9mMza2c7ExeXESq0xHfNjCEC/OO3VpPAJQbXyNx4ilK91T0oL/5vcxa2XpvDdrM9xegmL6UzZkBjOzYONAgA4HlV5/YUeY3IEdbCOWgVMN4AgSJgQBgeF8gwFMci9OOarA9DWNeZw/LAkbE82buz7H2fo3PjJlkO85EkAACwIDAQAB',
        'private_key'    => 'MIIEpAIBAAKCAQEAySpdZbkgcEXne0uVrwrN3J+R3La4RGleEciiUbiz3Z72abxQGs14TR9o/Ax3dsuIzPSolgwVQ9jxsU4gnLF/bmEUSFJqiwNS0P4xiWaimaEu1kUCsOvGNuqTN6rKF2YglpBCFOj96fXi35D9xaQQgU/SqjxRRN7XGudWJclbadGVBolt8CMpdtpjsxtsnOF7I2Y0x1f2JAPFUNd0AZeS5N8xracE/JZrq/sZtu0DWxqNMH+cXMn36eFN1e4x6Dm8TjEqiayeY/JsqUQBxUptbATaGuszRXGHGolmBQQeStXszTMvHFphybJglGi/T4Vt6lRyNLJ7qQs8LDF5QeRs0wIDAQABAoIBAQCe+OLMM6uF7khLcGT+6ovbESNFCATA03/qj+JusIc72Zaj4pRvSfcLA4qEvRlfgONADQx5X10iu/vsgXBWRBMPWJqduLvH1b2JuYfOLJTM0crgu7Kvu/NtjJ0AvfBs42FXevMt8R5P2OgMg1l46fm9Jya1L2uOAIh/E1hKDkE2cmktU/MwLzcxo3DbFemfK2lJvzKiJ0xFtRAkry11Ji/KiDH0znYZbIYGCQLysWGD3ZkXAVMXbRstKc5F84j7n7SKrno4/7ZpA+rmXmb/sgVSFP8DN6U2udCnwDSsevpVs9KBU8yX4JsOEjS0i9rJ2OkAzQpH07PzBELi0gHXD3NpAoGBAPQ4uUM+AKoyH9uaawOFTYTaMZdoRHll/HI/AyFuGE1LEHB1YAJgALxNtYgSi58nTPEOCCbTK5f9/Pr8/3nKBcxHp4chlWTZYSka/9+B1ylmI+/N+4HwDcCuKyOAeGyEWfcCJ/EwoKsA9q/RDY5YCDOAPIIAk5h64iXVWhF2bmOlAoGBANLeDNLz9n+FcsrQTkeabq9fqza+h5cIpoweDoAUTBVCnTqfm4j2IMRyd+w6W4w+cdv0dy6NndP3Mk0k+cbi/3KZ83KLEn9OrVcZRSZ4LTHIcjPlHIhwUZpaqw+qks7pHl/VV+/eTesqhKRbiCfUJ0+IOHUf/PXNQMd+cd/GRkUXAoGAU5l/QZdd3uTdpuzLKR9ek9WlGDEnD29r5SfQyIbJZtwFOpnTTbzTQ6JOO9AtX0OywOmOvMuYpqTZDonAYk5XgcAdhtJmM2l+KvYFFNt7bb57GsGmEKq96nE0byixEGSV70obpiKBPUhNKY4kV8+mrwp8q/vKim22MEFCXavuyckCgYEAiP1Yt3NmoFHB0aCiOkJJxgUQ5e/Bho1IJZ8hLHQDOYydOIiYMtIzV2xGHGGNN/8ZWRvokYXPEvV06EktO9gcvAbn8XIIAkzKr9rq4aMROVZPWwdtEfZmDTD6EKNv3Jv176xaBKsXU7+7jEsqmTVqlHCavPybCdTeULwEnqvg2uECgYADPeFWzEi8gAtPF4DDFETldmG15S/be6xqzcEGx6F5XY3dWTwqqSyocACRTpQBaDefvg+7Jxpwu7h8kFapNydT05hMQxwzcurjHe0ss7sijrnHvtdROSZUf+jCadiSYfwQMXdchc1Mx1IdJMDj+YSEyiagss9Qs6GVpyNQyNhVuQ==',
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