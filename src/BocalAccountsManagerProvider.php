<?php

namespace ETNA\Silex\Provider\BocalAccounts;

use Silex\Application;
use Pimple\ServiceProviderInterface;
use Pimple\Container;

class BocalAccountsManagerProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $api_key      = getenv("BOCAL_API_KEY");
        $api_endpoint = getenv("BOCAL_API_URL");
        $read_only    = !(getenv("BOCAL_READ_ONLY") === "false");

        if (false === $api_key) {
            return $app->abort(500, "No Bocal API key");
        }

        $app["bam"] = new BocalAccountsManager($api_endpoint, $api_key, $read_only);
    }
}
