<?php

$use_js=true;

DEFINE("SQSTAT_SHOWLEN",60);

$squidhost[0]="10.10.10.1";
$squidport[0]=3128;
$cachemgr_passwd[0]="";
$resolveip[0]=true;
$group_by[0]="host";
$db=true;

/*
Squid proxy server ip address or host name
    $squidhost[0]="10.10.10.1";
Squid proxy server port
    $squidport[0]=3128;
cachemgr_passwd in squid.conf. Leave blank to disable authorisation
    $cachemgr_passwd[0]="";
Resolve user IP addresses or print them as numbers only [true|false]
    $resolveip[0]=false;
hosts-like file.
    $hosts_file[0]="hosts.txt"
Group users by hostname - "host" or by User - "username". Username work only with squid 2.6+
    $group_by[0]="host";

you can specify more than one proxy in the configuration file, e.g.:
    $squidhost[1]="192.168.0.2";
    $squidport[1]=3129;
    $cachemgr_passwd[1]="secret";
    $resolveip[1]=true;
    $hosts_file[1]="otherhosts.txt"