<?php
/**
 * A small factory to handle creation of the profile saver instance.
 *
 * This class only exists to handle cases where an incompatible version of pimple
 * exists in the host application.
 */
class TwLogger_Saver
{
    /**
     * Get a saver instance based on configuration data.
     *
     * @param array $config The configuration data.
     * @return TwLogger_Saver_File|TwLogger_Saver_Elastic
     */
    public static function factory($config)
    {
        switch ($config['save.handler']) {
            case 'file':
                return new TwLogger_Saver_File($config['save.handler.filename']);
            case 'elastic':
            default:
                $client = Elasticsearch\ClientBuilder::create()->setHosts($config['hosts'])->build();
                return new TwLogger_Saver_Elastic($client);
        }
    }
}
