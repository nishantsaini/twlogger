<?php

/**
 * Description of Elastic
 *
 * @author nishant
 */
class TwLogger_Saver_Elastic implements TwLogger_Saver_Interface
{

    private $client;

    public function __construct(\Elasticsearch\Client $elasticClient) {
        $this->client = $elasticClient;
    }

    public function save($data) {
        $dataMapper = new TwLogger_Elastic_Mapper($data);
        $dataArray = $dataMapper->getProfilingDataArray();
        if (empty($dataArray)) {
            return;
        }
        try {
            $config = TwLogger_Config::all();
            $bulkParams = [];
            foreach ($dataArray as $fieldArr) {
                $bulkParams['body'][] = [
                    'index' => [
                        '_index' => $config['es.index'],
                        '_type' => $config['es.type']
                    ]
                ];
                $bulkParams['body'][] = $fieldArr;
            }
            $response = $this->client->bulk($bulkParams);            
        } catch (Exception $ex) {
            error_log('xhgui indexing - ' . $ex->getMessage());
        }
    }

}
