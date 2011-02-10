[<?php  
//$user              = $sf_data->getRaw('user');
//$array             = $user->toArray();
//$array['password'] = '[hidden]';
//$seeDetails = ($sf_user->isAdmin() || $user->isContactOf());
//if (!$seeDetails):
//  $array['fullname']      = '[hidden]';
//  $array['email_address'] = '[hidden]';
//  $array['latitude']      = '[hidden]';
//  $array['longitude']     = '[hidden]';
//endif;
//unset($array['Contacts']);
//echo json_encode($array);
$contacts = $sf_data->getRaw('contacts');
foreach ($contacts as $contact):
  $user              = $contact->getReceiver();
  $array             = $user->toArray();
  $array['password'] = '[hidden]';
  $seeDetails        = ($sf_user->isAdmin() || $user->isContactOf());
  if (!$seeDetails):
    $array['fullname']      = '[hidden]';
    $array['email_address'] = '[hidden]';
    $array['latitude']      = '[hidden]';
    $array['longitude']     = '[hidden]';
  endif;
  unset($array['Contacts']);
  echo json_encode($array);
  echo ',';
endforeach;
?>]