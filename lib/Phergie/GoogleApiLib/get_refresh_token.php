<?php
$url = 'https://accounts.google.com/o/oauth2/token';
$post_data = array(
                    'code'          =>   '4/X4K5PU72U2Pd_JYOxXb94RrycVqr.Ikshgs-T0twdOl05ti8ZT3YLysjEcwI',
                    'client_id'     =>   '1009383154614-vundc2b345tl30auj444aua66rnt4a5c.apps.googleusercontent.com',
                    'client_secret' =>   'KNjhOgF0b9H3ggH3gRR0uMcQ',
                    'redirect_uri'  =>   'urn:ietf:wg:oauth:2.0:oob',
                    'grant_type'    =>   'authorization_code',
                    );
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
$token = json_decode($result);
var_dump($result);
echo $token->refresh_token . "\n";

/**
* string(203) "{ "access_token" : "ya29.AHES6ZSjR_E521etEqOxVyTVIvYLUwQOTmZA2JknXsnyV9Z4E7IphA", 
*				"token_type" : "Bearer",
*				"expires_in" : 3600,
*				"refresh_token" : "1/uDfgGh-gfDljFj3nqdzeAOTAGFMoflaoEM72nnMjF4k" }" 
*				
* received refresh token: 1/uDfgGh-gfDljFj3nqdzeAOTAGFMoflaoEM72nnMjF4k 
*/
?>
