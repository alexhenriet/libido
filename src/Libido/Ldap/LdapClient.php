<?php

namespace Libido\Ldap;

use Libido\Ldap\LdapClientException;

class LdapClient
{
    protected $host;
    protected $port;
    protected $user;
    protected $pass;
    protected $baseDn;
    protected $connection;

    /**
     * LdapClient constructor.
     * @param $host
     * @param $port
     * @param $user
     * @param $pass
     * @param $baseDn
     */
    public function __construct($host, $port, $user, $pass, $baseDn)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->baseDn = $baseDn;
    }

    /**
     * @throws LdapClientException
     */
    public function connect()
    {
        $this->connection = \ldap_connect($this->host, $this->port);
        \ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        \ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
        if (!@\ldap_bind($this->connection, $this->user, $this->pass)) {
            throw new LdapClientException('Unable to bind');
        }
    }

    /**
     *
     */
    public function disconnect()
    {
        \ldap_close($this->connection);
    }

    /**
     * @param $filter
     * @param array $cols
     * @return array
     * @throws LdapClientException
     */
    public function search($filter, $cols = array())
    {
        if (!is_string($filter)) {
            throw new LdapClientException("Filter with attribute=value form expected");
        }
        $search = \ldap_search($this->connection, $this->baseDn, $filter, $cols);
        return \ldap_get_entries($this->connection, $search);
    }

    public static function parseResults($entries)
    {
        $fmtdEntries = array();
        if (!isset($entries['count'])) {
            return $fmtdEntries;
        }
        $nbEntries = $entries['count'];
        for ($i = 0; $i < $nbEntries; $i++) {
            $fmtdEntry = array();
            while(list($key, $value) = each($entries[$i])) {
                if (is_int($key)) {
                    continue;
                }
                $fmtdEntry[$key] = $value[0];
            }
            $fmtdEntries[] = $fmtdEntry;
        }
        return $fmtdEntries;
    }

}