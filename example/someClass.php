<?
/* ------------------------------------------------------------------------ *\
	Copyright 2007-2014 Your Company ltd. All rights reserved
\* ------------------------------------------------------------------------ */
?>
<?php
abstract class F11ab1c49
{
public $msg='';
public $sql='';
protected $V36c712da;
protected $V4bdda913 = array();
protected $Ve9d65212 = array();
protected $V2b847bec;
protected $Vc6cafee7 = array();
protected $V71f7be02;
protected $V77d3e4dc = '__LAST_INSERT_ID__';
protected $V834aacdd = array();
private $Vbf1d4eff = '`';
private $V062f067e = '`';
public function __construct()
{
if (empty($this->Vc6cafee7))
{ throw new Exception('pv_aliasNames2colNames is empty');
} $this->V71f7be02= array_flip($this->Vc6cafee7);
}
private function F4ce4015e($Vdf6eeb13)
{ if (isset($this->Vc6cafee7[$Vdf6eeb13]))
{ return $this->Vc6cafee7[$Vdf6eeb13];
} return $Vdf6eeb13;
} 
private function Fe940998f($V7e287edc)
{ if (isset($this->V71f7be02[$V7e287edc]))
{ return $this->V71f7be02[$V7e287edc];
} return $V7e287edc;
} 
private function F42858f10($Vdf6eeb13)
{ $V8193fdf0 = '';
$V7e287edc = $this->F4ce4015e($Vdf6eeb13);
$Vbccbf69d = array();
if (preg_match('#(.+?)\.(.+)#', $V7e287edc, $Vbccbf69d))
{ $V8193fdf0 = $Vbccbf69d[1];
$V7e287edc = $Vbccbf69d[2];
} return array(
'tbl' => $V8193fdf0,
'col' => $V7e287edc
);
} 
private function Fd0475041($V3ba4cf23, $Vd75b70be=array())
{ $V2bf298f9 = array();
if (!empty($V3ba4cf23))
{ foreach ($V3ba4cf23 as $Vf71a2b7d => &$V94309e6b)
{
if (!empty($Vd75b70be) && in_array($Vf71a2b7d, $Vd75b70be))
{ continue;
}
$V94309e6b = $this->Fdd854504($Vf71a2b7d, $V94309e6b);
$Vaeb948e0 = $this->F42858f10($Vf71a2b7d);
$V8193fdf0 = $Vaeb948e0['tbl'];
$Vae1d8f33 = $Vaeb948e0['col'];
unset($Vaeb948e0);
if (!isset($V2bf298f9[$V8193fdf0]))
{ $V2bf298f9[$V8193fdf0] = array();
} $V2bf298f9[$V8193fdf0][$Vae1d8f33] = $V94309e6b;
} } return $V2bf298f9;
}
protected function F71555a34($V3ba4cf23, $Vd75b70be=array(), $V5d5ce40a=true)
{ $V2bf298f9 = array();
if (!empty($V3ba4cf23))
{ foreach ($V3ba4cf23 as $Vf71a2b7d => &$V94309e6b)
{
if (!empty($Vd75b70be) && in_array($Vf71a2b7d, $Vd75b70be))
{ continue;
}
$V61f65f3c = '=';
if (is_array($V94309e6b))
{
if (!isset($V94309e6b[0]) || !isset($V94309e6b[1]) || !preg_match('#^([<>=!RLIKE ]+|IN|NOT IN|IS|IS NOT)$#', $V94309e6b[0]))
{ continue;
} $V61f65f3c = $V94309e6b[0];
$V94309e6b = $V94309e6b[1];
}
if ($V61f65f3c == 'IN' || $V61f65f3c == 'NOT IN')
{ $V94309e6b = $this->F6407631e($Vf71a2b7d, $V94309e6b);
} else
{ $V94309e6b = $this->Fdd854504($Vf71a2b7d, $V94309e6b);
} $V7e287edc = $this->F4ce4015e($Vf71a2b7d);
if ($V5d5ce40a)
{ if ($V61f65f3c == 'IN' || $V61f65f3c == 'NOT IN')
{ $V2bf298f9[] = $V7e287edc.' '.$V61f65f3c.' ('.$V94309e6b.')';
} elseif ($V61f65f3c == 'IS' || $V61f65f3c == 'IS NOT')
{ $V2bf298f9[] = $V7e287edc.' '.$V61f65f3c.' NULL';
} elseif (is_string($V94309e6b))
{ $V2bf298f9[] = $V7e287edc.' '.$V61f65f3c.' \''.$V94309e6b.'\'';
} else
{ $V2bf298f9[] = $V7e287edc.' '.$V61f65f3c.' '.$V94309e6b.'';
} } else
{ $V2bf298f9[] = $V7e287edc.' LIKE \''.$V94309e6b.'\'';
} } } return $V2bf298f9;
} 
protected function F0be0c9c1($Vdb64e12f, $Vd75b70be=array(), $V5d5ce40a=true)
{ $Vaaebcfff = $this->F71555a34($Vdb64e12f, $Vd75b70be, $V5d5ce40a);
if (!empty($Vaaebcfff))
{ $V4ecc28f5='WHERE (' . implode(') AND (', $Vaaebcfff) .')';
} else
{ $V4ecc28f5='';
} return $V4ecc28f5;
} 
protected function F8798a4a9($V4ba49586, $Vd75b70be=array())
{ $Vcdbd1ded = $this->F71555a34($V4ba49586, $Vd75b70be);
if (!empty($Vcdbd1ded))
{ $V5f3968da='SET ' . implode(', ', $Vcdbd1ded);
} else
{ $V5f3968da='';
} return $V5f3968da;
} 
protected function Ff56b3f7f($V4ba49586, $Vd75b70be=array())
{ $Vb235b783 = $this->Fd0475041($V4ba49586, $Vd75b70be);
$V25fe9a70 = array();
if (!empty($Vb235b783))
{ foreach ($Vb235b783 as $V8193fdf0=>$Vbc1e6e5e)
{ $V25fe9a70[$V8193fdf0] = array(
'keys'=>array(),
'vals'=>array(),
);
foreach ($Vbc1e6e5e as $Vf71a2b7d=>$V94309e6b)
{ $V25fe9a70[$V8193fdf0]['keys'][] = $Vf71a2b7d;
$V25fe9a70[$V8193fdf0]['vals'][] = $V94309e6b;
} $V25fe9a70[$V8193fdf0]['vals'] = "VALUES ('". implode("', '", $V25fe9a70[$V8193fdf0]['vals']) ."')";
$V25fe9a70[$V8193fdf0]['keys'] = '('.$this->Vbf1d4eff
. implode($this->V062f067e.', '.$this->Vbf1d4eff, $V25fe9a70[$V8193fdf0]['keys'])
. $this->V062f067e.')';
} } return $V25fe9a70;
} 
protected function F8a2ce552($V995dc27a, $Vd75b70be=array())
{ $V52d6f3b1 = array();
foreach ($V995dc27a as $Vdf6eeb13)
{
if (!empty($Vd75b70be) && in_array($Vdf6eeb13, $Vd75b70be))
{ continue;
}
if (isset($this->Vc6cafee7[$Vdf6eeb13]))
{ $V7e287edc = $this->Vc6cafee7[$Vdf6eeb13];
$V52d6f3b1[] = "$V7e287edc as '$Vdf6eeb13'";
} } if (empty($V52d6f3b1))
{ $V52d6f3b1 = '';
} else
{ $V52d6f3b1 = implode(', ', $V52d6f3b1);
} return $V52d6f3b1;
}
protected function Fdd854504($Vdf6eeb13, $V94309e6b)
{ if (!is_integer($V94309e6b))
{ if (!empty($this->V4bdda913) && array_search($Vdf6eeb13, $this->V4bdda913)!==false)
{ $V94309e6b = intval($V94309e6b);
} else
{ $V94309e6b = mysql_real_escape_string($V94309e6b);
} } return $V94309e6b;
} 
private function F6407631e($Vdf6eeb13, $V94309e6b)
{ if (!is_array($V94309e6b))
{ $V94309e6b = explode(',', $V94309e6b);
} $V7979ddd7 = array();
foreach ($V94309e6b as $v)
{ $v = $this->Fdd854504($Vdf6eeb13, $v);
if (is_string($v))
{ $v = '\''.$v.'\'';
} $V7979ddd7[] = $v;
} return implode(',', $V7979ddd7);
} 
protected function F01892f00(&$V31e2122f, $V9289ac9b)
{ $V31e2122f = strtr($V31e2122f, array($this->V77d3e4dc=> $V9289ac9b));
} 
private function F018065a9($V652f0465)
{ $this->sql = $V652f0465;
$this->msg = mysql_error();
trigger_error("\nSQL error: {$this->msg}\nSQL:{$this->sql}\n", E_USER_ERROR);
}
protected function F8ac2c982(&$Vedc28599, $V652f0465, $Vd1df70ec = false)
{ $V62934996 = mysql_query($V652f0465);
if ($V62934996==false)
{ $this->F018065a9($V652f0465);
return false;
} if (empty($this->V4bdda913) || $Vd1df70ec)
{ while ($row = mysql_fetch_array($V62934996, MYSQL_ASSOC))
{ $Vedc28599[] = $row;
} } else
{ while ($row = mysql_fetch_array($V62934996, MYSQL_ASSOC))
{ foreach ($row as $Vdf6eeb13=>&$val)
{ if (array_search($Vdf6eeb13, $this->V4bdda913)!==false)
{ $row[$Vdf6eeb13] = intval($row[$Vdf6eeb13]);
} } $Vedc28599[] = $row;
} } mysql_free_result ($V62934996);
return true;
} 
protected function Fac5546b9($V652f0465)
{ $V62934996 = mysql_query($V652f0465);
if ($V62934996==false)
{ $this->F018065a9($V652f0465);
return false;
} return mysql_affected_rows();
} 
protected function F849f9f5d()
{ return mysql_insert_id();
}
protected function F1bf8b4f1(&$V4ba49586)
{ } 
protected function F21375b94(&$V4ba49586)
{ } 
public function F606b94b2(&$Vedc28599, $V14bd4c8f, $Vdb64e12f=array())
{ $Vedc28599 = array();
if (empty($this->V834aacdd) || !isset($this->V834aacdd[$V14bd4c8f]))
{ $this->msg = 'Unknown template';
return 0;
} $V652f0465 = $this->V834aacdd[$V14bd4c8f];
$V567b024c = '#\{(?:pv_constraints|pv_ograniczenia)(\|([\s\S]+?))?\}#';
$V7e707a32 = array();
if (preg_match($V567b024c, $V652f0465, $V7e707a32))
{ $V4ecc28f5 = $this->F0be0c9c1($Vdb64e12f);
$Vcab7b8f3 = '';
if (!empty($V4ecc28f5))
{ $Vcab7b8f3 = preg_replace('#^WHERE\s+(.+)$#', '($1)', $V4ecc28f5);
}
else if (count($V7e707a32)>2)
{ $Vcab7b8f3 = $V7e707a32[2];
}
$V652f0465 = preg_replace($V567b024c, $Vcab7b8f3, $V652f0465);
} 
$this->F8ac2c982($Vedc28599, $V652f0465);
if (!empty($Vedc28599))
{ return 1;
} else
{ return 0;
} } 
public function Fa7fb9317($V4ba49586)
{ if (empty($this->V36c712da))
{ throw new Exception("Tabel name is empty");
}   
$this->F1bf8b4f1($V4ba49586);
$V25fe9a70 = $this->Ff56b3f7f($V4ba49586, $this->Ve9d65212);
$sql = "INSERT INTO {$this->V36c712da} {$V25fe9a70['']['keys']} {$V25fe9a70['']['vals']}";
$Vd1b7dd14 = $this->Fac5546b9($sql);
if ($Vd1b7dd14==0)
{ $this->msg = 'DB error while inserting record!';
return 0;
} return 1;
} 
public function Fc5660b2b($Vdb64e12f=array(), $V5d5ce40a=true)
{ if (empty($this->V36c712da))
{ throw new Exception("Table name is empty");
}   
$V4ecc28f5 = $this->F0be0c9c1($Vdb64e12f, array(), $V5d5ce40a);
$sql = "DELETE
FROM {$this->V36c712da} $V4ecc28f5"
;
$Vd1b7dd14 = $this->Fac5546b9($sql);
if ($Vd1b7dd14===false)
{ $this->msg = 'DB error while deleting record(s)!';
return 0;
} return 1;
} 
public function F6e07282d($V4ba49586, $Vdb64e12f=array(), $Vd75b70be=array(), $V5d5ce40a=true)
{ if (empty($this->V36c712da))
{ throw new Exception("Table name is empty");
}   
$this->F21375b94($V4ba49586);
$V4ecc28f5 = $this->F0be0c9c1($Vdb64e12f, array(), $V5d5ce40a);
$V5f3968da = $this->F8798a4a9($V4ba49586, $Vd75b70be);
$sql = "UPDATE {$this->V36c712da} $V5f3968da
$V4ecc28f5"
;
$Vd1b7dd14 = $this->Fac5546b9($sql);
if ($Vd1b7dd14===false)
{ $this->msg = 'DB error while updating record(s)!';
return 0;
} return 1;
} 
public function F633c953d(&$Vedc28599, $V995dc27a=array(), $Vdb64e12f=array(), $V5d5ce40a=true)
{ if (empty($this->V36c712da))
{ throw new Exception("Table name is empty");
} $Vedc28599 = array();
$V4ecc28f5 = $this->F0be0c9c1($Vdb64e12f, array(), $V5d5ce40a);
$V3b215a83 = empty($this->V2b847bec) ? "" : $this->V2b847bec;
if (empty($V995dc27a))
{ $V995dc27a = array_keys($this->Vc6cafee7);
} $Vf668e29f = $this->F8a2ce552($V995dc27a);
$sql = "SELECT $Vf668e29f
FROM {$this->V36c712da} $V4ecc28f5
$V3b215a83"
;
$this->F8ac2c982($Vedc28599, $sql);
return !empty($Vedc28599) ? 1 : 0;
}
} ?>