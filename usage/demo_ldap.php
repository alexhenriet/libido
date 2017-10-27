<?php

require __DIR__ . '/../vendor/autoload.php';

use Libido\Ldap\LdapClient;

$params = require __DIR__ . '/ldap_params.php';
$ldapClient = new LdapClient(
    $params['host'], $params['port'], $params['user'], $params['pass'], $params['baseDn']
);
$ldapClient->connect();
$limitCols = array(
    'sn', 'givenname', 'telephonenumber', 'company', 'mail', 'preferredlanguage'
);
print_r(
    LdapClient::parseResults(
        $ldapClient->search(
            $params['filter'], $limitCols
        )
    )
);