README.TXT
======================================================================

Project:   Klout API wrapper for PHP
Author:    Cal Evans <cal@calevans.com>
Copyright: 2011 Cal Evans
License:   BSD http://www.opensource.org/licenses/bsd-license.php
Requires:  PHP 5.1+, Curl

Installation:
Place the CalEvans directory in a directory in your include path.


Usage:

$o = new CalEvans_Klout($key,'CalEvans');
$o->setFormat('json');
$topics = $o->topics();
foreach ($topics->users[0]->topics as $topic) {
    echo $topic . "\n";
}

The value returned from Klout, in the requested format, is stored off. A second
call to the same method will not make a second call to klout.com, it will
simply return the stored value.

When requesting XML, the raw XML is not stored, a simplexml object
representation is stored.

Release Notes:
1.0 - Initial public release