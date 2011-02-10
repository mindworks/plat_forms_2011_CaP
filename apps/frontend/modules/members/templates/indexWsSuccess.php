[<?php 
$users = $sf_data->getRaw('users');
foreach($users as $aUser):
  $seeDetails = ($sf_user->isAdmin() || $aUser->isContactOf());
  $array      = $aUser->toArray();
  unset($array['Permissions'], $array['Groups']);
  $array['password'] = '[hidden]';
  if (!$seeDetails):
    $array['fullname']      = '[hidden]';
    $array['email_address'] = '[hidden]';
    $array['latitude']      = '[hidden]';
    $array['longitude']     = '[hidden]';
  endif;
  echo json_encode($array);
  echo ',';
endforeach; ?>]