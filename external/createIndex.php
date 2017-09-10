<?php

if (!defined('XHGUI_ROOT_DIR')) {
    require dirname(dirname(__FILE__)) . '/src/bootstrap.php';
}

$dir = dirname(__DIR__);
require_once $dir . '/src/TwLogger/Config.php';
require_once $dir . '/src/TwLogger/Db/ProfileMapping.php';
TwLogger_Config::load($dir . '/config/config.default.php');
if (file_exists($dir . '/config/config.php')) {
    TwLogger_Config::load($dir . '/config/config.php');
}
unset($dir);

function createIndex() {
    $config = TwLogger_Config::all();
    $profileMapping = new TwLogger_Profile_Mapping();
    $params = [
        'index' => $config['es.index'],
        'body' => [
            'settings' => [
                'number_of_shards' => 3,
            ],
            'mappings' => [
                $config['es.type'] => $profileMapping->getMapping()
            ],
        ],
    ];
    try {
        $esClient = \Elasticsearch\ClientBuilder::create()->setHosts($config['hosts'])->build();
        $res = $esClient->indices()->create($params);
        echo json_encode($res);
    } catch (Exception $e) {
        error_log('xhgui create index - ' . $e->getMessage());
    }
}

createIndex();
