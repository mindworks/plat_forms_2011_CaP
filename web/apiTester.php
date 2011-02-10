<?php
  $x = new StdClass();

  $x->name = 'API-Conference';
  $x->startdate = '2011-01-01';
  $x->enddate   = '2011-01-01';

  $s1 = new StdClass();

  $s1->id = 1;

  $x->series = $s1;


  echo json_encode($x);

?>
<form action="/ws/conferences" method="post">
  <textarea name="data" rows="20" cols="100"></textarea>
  <input type="submit" value="Go" />
</form>