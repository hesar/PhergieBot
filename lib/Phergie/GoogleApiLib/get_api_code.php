<?php
$scope         =   'https://www.googleapis.com/auth/calendar';
$client_id      =   '1009383154614-vundc2b345tl30auj444aua66rnt4a5c.apps.googleusercontent.com';
$redirect_uri   =   'urn:ietf:wg:oauth:2.0:oob';

$params = array(
                    'response_type' =>   'code',
                    'client_id'     =>   $client_id,
                    'redirect_uri'  =>   $redirect_uri,
                    'scope'         =>   $scope
                    );
$url = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);        
echo $url."\n";

/**
* token received: 4/55MNpkzwldwPUcdlkvndejlxN_Q_.sieFIMh7BMITOl05ti8ZT3Y_9K3EcwI
* second for calendar: 4/X4K5PU72U2Pd_JYOxXb94RrycVqr.Ikshgs-T0twdOl05ti8ZT3YLysjEcwI
*/
?>


