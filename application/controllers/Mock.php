<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mock extends CI_Controller {

    // mock接口配置目录
    const MOCKPATH = APPPATH . 'mock' . DIRECTORY_SEPARATOR;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Index Page for Mock.
     */
    public function index() {
        // URI解析
        $path_info = array_slice(explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'), 3), 1);
        if (count($path_info) != 2) {
            $this->show_404('json');
        }

        $repository = $path_info[0];
        $repository_path = self::MOCKPATH . $repository . DIRECTORY_SEPARATOR;
        $file_name = str_replace('/', '.', $path_info[1]);

        if (!is_dir($repository_path)) {
            $this->show_404('json');
        }

        if (!file_exists( $repository_path . $file_name . '.php')) {
            $this->show_404('json');
        }

        $config = include $repository_path . $file_name . '.php';
        // 合并默认配置
        $config += [
            'method' => 'GET',
            'input' => []
        ];

        // 配置检查
        if (!isset($config['output']['content_type']) || (!isset($config['output']['content']) && !isset($config['output']['file']))) {
            $this->output_json([
                'code' => -1,
                'msg' => 'config error',
                'data' => []
            ]);
        }

        // 参数检查
        if (!empty($config['input']) && is_array($config['input'])) {
            $this->load->library('form_validation');
            if (strtoupper($config['method']) == 'GET') {
                if ($_SERVER['REQUEST_METHOD'] == 'GET' && empty($_GET)) {
                    $this->output_json([
                        'code' => -2,
                        'msg' => 'input required',
                        'data' => []
                    ]);
                }
                $this->form_validation->set_data($_GET);
            } elseif (strtoupper($config['method']) == 'POST') {
                // 支持POST raw json
                if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == 'application/json') {
                    $post = file_get_contents('php://input');
                    $post = json_decode($post, true);
                    if (!$post || !is_array($post)) {
                        $this->output_json([
                            'code' => -3,
                            'msg' => 'json decode error',
                            'data' => []
                        ]);
                    }
                    $this->form_validation->set_data($post);
                }
            }

            $this->form_validation->set_rules($config['input']);
            if ($this->form_validation->run() == FALSE) {
                $this->output_json([
                    'code' => -2,
                    'msg' => $this->form_validation->error_string(),
                    'data' => []
                ]);
            }

        }

        // 优先输出文件内容
        if (isset($config['output']['file']) && $config['output']['file']) {
            $output_file = $repository_path . $config['output']['file'];
            if (file_exists($output_file)) {
                $this->output_file($output_file, $config['output']['content_type']);
            }
        }

        if (isset($config['output']['content'])) {
            $this->output_json_str($config['output']['content']);
        }

    }

}
