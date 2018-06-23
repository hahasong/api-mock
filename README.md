# api mock
一个基于 CI3 框架的 mock 工具，无须数据库，没有UI界面。性能优秀，通过修改配置接口即时生效。

## 快速上手

- 项目以文件夹区分，放在 `/application/mock` 目录下
- 一个接口一个配置文件，如 `test` 项目接口 `test/show/1` 的配置文件是 `/application/mock/test/show.1.php`
- 支持直接使用文件内容作为mock的响应体，文件放在配置相同目录即可，另外还须在配置中填写格式和文件名
- 访问 `http://{domain}/mock/test/show/1` 使用接口

## 配置文件

- php 数组格式，主要有三个键值 `method`（可选）， `input`（可选）， `output`（必须）
- input 支持参数检查，使用 [CI 自带表单校验规则][ci_form]。如不需要校验可直接留空 `'input' => []`
- output 设置响应体，默认输出json格式，`content` 的值原样输出。如果指定了 `file` 优先返回文件的内容。
  支持返回图片，表格等格式。需要设置相应的 `content_type`，如图片设为 `jpg`。常用MIME列表可参考 `/application/config/mimes.php`

```php
<?php
return [
    'method' => 'GET',
    'input' => [
        [
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'valid_email'
        ],
        [
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required'
        ]
    ],
    'output' => [
        'content_type' => 'json',
        'content' => '{}',
        'file' => 'show.1.json'
    ]

];
```

[ci_form]: http://codeigniter.org.cn/user_guide/libraries/form_validation.html#id25