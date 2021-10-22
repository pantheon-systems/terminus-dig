<?php

namespace Pantheon\TerminusDig\Commands;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Commands\StructuredListTrait;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class DigCommand
 * 
 * @package Pantheon\TerminusDig\Commands
 */
class DigCommand extends TerminusCommand implements SiteAwareInterface 
{
    use SiteAwareTrait;
    use StructuredListTrait;

    private $site;
    private $environment;

    /**
     * Object constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the DNS record.
     *
     * @command dig
     * 
     * @usage dig <site>.<env> --type=AAAA
     * @usage dig <site>.<env> --type=CNAME
     * @usage dig <site>.<env> --server=dbserver
     */
    public function getDNSRecords($site_env, $options = ['server' => 'appserver', 'type' => 'A']) 
    {
        $type = null;
        switch ($options["type"]) {
            case 'A':
            case 'a':
                    $type = DNS_A;
                    break;
            case 'AAAA':
            case 'aaaa':
                $type = DNS_AAAA;
                break;
            case 'CNAME':
            case 'cname':
                $type = DNS_CNAME;
                break;
            case 'MX':
            case 'mx':
                $type = DNS_MX;
                break;
            case 'NS':
            case 'ns':
                $type = DNS_NS;
                break;
            case 'CAA':
            case 'caa':
                $type = DNS_CAA;
                break;
            case 'SOA':
            case 'soa':
                $type = DNS_SOA;
                break;
            case 'TXT':
            case 'txt':
                $type = DNS_TXT;
                break;
            default:
                $this->log()->error('Invalid type');
        }

        // Get env_id and site_id.
        $this->DefineSiteEnv($site_env);
        $site = $this->site->get('name');

        $env = $this->environment->id;
        $env_id = $this->environment->get('id');
        $site_id = $this->site->get('id');

        if ($options["server"] == "appserver") {
            // Get all appservers' IP address.
            $appserver_dns_records = dns_get_record("appserver.$env_id.$site_id.drush.in", $type);

            // Appserver - Loop through the record and download the logs.
            foreach($appserver_dns_records as $appserver) 
            {
                print $appserver['ip'] . "\n";
            }
        }
        
        if ($options["server"] == "dbserver")
        {
            // Get dbserver IP address.
            $dbserver_dns_records = dns_get_record("dbserver.$env_id.$site_id.drush.in", $type);
  
            foreach($dbserver_dns_records as $dbserver) 
            {
                print $dbserver['ip'] . "\n";
            }
        }
        
    }

    /** 
     * Define site environment properties.
     * 
     * @param string $site_env Site and environment in a format of <site>.<env>.
     */
    private function DefineSiteEnv($site_env)
    {
        list($this->site, $this->environment) = $this->getSiteEnv($site_env);
    }

    /**
     * Exclude files and dirs.
     */
    private function Exclude()
    {
        return ['.DS_Store', '.', '..'];
    }
}
