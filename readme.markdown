Klout API Wrapper
=================

Requirements
------------
* PHP 5.2+
* Curl
* [Klout API Key](http://developer.klout.com/member/register)

Installation:
-------------
Place the CalEvans directory in a directory in your include_path.


Usage:
------
    $o = new CalEvans_Klout($key,'CalEvans');
    foreach ($o->topics() as $topic) {
        echo $topic . "\n";
    }

The value returned from Klout, in the requested format, is stored. A second call
to the same method will not make a second call to klout.com, it will simply
return the stored value.

When requesting XML, the raw XML is not stored, a simplexml object
representation is stored.

Release Notes:
--------------
1.0 - Initial public release