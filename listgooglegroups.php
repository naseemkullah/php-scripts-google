//replace my_domain with a domain
//configure redirect uri to point to oauth2callback.php
<?php
require_once __DIR__.'/vendor/autoload.php';
session_start();
$client = new Google_Client();
$client->setAuthConfig('client_secrets.json');
$client->addScope('https://www.googleapis.com/auth/admin.directory.group');

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  $redirect_uri = 'https://path.to/oauth2callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}


$service = new Google_Service_Directory($client);
$pageToken = NULL;
$optParams = array(
  'customer' => 'my_customer',
  'domain' => 'my_domain'
);

try {
  do {
    if ($pageToken){
    $optParams['pageToken'] = $pageToken;
  }

$results = $service->groups->listGroups($optParams);
$pageToken = $results->getNextPageToken();
$groups = $results->getGroups();

foreach($groups as $group) {
  $groupsemails = $group->getEmail();
  echo $groupsemails.'<br>';
}

} while($pageToken);

} catch (Exception $e) {
    print 'An error occurred: ' . $e->getMessage();
}
