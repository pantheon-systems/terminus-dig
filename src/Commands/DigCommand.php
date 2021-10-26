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
     * Get the DNS record of any domain. By default, the type is A record.
     *
     * @command dig:domain
     * 
     * @usage dig:domain --domain=pantheon.io
     * @usage dig:domain --domain=pantheon.io --type=NS
     * @usage dig:domain --domain=pantheon.io --type=MX
     * @usage dig:domain --domain=pantheon.io --type=CNAME
     * @usage dig:domain --domain=pantheon.io --type=SOA
     * @usage dig:domain --domain=pantheon.io --type=TXT
     * @usage dig:domain --domain=pantheon.io --type=CAA
     * @usage dig:domain --domain=pantheon.io --type=AAAA
     */
    public function customDomainRecords($options = ['type' => 'A', 'domain' => '']) 
    {
        $type = null;
        switch ($options["type"]) 
        {
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

        if (!empty($options["domain"]))
        {
            $dns_records = dns_get_record($options["domain"], $type);

            foreach($dns_records as $records) 
            {
                switch ($options["type"]) {
                    case 'A':
                    case 'a':
                        if (isset($records['ip']))
                        {
                            print $records['ip'] . "\n";
                        }
                        else {
                            $this->log()->notice('No A record found.');
                        }
                        break;
                    case 'AAAA':
                    case 'aaaa':
                        if (isset($records['ipv6']))
                        {
                            print $records['ipv6'] . "\n";
                        }
                        else 
                        {
                            $this->log()->notice('No AAAA record found.');
                        }
                        break;
                    case 'CNAME':
                    case 'cname':
                        if (isset($records['target']))
                        {
                            print $records['target'] . "\n";
                        }
                        else 
                        {
                            $this->log()->notice('No CNAME record found.');
                        }
                        break;
                    case 'MX':
                    case 'mx':
                        if (isset($records['target']))
                        {
                            print $records['target'] . "\n";
                        }
                        else 
                        {
                            $this->log()->notice('No MX record found.');
                        }
                        break;
                    case 'NS':
                    case 'ns':
                        if (isset($records['target']))
                        {
                            print $records['target'] . "\n";
                        }
                        else 
                        {
                            $this->log()->notice('No NS record found.');
                        }
                        break;
                    case 'CAA':
                    case 'caa':
                        if (isset($records['value'])) 
                        {
                            print $records['value'] . "\n";
                        }
                        else 
                        {
                            $this->log()->notice('No CAA record found.');
                        }
                        break;
                    case 'SOA':
                    case 'soa':
                        if (isset($records['mname'])) 
                        {
                            print $records['mname'] . "\n";
                        }
                        else 
                        {
                            $this->log()->notice('No NNAME record found.');
                        }

                        if (isset($records['rname'])) 
                        {
                            print $records['rname'] . "\n";
                        }
                        else 
                        {
                            $this->log()->notice('No RNAME record found.');
                        }
                        break;
                    case 'TXT':
                    case 'txt':
                        if (isset($records['entries'])) 
                        {
                            print_r($records['entries']);
                        }
                        else 
                        {
                            $this->log()->notice('No TXT record found.');
                        }
                        break;
                    default:
                        $this->log()->error('Invalid type');
                }
            }
        }
        else {
            $this->log()->error('Missing domain');
        }
    }

    /**
     * Get the A record of either appserver or dbserver. By default, the server is set to appserver.
     *
     * @command dig:server
     * 
     * @usage dig:server <site>.<env>
     * @usage dig:server <site>.<env> --server=dbserver
     */
    public function getServerIP($site_env, $options = ['server' => 'appserver', 'type' => 'A']) 
    {
        $type = null;
        switch ($options["type"]) 
        {
            case 'A':
            case 'a':
                $type = DNS_A;
                break;
            case 'AAAA':
            case 'aaaa':
                $type = DNS_AAAA;
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

        $dns_records = array();
      
        // For database server.
        if ($options["server"] == "dbserver")
        {
            // Get dbserver IP address.
            $dbserver_dns_records = dns_get_record("dbserver.$env_id.$site_id.drush.in", $type);

            foreach($dbserver_dns_records as $dbserver) 
            {
                print $dbserver['ip'] . "\n";
            }
        }

        // For application server.
        if ($options["server"] == "appserver")
        {
            // Get all appservers' IP address.
            $dns_records = dns_get_record("appserver.$env_id.$site_id.drush.in", $type);        
        } 

        // Appserver - Loop through the record and download the logs.
        foreach($dns_records as $records) 
        {
            switch ($options["type"]) {
                case 'A':
                case 'a':
                    if (isset($records['ip']))
                    {
                        print $records['ip'] . "\n";
                    }
                    else {
                        $this->log()->notice('No A record found.');
                    }
                    break;
                case 'AAAA':
                case 'aaaa':
                    if (isset($records['ipv6']))
                    {
                        print $records['ipv6'] . "\n";
                    }
                    else 
                    {
                        $this->log()->notice('No AAAA record found.');
                    }
                    break;
                default:
                    $this->log()->error('Invalid type');
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
