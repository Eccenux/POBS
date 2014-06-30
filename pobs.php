<?
	error_reporting(E_ERROR | E_WARNING | E_PARSE);

	/*

	POBS - PHP Obfuscator

	August 10th 2003++

	Version: 0.99nux2

	- AUTHOR
			- Frank Karsten (http://www.walhalla.nl)
	- ADDING
			- Florian PERRICHOT.
			- Steve BEURET
			- Philip ROBINSON
			- Gr�gory GRAUMERre
			- Mark (mark@mylinks.sk)
			- Maciej "Nux" Jaros

	For the most up-to-date documentation visit:
	http://pobs.mywalhalla.net

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License as
	published by the Free Software Foundation; either version 2 of the
	License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	General Public License for more details.
	*/

	include 'pobs-ini.inc.php';
?>
<HTML>
<HEAD>
	<TITLE>POBS - A PHP Obfuscator</TITLE>
	<STYLE TYPE="text/css">
		body { font-family: Arial, sans serif;font-size:<? echo ($FontSize+2); ?>pt;  vertical-align: top; }
		td { font-family: Verdana, sans serif;font-size:<? echo $FontSize; ?>pt;  vertical-align: top; }
	</STYLE>
</HEAD>
<BODY>
<?
	if (!empty($_POST) ) extract($_POST);
	else if (!empty($HTTP_POST_VARS)) extract($HTTP_POST_VARS);

	if ( isset( $OK ) ) CheckSafeMode();

	$StartTime = time();

	$TotalFileSizeRead = 0;
	$TotalFileSizeWrite = 0;
	$NewlinesReplaced = 0;

	$ExVarArray = array();
	$FuncArray = array();
	$ClassArray = array();
	$ConstArray = array();
	$VarArray = array();
	$ObjectVarArray = array();
	$JSVarArray = array();
	$JSFuncArray = array();

	$LineArray = array();
	$FileArray = array();

	$UdExcVarArrayWild = array();
	$UdExcVarArrayDliw=array();
	$UdExcFileArrayRegEx = array();
	$UdExcDirArrayRegEx = array();

	$ExcludedLines = array();
	$CopyrightText = trim($CopyrightText);
	$CopyrightText = str_replace("\r","", $CopyrightText); // without this it was making double
															// newlines on Windows XP
															// just hope it works in UNIX as well

	if ( isset( $OK ) ) // if action parameter in querystring
	{
		if (!(is_readable($SourceDir)))
		{
			echo "Error. Source Directory ".$SourceDir." is not readable. Program will terminate<br>";
			exit;
		}

		if (!(is_writeable($TargetDir)))
		{
			echo "Error. Target Directory ".$TargetDir." is not writeable. Program will terminate<br>";
			exit;
		}

		echo	'<h3>Execute POBS : &quot;'.$SourceDir.'&quot; =&gt; &quot;'.$TargetDir.'&quot;</h3>';

		GetWildCards();
		ScanSourceFiles();

		krsort( $FuncArray );
		krsort( $ConstArray );
		krsort( $VarArray );
		sort( $FileArray );

		if(!$ReplaceClasses)
		{

//foreach($ClassArray as $key)
//  echo "Class = $key<br>";
			// remove class names from functions
			// this way we also remove all constructors
			$tempFuncArray = array();
			foreach($FuncArray as $Key => $Value)
			{
				if(!in_array($Key, $ClassArray))
				{
					$tempFuncArray[$Key] = $Value;
				}
			}

			$FuncArray = $tempFuncArray;
		}

		ShowArrays();
		WriteTargetFiles();
	}
	else
		ShowScreen();


function ShowScreen() {
	global $TimeOut, $FileExtArray, $JSFileExtArray, $TargetDir, $SourceDir, $UdExcFuncArray, $UdExcVarArray, $UdExcConstArray, $StdObjRetFunctionsArray;
	global $ReplaceFunctions, $ReplaceConstants, $ReplaceVariables, $RemoveComments, $RemoveIndents, $ConcatenateLines, $CopyrightTextFromIni;
	global $FilesToReplaceArray, $UdExcFileArray, $UdExcDirArray;

?>
	<TABLE CELLPADDING=0 WIDTH=100% CELLSPACING=0 BORDER=0>
		<TR>
			<TD BGCOLOR=#6699CC VALIGN=TOP><A HREF="http://pobs.mywalhalla.net" TARGET=_new><IMG SRC="pobslogo.gif" HSPACE=20 WIDTH=150 HEIGHT=61 BORDER=0></A><TD>
			<TD BGCOLOR=#6699CC VALIGN=TOP><br><b>A PHP Obfuscator<br>Version 0.99</TD>
		</TR>
	</TABLE>
	<? CheckSafeMode(); ?>
	<TABLE CELLPADDING=3 WIDTH=100% CELLSPACING=0 BORDER=1 BORDERCOLOR=#000000>
		<TR><TD BGCOLOR=#6699CC VALIGN=TOP> <CENTER><DIV style="font-size:13pt"><b>Settings</DIV></CENTER></TD></TR>
		<TR><TD><CENTER>For the most up-to-date documentation, visit <A HREF="http://pobs.mywalhalla.net" TARGET="STD">http://pobs.mywalhalla.net</A></CENTER></TD></TR>
	</TABLE>
	<br>
	<TABLE CELLPADDING=3 WIDTH=100% CELLSPACING=0 BORDER=0>
		<TR>
			<TD WIDTH=60% VALIGN=TOP>
			<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=0 BORDER=1 BORDERCOLOR=#000000>
				<FORM METHOD="POST" ACTION="<? echo $GLOBALS['HTTP_SERVER_VARS']['PHP_SELF']?>">
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP><b>TimeOut (sec)</b></TD></TR>
				<TR><TD><? echo $TimeOut ?></TD></TR>
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP><b>Source Directory</b></TD></TR>
				<TR><TD><INPUT TYPE=TEXT NAME=SourceDir VALUE="<? echo $SourceDir ?>" SIZE=70></TD></TR>
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP><b>Target Directory</b></TD></TR>
				<TR><TD><INPUT TYPE=TEXT NAME=TargetDir VALUE="<? echo $TargetDir ?>" SIZE=70></TD></TR>
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP>
				<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=0>
				<TR><TD width=50%><b>Allowed File Extensions</b></TD><TD width=50%><b>Allowed JavaScriptFile Extensions</b></TD></TR>
				</TABLE>
				</TD></TR>
				<TR><TD>
				<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=0>
				<?
				$maxcount = (count($FileExtArray)>count($JSFileExtArray) ? count($FileExtArray) : count($JSFileExtArray));
				for($i=0; $i<$maxcount; $i++)
					echo "<TR><TD width=50%>".($FileExtArray[$i]!='' ? "$i: ".$FileExtArray[$i] : "&nbsp;")."</TD><TD width=50%>".($JSFileExtArray[$i]!='' ? "$i: ".$JSFileExtArray[$i] : "&nbsp;")."</TD></TR>\n";
				?>
				</TABLE>
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP><b>Replacements</b></TD></TR>
				<TR><TD>
					<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=2>
						<TR><TD WIDTH=130 valign="bottom">Classes</TD><TD WIDTH=10>&nbsp;</TD><TD><input type="checkbox" name="ReplaceClasses" value="1" checked></TD></TR>
						<TR><TD valign="bottom">Functions</TD><TD WIDTH=10>&nbsp;</TD><TD><input type="checkbox" name="ReplaceFunctions" value="1" checked></TD></TR>
						<TR><TD valign="bottom">Constants</TD><TD WIDTH=10>&nbsp;</TD><TD><input type="checkbox" name="ReplaceConstants" value="1"></TD></TR>
						<TR><TD valign="bottom">Variables</TD><TD WIDTH=10>&nbsp;</TD><TD><input type="checkbox" name="ReplaceVariables" value="1" checked></TD></TR>
						<TR><TD valign="bottom">JavaScript (Functions &amp; Variables)</TD><TD WIDTH=10 valign="top">&nbsp;</TD><TD><input type="checkbox" name="ReplaceJS" value="1">&nbsp;&nbsp;<? echo "+ files with extensions: "; foreach($JSFileExtArray as $Key => $Value ) echo '<b>'.$Value.'</b>,'; ?></TD></TR>
					</TABLE>
				</TD></TR>
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP><b>Removals</b></TD></TR>
				<TR><TD>
					<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=2>
						<TR><TD WIDTH=130 valign="bottom">Comments</TD><TD WIDTH=10>&nbsp;</TD><TD><input type="checkbox" name="RemoveComments" value="1" checked>
						(Always preserve first <INPUT TYPE="text" SIZE="3" NAME="KeptCommentCount" VALUE="0"> comments)
						</TD></TR>
						<TR><TD valign="bottom">Indents</TD><TD WIDTH=10>&nbsp;</TD><TD><input type="checkbox" name="RemoveIndents" value="1" checked></TD></TR>
						<TR><TD valign="bottom">Returns</TD><TD WIDTH=10>&nbsp;</TD><TD><input type="checkbox" name="ConcatenateLines" value="1"></TD></TR>
					</TABLE>
				</TD></TR>
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP><b>File system</b></TD></TR>
				<TR><TD>
					<INPUT TYPE=CHECKBOX NAME="ReplaceNewer" CHECKED>Replace edited files only<br>
					<INPUT TYPE=CHECKBOX NAME="RecursiveScan" CHECKED>Recursive scan (into sub-directory)<br>
					<INPUT TYPE=CHECKBOX NAME="CopyAllFiles" CHECKED>Copy all files (not in allowed file extensions) from source to dest <br>
				</TD></TR>
				<TR><TD>
					<b>Copyright Text</b> (to put on top of every processed file)<br>
					<INPUT TYPE=CHECKBOX NAME="CopyrightPHP" value=1 checked>on top of PHP files<br>
					<INPUT TYPE=CHECKBOX NAME="CopyrightJS" value=1 checked>on top of JavaScript files<br>
					<TEXTAREA name="CopyrightText" ROWS=9 COLS=70 style="width:100%"><? echo $CopyrightTextFromIni ?></TEXTAREA>
				</TD></TR>
				<TR>
					<TD BGCOLOR=#E6E6E6 ALIGN=CENTER VALIGN=TOP>
					<INPUT TYPE=SUBMIT NAME=OK VALUE="Start processing">
					</TD>
				</TR>
				</FORM>
			</TABLE>
			</TD>
			<TD WIDTH=20%>
			<TABLE CELLPADDING=3 WIDTH=100% CELLSPACING=0 BORDER=1 BORDERCOLOR=#000000>
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP><b>Exclude Functions</b></TD></TR>
				<TR><TD><? foreach($UdExcFuncArray as $Key => $Value ) echo $Key.': '.$Value.'<br>'; ?></TD></TR>
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP><b>Exclude Constants</b></TD></TR>
				<TR><TD><? foreach($UdExcConstArray as $Key => $Value ) echo $Key.': '.$Value.'<br>'; ?></TD></TR>
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP><b>Functions returning objects (special handling)</b></TD></TR>
				<TR><TD><? foreach($StdObjRetFunctionsArray as $Key => $Value ) echo $Key.': '.$Value.'<br>'; ?></TD></TR>
			</TABLE>
			</TD>
			<TD WIDTH=20%>
			<TABLE CELLPADDING=3 WIDTH=100% CELLSPACING=0 BORDER=1 BORDERCOLOR=#000000>
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP><b>Exclude Variables</b></TD></TR>
				<TR><TD><? foreach($UdExcVarArray as $Key => $Value ) echo $Key.': '.$Value.'<br>'; ?></TD></TR>
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP><b>Exclude Files</b></TD></TR>
				<TR><TD><? foreach($UdExcFileArray as $Key => $Value ) echo $Key.': '.$Value.'<br>'; ?></TD></TR>
				<TR><TD BGCOLOR=#E6E6E6 VALIGN=TOP><b>Exclude Directories</b></TD></TR>
				<TR><TD><? foreach($UdExcDirArray as $Key => $Value ) echo $Key.': '.$Value.'<br>'; ?></TD></TR>
			</TABLE>
			</TD>
		</TR>
	</TABLE>
<?
}

function GetWildCards() {
	// Scan UdExcVarArray and move the Variables with Wildcards (*) to a separate array
	// Separating the variables with wildcards speeds up the scanning and checking process
	global $UdExcVarArray, $UdExcVarArrayWild, $UdExcVarArrayDliw;
	global $UdExcFileArray, $UdExcFileArrayRegEx, $UdExcDirArray, $UdExcDirArrayRegEx;

	// process Exclude File array
	foreach($UdExcFileArray as $value)
	{
		// convert it to regular expression

		$value = str_replace(".", "\\.", $value);
		$value = str_replace("*", ".*", $value);
		$value = "/^$value/i";
		$UdExcFileArrayRegEx[] = $value;
	}

	foreach($UdExcDirArray as $value)
	{
		// convert it to regular expression
		$value = str_replace(".", "\\.", $value);
		$value = str_replace("\\\\", "\\/", $value);
		$value = str_replace("\\", "\\/", $value);
		$value = str_replace("/", "\\/", $value);
		$value = str_replace("*", ".*", $value);
		$value = "/$value/i";
		$UdExcDirArrayRegEx[] = $value;
	}

	foreach( $UdExcVarArray as $Key => $Value )
	{
		// SB adding support for wildcards that are wild at the front end (e.g. "*_x"
		//$pos=strrpos($Value, "*");
		//if ($pos!==FALSE) {
		$pos = strrpos(' '.$Value, "*");
		if ($pos>1) { //true of properly formed standard wildcards (* at end)
			echo 'WildCardValue:'.$Value.'<br>';
			array_push($UdExcVarArrayWild, str_replace( '*', '', $Value ) );
			$UdExcVarArray[$Key] = 'Niets'.$Key;
		}
		if ($pos==1) { //true of backwards wildcards (* at front)
			echo 'DliwCardValue:'.$Value.'<br>';
			array_push($UdExcVarArrayDliw, str_replace( '*', '', $Value ) );
			$UdExcVarArray[$Key] = 'Niets'.$Key;
		}
	}

	echo '&nbsp;<br>';
}

function findScriptTagInFile($index, $LineArray)
{
  $WholeFile = strtolower(implode("", $LineArray));
  $Line = strtolower($LineArray[$index]);

  $LinePos = strpos($WholeFile, $Line);
  if($LinePos === false)
	return false;

  $offset = 0;
  $MaxPos = false;

  // find closest $what string
  while(true)
  {
	$pos = strpos($WholeFile, '<script', $offset);
	if($pos === false)
		break;

	if($pos>$LinePos)
		break;

	$offset = $pos+1;
	$MaxPos = $pos;
  }

  if($MaxPos === false)
	return false;

  // found one, now check if there is not and ending tag before our line
  $pos = strpos($WholeFile, '</script', $MaxPos);

  if($pos === false || $pos > $LinePos )
	return true;

  return false;
}

function ScanSourceFiles( $path = '' )
{
	global $ExVarArray, $FuncArray, $ClassArray, $ConstArray, $VarArray, $LineArray, $FileArray;
	global $SourceDir, $TargetDir, $FileExtArray, $JSFileExtArray, $JSFuncArray, $ReplaceJS, $ReplaceFunctions, $ReplaceVariables, $ReplaceConstants, $MaxFiles;
	global $RecursiveScan, $CopyAllFiles; // File system option...
	global $StdExcJSFuncArray, $UdExcVarArray, $UdExcVarArrayWild, $UdExcFuncArray, $UdExcConstArray, $UdExcFileArrayRegEx, $UdExcDirArrayRegEx;

	$dir = dir( $SourceDir.$path.'/' );
	while( $FileNaam = $dir->read() )
	{
		$fileName = $path.'/'.$FileNaam;
		$excludeFile = FALSE;
		$excludeDirectory = FALSE;

		if ( is_file( $SourceDir.$fileName ) )
		{
			// check if file has the proper suffix
			$extpos = strrpos($FileNaam, ".");

			if($extpos>0)
				$Suffix = substr($FileNaam,$extpos+1);
			else
				$Suffix = md5(rand()); // generate some non existing extension

			if ((in_array($Suffix, $FileExtArray) || ($extpos==0 && in_array(".", $FileExtArray)) || (in_array($Suffix, $JSFileExtArray) && $ReplaceJS)) && sizeof($FileArray) < $MaxFiles)
			{
				// check if the file is in UdExcFileArray
				foreach($UdExcFileArrayRegEx as $value)
				{
					// compare file name with regular expression
					if(preg_match($value, $FileNaam))
					{
						$excludeFile = TRUE;
					}
				}

				if($excludeFile == FALSE)
				{
					if(in_array($Suffix, $JSFileExtArray))
					{
					// it is JavaScript file
					echo "<b>+ Scanning JavaScript File: ".substr($fileName, 1)."</b><br>\n";
					array_push( $FileArray, substr($fileName, 1) );
					$LineArray = file( $SourceDir.$fileName );
					flush();

					for ($rgl = 0; $rgl<sizeof($LineArray); $rgl++)
					{
						$Line = trim(strtolower($LineArray[$rgl]));

						if (($ReplaceJS) && substr($Line, 0, 9)=="function " ) // Search for Function declaration
						{
						// we have to find out if function is JavaScript Function or PHP function
						$posEinde = strpos($Line, "(");
						$FunctieNaam = substr(trim($LineArray[$rgl]), 0, $posEinde);
						$FunctieNaam = trim(preg_replace("/function /i", "", $FunctieNaam));
						$FunctieNaam = trim(preg_replace("/\&/i", "", $FunctieNaam));
						if ( empty($JSFuncArray[$FunctieNaam]) and !(in_array($FunctieNaam,$StdExcJSFuncArray))) $JSFuncArray[$FunctieNaam]="F".substr(md5($FunctieNaam), 0,8);
						}

						if ($ReplaceJS) SearchVars( $LineArray[$rgl] ); // *** Search JavaScript Variables
					}
					}
					else
					{
					// it should be PHP file
					echo "<b>+ Scanning File: ".substr($fileName, 1)."</b><br>\n";
					array_push( $FileArray, substr($fileName, 1) );
					$LineArray = file( $SourceDir.$fileName );
					flush();

					for ($rgl = 0; $rgl<sizeof($LineArray); $rgl++)
					{
						$Line = trim(strtolower($LineArray[$rgl]));

						if ( ($ReplaceFunctions || $ReplaceJS) && substr($Line, 0, 9)=="function " ) // Search for Function declaration
						{
							$posEinde = strpos($Line, "(");
							$FunctieNaam = substr(trim($LineArray[$rgl]), 0, $posEinde);
							$FunctieNaam = trim(preg_replace("/function /i", "", $FunctieNaam));
							$FunctieNaam = trim(preg_replace("/\&/i", "", $FunctieNaam));

							if($FunctieNaam == 'doLoad')
								$FunctieNaam = 'doLoad';
							// we have to find out if the function is JavaScript Function or PHP function
							// we do it by checking if function is between '<script' and '</script' tags
							if(findScriptTagInFile($rgl, $LineArray))
							{
								// it is JS function
								if ( empty($JSFuncArray[$FunctieNaam]) and !(in_array($FunctieNaam,$StdExcJSFuncArray)))
								{
									$JSFuncArray[$FunctieNaam]="F".substr(md5($FunctieNaam), 0,8);
								}
							}
							else
							{
								// it is PHP function
								if ( empty($FuncArray[$FunctieNaam]) and !(in_array($FunctieNaam,$UdExcFuncArray))) $FuncArray[$FunctieNaam]="F".substr(md5($FunctieNaam), 0,8);
							}
						}
						else if ( $ReplaceFunctions && preg_match("/^[ \t]*class[ \t]+([0-9a-zA-Z_]+)[ \t\n\r\{]/U", $LineArray[$rgl], $matches )) // Search for Class declaration
						{
							// store class name to the functions array - class name has to be same as constructor name
							$FunctieNaam = $matches[1];
							if ( empty($FuncArray[$FunctieNaam]) and !(in_array($FunctieNaam,$UdExcFuncArray))) $FuncArray[$FunctieNaam]="F".substr(md5($FunctieNaam), 0,8);
							if ( !in_array($FunctieNaam, $ClassArray ) and !(in_array($FunctieNaam,$UdExcFuncArray))) $ClassArray[] = $FunctieNaam;
						}
						elseif ( $ReplaceConstants && preg_match( "/define[ \t(]/i", substr($Line, 0, 7) ) ) // Search for Constant declaration
						{
							$posStart = strpos($Line, "(");
							$posEnd = strpos($Line, ",");
							$ConstantName = substr(trim($LineArray[$rgl]), ($posStart+1), ($posEnd-$posStart-1));
							$ConstantName = preg_replace('/[\"\']/',"",$ConstantName);
							$posDollar = strpos($ConstantName, "$"); // name of constant may not be a variable
							if ( $posDollar === FALSE && $ConstantName != 'SID' )
							{
								// doesn't convert SID constant (PHP4)
								if (!($ConstArray[$ConstantName]) and !(in_array($ConstantName,$UdExcConstArray)))
								{
									$ConstArray[$ConstantName]="C".substr(md5($ConstantName), 0,8);
								}
							}
						}
						if ( $ReplaceVariables || $ReplaceJS) SearchVars( $LineArray[$rgl] ); // *** Search Variables
					}
					}
				}
				else
				{
					// file was excluded, just copy it
					echo "- <font color=blue>Excluded</font>, just copy Filename: ".substr($fileName, 1)."<br>\n";
					copy(  $SourceDir.$fileName,  $TargetDir.$fileName );
				}
			}
			elseif ( $CopyAllFiles )
			{
				echo "- Copy Filename: ".substr($fileName, 1)."<br>\n";
				copy(  $SourceDir.$fileName,  $TargetDir.$fileName );
			}
		}
		else if ( $RecursiveScan && is_dir( $SourceDir.$fileName ) && $FileNaam != "." && $FileNaam != ".." )
		{
			// check if the directory is in UdExcDirArray
			foreach($UdExcDirArrayRegEx as $value)
			{
				// compare directory name with regular expression
				if(preg_match($value, $SourceDir.$fileName))
					$excludeDirectory = TRUE;
			}

			if($excludeDirectory == TRUE)
			{
				echo "<font color=blue>Directory $SourceDir.$fileName excluded, not copied!</font><br>";
			}
			else
			{

				if(!is_dir($TargetDir.$fileName))
				{
					if ( @mkdir( $TargetDir.$fileName, 0707 ) ) echo 'Creating Directory : '.$TargetDir.$fileName.'.<br>';
					else echo '- Creating Directory : '.$TargetDir.$fileName.' <FONT COLOR=orange>Warning: Creation failed.</b></FONT><br>';
				}

				ScanSourceFiles( $fileName );
			}
		}
	}
	$dir->close();
}

function ShowArrays() {
	global $FuncArray, $VarArray, $JSVarArray, $JSFuncArray, $ConstArray, $FileArray, $UdExcVarArray, $UdExcVarArrayWild;

	echo	'<br>&nbsp;<br><hr color="#000000" height=1 noshade><h3>Replaced elements :</h3>';

	DisplayArray( $FuncArray, "Found functions or classes that will be replaced", $BgColor="FFF0D0");
	DisplayArray( $ConstArray, "Found constants that will be replaced", $BgColor="8DCFF4");
	$VarsArr = $VarArray;
	ksort( $VarsArr );
	$JSVarsArr = $JSVarArray;
	ksort( $JSVarsArr );

	DisplayArray( $VarsArr, "Found variables that will be replaced", $BgColor="89CA9D");
	DisplayArray( $JSFuncArray, "Found JavaScript functions that will be replaced", $BgColor="89CA00");
	DisplayArray( $JSVarsArr, "Found JavaScript variables that will be replaced", $BgColor="89CA00");
	DisplayArray( $UdExcVarArray, "User Defined Exclude Variables", $BgColor="BFBFBF");
	DisplayArray( $FileArray, "Scanned Files", $BgColor="FA8B68");

	echo	'<br>&nbsp;<br><hr color="#000000" height=1 noshade><h3>Number of userdefined elements to be replaced :</h3>'.
				'Functions: '.sizeof( $FuncArray ).'<br>'.
				'Variables: '.sizeof( $VarArray ).'<br>'.
				'JavaScript Variables: '.sizeof( $JSVarArray ).'<br>'.
				'Constants: '.sizeof( $ConstArray ).'<br>'.
				'<br>Scanned Files: '.sizeof( $FileArray ).'<br>'.
				'&nbsp;<br>';
}


function WriteTargetFiles() {
	global $FilesToReplaceArray, $FileArray, $StartTime, $TotalFileSizeRead, $TotalFileSizeWrite;
	global $ReplaceNewer, $SourceDir, $TargetDir;

	echo	'<h3>Check and Replacing file :</h3>'.
				'<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=3><TR>';

	$count = 0;

	foreach( $FileArray as $Key => $FileName)
	{
		$count++;
		$ReplaceFile = TRUE;

		if ( $ReplaceNewer ) {
			$FileRead = $SourceDir."/".$FileName;
			$FileWrite = $TargetDir."/".$FileName;
			if (file_exists($FileWrite)) { // *** CHECK IF SOURCEFILE IS NEWER THAN TARGETFILE
				$FileStats = stat($FileWrite);
				$FileWriteDate = $FileStats[9];
				$FileStats = stat($FileRead);
				$FileReadDate = $FileStats[9];
				if ( $FileReadDate <= $FileWriteDate ) $ReplaceFile = FALSE;
			}
		}

		echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=3><TR>';
		echo '<TR><TD>'.$count.' - '.$FileName.'</TD><TD>';
		if ( $ReplaceFile ) {
			$FileStartTime = time();
			echo ': <FONT COLOR=red>Replaced</FONT>';
			ReplaceThem($FileName);
			echo ' - Elapsed Time: '.(time()-$FileStartTime).' sec.';
		}	else echo ': <FONT COLOR=green>Not replaced</FONT> (sourcefile older than targetfile).';
		echo '</TD></TR></TABLE>';
		flush();
	}

	echo '&nbsp;<br>'.
		'&nbsp;<br><hr color="#000000" height=1 noshade><h3>Stats :</h3>'.
		'Start Time: '.$StartTime.'<br>'.
		'Finish Time: '.time().'<br>'.
		'<b>Elapsed Time: '.(time()-$StartTime).' sec</b><br>'.
		'&nbsp;<br>'.
		'<b>Total FileSize of parsed Files: '.$TotalFileSizeRead.' Bytes<br>'.
		'Total FileSize of written Files: '.$TotalFileSizeWrite.' Bytes</b><br>';
}

// ** FUNCTIONS **

function SearchVars($Line)
{
	global $VarArray, $StdExcVarArray, $StdExcKeyArray, $UdExcVarArray, $UdExcVarArrayWild, $UdExcVarArrayDliw, $ObjectVarArray, $JSVarArray, $StdObjRetFunctionsArray;
	global $MinimumReplaceableVarLen;	// by nux

	// special handling for functions returning objects
	foreach($StdObjRetFunctionsArray as $Key => $Value )
	{
		if ( preg_match('/\$([0-9a-zA-Z_]+)[ \t]*\=[ \t]*'.$Value.'/', $Line, $matches )) // Search for variables, that are objects
		{
			// store class name to the functions array - class name has to be the same as constructor name
			$ObjectVariable = $matches[1];
			if ( !in_array($ObjectVariable, $ObjectVarArray) ) $ObjectVarArray[] = $ObjectVariable;
			$ObjectVariableEncoded ='V'.substr(md5($VarName), 0,8);
			if ( !in_array($ObjectVariableEncoded, $ObjectVarArray) ) $ObjectVarArray[] = $ObjectVariableEncoded;
		}
	}


	// search in javascript code


	preg_match_all('/var[ \t]+([0-9a-zA-Z_]+)[ \t]*[\=;]+/', $Line, $matches);
//	preg_match_all('/var(?:[ \t]+|[ \t\,\=a-zA-Z0-9_]+[ \t,])([a-zA-Z0-9_]+)[ \t]*[\=\;\,]/', $Line, $matches);

	foreach($matches[1] as $mkey)
	{
		$orig = $mkey;
		$VarName = $orig;

		if (strlen($VarName)>=$MinimumReplaceableVarLen && !$JSVarArray[$VarName] && !(in_array($VarName,$StdExcVarArray)) && !(in_array($VarName,$UdExcVarArray)))
		{
			// check in Wildcards Array
			foreach( $UdExcVarArrayWild as $Key => $Value )
			{
				if (substr($VarName, 0, strlen($Value)) == $Value )
				{
					echo 'Variable with name '.$VarName.' added to $UdExcVarArray.<br>';
					array_push( $UdExcVarArray, $VarName ); // add to excluded Variables array
				}
			}

			// SB check in Dliwcards Array (the wild part's on the front)
			foreach( $UdExcVarArrayDliw as $Key => $Value )
			{
				if (substr($VarName, 0 - strlen( $Value ) ) == $Value )
				{
					echo 'Variable with name '.$VarName.' added to $UdExcVarArray.<br>';
					array_push( $UdExcVarArray, $VarName ); // add to excluded Variables array
				}
			}

			if (!(in_array($VarName,$UdExcVarArray)))	// check again in Excluded Variables Array
				$JSVarArray[$VarName]= 'V'.substr(md5($VarName), 0,8);
		}
	}


	while (preg_match('/\$([0-9a-zA-Z_]+)/', $Line, $regs))
	{

		$VarName = $regs[1];
		if (strlen($VarName)>=$MinimumReplaceableVarLen && !$VarArray[$VarName] && !(in_array($VarName,$StdExcVarArray)) && !(in_array($VarName,$UdExcVarArray)))
		{
			// check in Wildcards Array
			foreach( $UdExcVarArrayWild as $Key => $Value )
			{
				if (substr($VarName, 0, strlen($Value)) == $Value )
				{
					echo 'Variable with name '.$VarName.' added to $UdExcVarArray.<br>';
										array_push( $UdExcVarArray, $VarName ); // add to excluded Variables array
				}
			}

			// SB check in Dliwcards Array (the wild part's on the front)
			foreach( $UdExcVarArrayDliw as $Key => $Value )
			{
				if (substr($VarName, 0 - strlen( $Value ) ) == $Value )
				{
					echo 'Variable with name '.$VarName.' added to $UdExcVarArray.<br>';
					array_push( $UdExcVarArray, $VarName ); // add to excluded Variables array
								}
						}

						if (!(in_array($VarName,$UdExcVarArray))) // check again in Excluded Variables Array
				$VarArray[$VarName]= 'V'.substr(md5($VarName), 0,8);
				}

				$Line = substr($Line, ( strpos($Line,'$') + 1 ) );
	}
}

class CommentHandler
{
	var $comments=array();
	var $keep_first=0;
	var $found=0;
	var $replaced=0;

	//-------------------------------------------
	// public
	//-------------------------------------------

	//initialise class, tell it how many comments
	//you wish to preserve
	function CommentHandler($keep_first)
	{
		$this->keep_first=$keep_first;
	}

	//tell it how many comments
	//you wish to preserve
	function SetKeepFirst($keep_first)
	{
		$this->keep_first=$keep_first;
	}

	//remove comments from string, replacing the first
	//n comments with placeholders
	function RemoveComments(&$contents)
	{
		global $StdReplaceComments;

		$this->comments=array();
		$this->found=0;
		$this->replaced=0;

		//because we use multiple regexps to spot the comments
		//we can't be sure which ones come first, so we replace
		//each comment with a placeholder. During the
		//RestoreComments phase, we *can* know which comments are
		//first, and can decide whether or not to restore the original

		if(in_array('//', $StdReplaceComments))
		{
			// REMOVE COMMENTS //, EXCEPT '//-->'
			$contents = preg_replace( "/[ \t\n]+(\/\/)(?![ \t]*-->)[^\n]*/me",
//			$contents = preg_replace( "/(\/\/)(?![ \t]*-->)[^\n]*/me",
				"\$this->StoreComment('\\0')", $contents);
		}
		if(in_array('#', $StdReplaceComments))
		{
			// REMOVE COMMENTS #
			$contents = preg_replace( "/[ \t\n]+(\#)[^\n]*/sme",
							"\$this->StoreComment('\\0')", $contents);
		}
		// REMOVE COMMENTS /* ... */
		if(in_array('/**/', $StdReplaceComments))
		{
			$contents = preg_replace( '/\/\*.*?\*\/[ \n]*/sme',
							"\$this->StoreComment('\\0')", $contents);
		}
		//
		// by nux
		// REMOVE COMMENTS <!-- ... --> (currently one line only)
		if(in_array('HTML', $StdReplaceComments))
		{
			$contents = preg_replace( '/<!--.*-->/se',
							"\$this->StoreComment('\\0')", $contents);
		}
		// by nux: end
		//
	}

	//restore the first n comments
	function RestoreComments(&$contents)
	{
		$contents = preg_replace( '/___POBS_COMMENT_(\d+)/e',
			"\$this->FetchComment('\\1')", $contents);

	}



	//-------------------------------------------
	// private
	//-------------------------------------------

	function StoreComment($comment)
	{
		//store the comment and return a placeholder
		//this allows us to preserve the format of
		//comments when POBS removes white space
		$this->comments[$this->found]=$comment;

		$replacement = '';

		if( ($pos = strpos($comment,'?>')) !== false && strpos($comment,'<?') === false)
		{
			$comment = substr($comment, 0, $pos);
			$replacement = '?>';
		}
		// it it is // type of comment, change it to /* */ type
		if($comment[0]=='/' && $comment[1]=='/')
		{
			$comment[1] = '*';
			$comment .= '*/ ';
		}

		$this->comments[$this->found]=$comment;
		// orig:
		$replacement="___POBS_COMMENT_".$this->found." ".$replacement;
		// by nux (white space for cases like $abc//comment)
		//$replacement=" ___POBS_COMMENT_".$this->found." ".$replacement;

		$this->found++;

		return $replacement;
	}

	function FetchComment($idx)
	{
		if ($this->replaced<$this->keep_first)
		{
			$this->replaced++;
			return $this->comments[$idx];
		}
		return "";
	}
}

/*
	we have to make sure that lines will be not longer than some constant,
	otherwise it can make problems with PHP
*/
function Concatenate($contents, $MaxCharsInLine = 100)
{
  $linelength = 0;
  $replaced = 0;

  // get rid of useless lines first
  $contents = preg_replace( "/___POBS_NEWLINE___[ \t]*___POBS_NEWLINE___/m", "___POBS_NEWLINE___", $contents);

  while(($pos = strpos($contents, "___POBS_NEWLINE___")) !== false)
  {
	if($pos-$linelength<$MaxCharsInLine)
	{

		// replace with space
		$head = substr($contents, 0, $pos);
		$tail = substr($contents, $pos+18);
		$contents = $head.' '.$tail;
		$replaced++;
	}
	else
	{
		// replace with newline
		$head = substr($contents, 0, $pos);
		$tail = substr($contents, $pos+18);
		$contents = $head."\n".$tail;
		$linelength = $pos;
		$replaced++;
	}
  }

  // get rid of multiple spaces
  $contents = preg_replace( "/[ \t]+/", ' ', $contents);

  return $contents;
}


function ReplaceThem($FileName)
{
	global $VarArray, $JSVarArray, $JSFuncArray, $FuncArray, $JSFileExtArray, $FileExtArray, $ConstArray, $SourceDir, $TargetDir, $ObjectVarArray, $ReplaceVariables, $ReplaceJS;
	global $ReplaceConstants, $ReplaceFunctions, $RemoveIndents, $RemoveComments, $ConcatenateLines, $StdExcKeyArray, $StdExcJSVarArray, $StdExcJSFuncArray;
	global $KeptCommentCount, $NewlinesReplaced, $_POBSMaxRepeats;
	global $LineExclude, $ExcludedLines;
	global $CopyrightText, $CopyrightPHP, $CopyrightJS;
	global $ReplaceVarsInTabsAndCookies, $ReplaceVarsInNameField;	// by nux

	$FileRead = $SourceDir."/".$FileName;
	$FileWrite = $TargetDir."/".$FileName;

	// check if file has the proper suffix
	$extpos = strrpos($FileName, ".");

	if($extpos>0)
		$Suffix = substr($FileName,$extpos+1);
	else
		$Suffix = md5(rand()); // generate some non existing extension

	$NewlinesReplaced = 0;

	$FdRead = fopen( $FileRead, 'rb' );

	$contents_arr = file($FileRead);
	$contents = '';
	$LinesExcluded = 0;
	$ExcludedLines = array();

	// take care of lines that should be excluded from obfuscation
	if($LineExclude == '')
		$contents = fread( $FdRead, filesize( $FileRead ) );
	else
	{
		for($i=0; $i<count($contents_arr); $i++)
		{
		// check if line should be excluded
		if(strpos($contents_arr[$i], $LineExclude) !== false)
		{
			$ExcludedLines[$LinesExcluded] = $contents_arr[$i];
			$contents .= '__POBS_@LINE@_EXCLUDED_'.$LinesExcluded;
			$LinesExcluded++;
		}
		else
			$contents .= $contents_arr[$i];
		}
	}

//	$contents = fread( $FdRead, filesize( $FileRead ) );
	$GLOBALS['TotalFileSizeRead'] += filesize( $FileRead );
	echo ' - Size:'.filesize( $FileRead );
	fclose( $FdRead );

	$ch=new CommentHandler($KeptCommentCount);

	// we have to process comments in any case
	$ch->RemoveComments($contents);

	$contents = preg_replace( "/[\r\n]{2,}/m", "\n", $contents ); // REMOVE EMPTY LINES AND DOS "\r\n"
	$contents = preg_replace( "/[ \t]{2,}/m", ' ', $contents ); // REMOVE TOO MANY SPACE OR TABS (but also in output text...)

	if ($RemoveIndents)
	{
		// by nux
		$contents = preg_replace( "/[ \t]+\n/m", "\n", $contents);  // REMOVE TABS and SPACES at the end of lines
		$contents = preg_replace( "/\n[ \t]+/m", "\n", $contents);  // REMOVE INDENT TABS and SPACES
		// ryzykowane - można wpraść w komentarz
		//$contents = preg_replace( "/[ \t\n]+([{}])[ \t\n]+/m", " $1 ", $contents);  // REMOVE lines around block of code
		$contents = preg_replace( "/([{}])[ \t\n]+/m", "$1 ", $contents);  // REMOVE lines after or before block of code
		// by nux
		$contents = preg_replace( "/[\n][\t ]+(?=[\n])/m", "", $contents ); // remove semi EMPTY LINES
		// orig
		//$contents = preg_replace( "/([;\}]{1})\n[ \t]*/m", "\\1\n", $contents);  // REMOVE INDENT TABS and SPACES
	}

	if ( strpos( $contents, '->') || strpos( $contents, '::') )
		$ReplaceObjects = TRUE;
	else
		$ReplaceObjects = FALSE;

	if ( preg_match('/class/i', $contents) )
	{
		$ReplaceClasses = TRUE;
	}
	else
		$ReplaceClasses = FALSE;

	// *** REPLACE FUNCTIONNAMES
	if ( $ReplaceFunctions)
	{
		foreach( $FuncArray as $Key => $Value )
		{
			if ( strlen($Key) && strpos(strtolower($contents), strtolower($Key)) !== FALSE ) // to speed up things, check if variable name is, in any way, present in the file
			{
				$contents = preg_replace("/([^a-zA-Z0-9_]+)".$Key."[ \t]*\\(/i","\\1".$Value."(", $contents); //werkt

				if ($ReplaceObjects)
				{
					$contents = preg_replace('/([^a-zA-Z0-9_]+)('.$Key.')::/','\1'.$Value.'::', $contents); // objects
				}
				if ($ReplaceClasses)
				{
					$contents = preg_replace('/([^0-9a-zA-Z_])class[ \t]*('.$Key.')([^0-9a-zA-Z_])/i','\1class '.$Value.'\3', $contents); // class declaration
				}

				$contents = preg_replace('/([^0-9a-zA-Z_])extends[ \t]*('.$Key.')([^0-9a-zA-Z_])/i','\1extends '.$Value.'\3', $contents); // extended or derived class declaration
				$contents = preg_replace('/([^0-9a-zA-Z_])new[ \t]+('.$Key.')([^0-9a-zA-Z_(])/i','\1new '.$Value.'\3', $contents); // extended or derived class declaration
			}
		}
	}

	// *** REPLACE VARIABLENAMES
	if ( $ReplaceVariables )
	{
		if ($ReplaceVarsInNameField && stristr($contents, 'name=' ))
		{
			$ReplaceFieldnames = TRUE;
		}
		else
		{
			$ReplaceFieldnames = FALSE;
		}

		foreach( $VarArray as $Key => $Value )
		{
			if ( strlen($Key) && strpos(strtolower($contents), strtolower($Key)) !== FALSE ) // to speed up things, check if variable name is, in any way, present in the file
			{
				//orig: $contents = preg_replace('/([$&?{])('.$Key.')([^0-9a-zA-Z_])/m','\1'.$Value.'\3', $contents);  // normal variables and parameters
				//by nux:
				$contents = preg_replace('/([$])('.$Key.')([^0-9a-zA-Z_]|\n)/m','\1'.$Value.'\3', $contents);  // normal variables and parameters
				$contents = preg_replace('/([$])('.$Key.')(___POBS_COMMENT_)/m','\1'.$Value.'\3', $contents);  // normal variables and parameters
				//if ($Key == 'subkat') echo "<pre>key_var:$Key At ".__LINE__."\n".htmlspecialchars($contents)."</pre>"; // debug
				//
				
				$contents = preg_replace('/(&amp;)('.$Key.')([^0-9a-zA-Z_])/m','\1'.$Value.'\3', $contents);  // variable in <A> tag with &amp;
				//if ($Key == 'subkat') echo "<pre>key_var:$Key At ".__LINE__."\n".htmlspecialchars($contents)."</pre>"; // debug

				// process javascript code
				preg_match_all('/\<SCRIPT.*>(.*)<\/SCRIPT>/Uis',$contents,$matches);  // in case there are more <SCRIPT> sections within one file

				foreach($matches[1] as $mkey)
				{
					$tcount++;
					$orig = $mkey;

					$replaced = $orig;

					if ( !in_array($Key, $StdExcJSVarArray) )
					{
						$replaced = preg_replace('/(.*?[ \.])('.$Key.')([ \t\.\=\!].*)/is','\1'.$Value.'\3', $orig);  // javascript variables
						// $replaced = preg_replace('/(\=[ \t]*)('.$Key.')([ \t]*[\;\.])/is','\1'.$Value.'\3', $replaced);  // javascript variables

						// $replaced = preg_replace('/(.*var[ \t\,a-zA-Z0-9_]+)('.$Key.')([ \t]*[\=\;\,])/Uis','\1'.$Value.'\3', $replaced);  // javascript var defines (var XXX;)
						// $replaced = preg_replace('/([^0-9a-zA-Z_])('.$Key.')([ \t]*[\+\-\*\/\[\;\,\.\)])/is','\1'.$Value.'\3', $replaced);  // javascript arrays (xxx[])	// \= MISSING
						// $replaced = preg_replace('/((?:\[|\[[ \t\'\"\+\-\*\/a-zA-Z0-9_]*[^a-zA-Z0-9_]))('.$Key.')((?:\]|[^a-zA-Z0-9_][ \t\'\"\+\-\*\/a-zA-Z0-9_]*\]))/is','\1'.$Value.'\3', $replaced);  // javascript arrays ([xxx])

						// $replaced = preg_replace('/((?:\(|\([^\)]*[ \t\,\+\-\.\*\/\!\<\>\=]))('.$Key.')((?:\)|[ \t\,\+\-\*\/\!\=\<\>][^\)]*\)))/Uis','\1'.$Value.'\3', $replaced);  // javascript function parameters

					}

					if($orig!==$replaced)
					{
						$contents = str_replace($orig, $replaced, $contents);
					}
				}
				//if ($Key == 'subkat') echo "<pre>key_var:$Key At ".__LINE__."\n".htmlspecialchars($contents)."</pre>"; // debug

				// replace javascript code in onXXX event handlers
				if (!in_array($Key, $StdExcJSVarArray))
				{
					$tcount = 0;
					while($tcount<$_POBSMaxRepeats && preg_match('/(\<[^\?][^\>]*on[0-9a-zA-Z_]+[ \t]*\=[ \t]*[\"\']{0,1}[^\>]*[^a-zA-Z0-9_]+)('.$Key.')([^a-zA-Z0-9_]+)/Ui', $contents))
					{
						$contents = preg_replace('/(\<[^\?][^\>]*on[0-9a-zA-Z_]+[ \t]*\=[ \t]*[\"\']{0,1}[^\>]*[^a-zA-Z0-9_]+)('.$Key.')([^a-zA-Z0-9_]+)/Ui','\1'.$Value.'\3', $contents);  // javascript event function parameters
						$tcount++;
					}
/*
					if(preg_match('/(\<[^\?][^\>]*on[0-9a-zA-Z_]+[ \t]*\=[ \t]*[\"\']{0,1}[^\>]*[^a-zA-Z0-9_]+)('.$Key.')([^a-zA-Z0-9_]+)/Ui', $contents, $matches))
					{
						echo HTMLSpecialChars("<BR> matches=".$matches[1].",".$Key.",".$matches[3].",<BR>");
					}
*/
				}
				//if ($Key == 'subkat') echo "<pre>key_var:$Key At ".__LINE__."\n".htmlspecialchars($contents)."</pre>"; // debug

				if ($ReplaceVarsInTabsAndCookies)	// by nux
				{
					$contents = preg_replace('/\$(GLOBALS|HTTP_COOKIE_VARS|HTTP_POST_VARS|HTTP_GET_VARS|HTTP_SESSION_VARS|_REQUEST|_FILES|_SERVER|_ENV|_POST|_COOKIE|_GET|_SESSION)([ \t]*)\[(["\' \t]*)'.$Key.'(["\' \t]*)\]/m', '$\1[\3'.$Value.'\4]', $contents ); // var in Tabs
					$contents = preg_replace('/(setcookie|session_register|session_is_registered|session_unregister)(?:[ \t]*)\(([\\\"\']*)'.$Key.'([\\\"\'\, \t)]*)/i', '\1(\2'.$Value.'\3', $contents ); // cookie or session variables
				}
				//if ($Key == 'subkat') echo "<pre>key_var:$Key At ".__LINE__."\n".htmlspecialchars($contents)."</pre>"; // debug

				if ($ReplaceObjects)
				{
					$contents = preg_replace('/->[ \t]*('.$Key.')(?:!\()/','->'.$Value, $contents); // objects
					$contents = preg_replace('/::[ \t]*('.$Key.')(?:![^0-9a-zA-Z_])/','::'.$Value, $contents); // objects

					// special handling for object variables
					if( preg_match('/\$([0-9a-zA-Z_]+)[ \t]*->[ \t]*('.$Key.')[ \t]*([^0-9a-zA-Z_])/', $contents, $matches) ) // class variables
					{
						// check if variable is not returned from object returning function
						$tempVar = $matches[1];
						if(!in_array($tempVar, $ObjectVarArray) )  // XX->YY : replace YY only if XX is not in $ObjectVarArray
							$contents = preg_replace('/(\$[0-9a-zA-Z_]+)[ \t]*->[ \t]*('.$Key.')[ \t]*([^0-9a-zA-Z_])/','\1->'.$Value.'\3', $contents); // class variables
					}

				}
				//if ($Key == 'subkat') echo "<pre>key_var:$Key At ".__LINE__."\n".htmlspecialchars($contents)."</pre>"; // debug

				if ($ReplaceFieldnames)
					$contents = preg_replace('/([ \t\"\'](?:(?i)name)=[\\\"\' \t]*)'.$Key.'([\\\"\'> \t])/','\1'.$Value.'\2', $contents); // input fields
			}
		}

	}

	// *** REPLACE JavaScript VARIABLENAMES
	if ( $ReplaceJS )
	{
		foreach( $JSVarArray as $Key => $Value )
		{
			if ( strlen($Key) && strpos(strtolower($contents), strtolower($Key)) !== FALSE ) // to speed up things, check if variable name is, in any way, present in the file
			{
				if (in_array($Suffix, $JSFileExtArray))
				{
				// for JS files dont need to search for script tags
				$orig = $contents;

				$replaced = $orig;

				if ( !in_array($Key, $StdExcJSVarArray) )
				{

					$replaced = preg_replace('/(.*?[ \.])('.$Key.')([ \t\.\=\!].*)/is','\1'.$Value.'\3', $orig);	// javascript variables
					$replaced = preg_replace('/(\=[ \t]*)('.$Key.')([ \t]*[\;\.])/is','\1'.$Value.'\3', $replaced);	// javascript variables

					// $replaced = preg_replace('/(.*var[ \t\,a-zA-Z0-9_]+)('.$Key.')([ \t]*[\=\;\,])/Uis','\1'.$Value.'\3', $replaced);	// javascript var defines (var XXX;)
					$replaced = preg_replace('/(.*var(?:[ \t]+|[ \t\,\=a-zA-Z0-9_]+[^a-zA-Z0-9_]))('.$Key.')([ \t]*[\=\;\,])/Uis','\1'.$Value.'\3', $replaced);	// javascript var defines (var XXX;)
					$replaced = preg_replace('/([^0-9a-zA-Z_])('.$Key.')([ \t]*[\+\-\*\/\[\;\,\.\\=)])/is','\1'.$Value.'\3', $replaced);  // javascript arrays (xxx[])	// \= MISSING
					$replaced = preg_replace('/((?:\[|\[[ \t\'\"\+\-\*\/a-zA-Z0-9_]*[^a-zA-Z0-9_]))('.$Key.')((?:\]|[^a-zA-Z0-9_][ \t\'\"\+\-\*\/a-zA-Z0-9_]*\]))/is','\1'.$Value.'\3', $replaced);  // javascript arrays ([xxx])

					$replaced = preg_replace('/((?:\(|\([^\)]*[ \t\,\+\-\.\*\/\!\<\>\=]))('.$Key.')((?:\)|[ \t\,\+\-\*\/\!\=\<\>][^\)]*\)))/Uis','\1'.$Value.'\3', $replaced);  // javascript function parameters

				}


				if($orig!==$replaced)
					$contents = $replaced;


				// replace javascript code in onXXX event handlers
				if ( !in_array($Key, $StdExcJSVarArray) )
				{
					$tempcount = 0;
					while(preg_match('/(\<[^\?][^\>]*on[0-9a-zA-Z_]+[ \t]*\=[ \t]*[\"\']{0,1}[^\>]*[^a-zA-Z0-9_]+)('.$Key.')([^a-zA-Z0-9_]+)/Ui',$contents) && $tempcount<500)
					{
					$contents = preg_replace('/(\<[^\?][^\>]*on[0-9a-zA-Z_]+[ \t]*\=[ \t]*[\"\']{0,1}[^\>]*[^a-zA-Z0-9_]+)('.$Key.')([^a-zA-Z0-9_]+)/Ui','\1'.$Value.'\3', $contents);  // javascript event function parameters
					$tempcount++;
					}
				}
				}
				else
				{
				// process only code within <script></script> tags
				// process javascript code
				preg_match_all('/\<SCRIPT.*>(.*)<\/SCRIPT>/Uis',$contents,$matches);  // in case there are more <SCRIPT> sections within one file

				foreach($matches[1] as $mkey)
				{
					$tcount++;
					$orig = $mkey;

					$replaced = $orig;

					if ( !in_array($Key, $StdExcJSVarArray) )
					{
						$replaced = preg_replace('/(.*?[ \.])('.$Key.')([ \t\.\=\!].*)/is','\1'.$Value.'\3', $orig);  // javascript variables
						$replaced = preg_replace('/(\=[ \t]*)('.$Key.')([ \t]*[\;\.])/is','\1'.$Value.'\3', $replaced);  // javascript variables

						// $replaced = preg_replace('/(.*var[ \t\,a-zA-Z0-9_]+)('.$Key.')([ \t]*[\=\;\,])/Uis','\1'.$Value.'\3', $replaced);  // javascript var defines (var XXX;)
						$replaced = preg_replace('/(.*var(?:[ \t]+|[ \t\,\=a-zA-Z0-9_]+[^a-zA-Z0-9_]))('.$Key.')([ \t]*[\=\;\,])/Uis','\1'.$Value.'\3', $replaced);  // javascript var defines (var XXX;)
						$replaced = preg_replace('/([^0-9a-zA-Z_])('.$Key.')([ \t]*[\+\-\*\/\[\;\,\.\\=)])/is','\1'.$Value.'\3', $replaced);  // javascript arrays (xxx[])	// \= MISSING
						$replaced = preg_replace('/((?:\[|\[[ \t\'\"\+\-\*\/a-zA-Z0-9_]*[^a-zA-Z0-9_]))('.$Key.')((?:\]|[^a-zA-Z0-9_][ \t\'\"\+\-\*\/a-zA-Z0-9_]*\]))/is','\1'.$Value.'\3', $replaced);  // javascript arrays ([xxx])

						$replaced = preg_replace('/((?:\(|\([^\)]*[ \t\,\+\-\.\*\/\!\<\>\=]))('.$Key.')((?:\)|[ \t\,\+\-\*\/\!\=\<\>][^\)]*\)))/Uis','\1'.$Value.'\3', $replaced);  // javascript function parameters
					}


					if($orig!==$replaced)
					{
					$contents = str_replace($orig, $replaced, $contents);
					}
				}

				// replace javascript code in onXXX event handlers
				if ( !in_array($Key, $StdExcJSVarArray) )
				{
					$tempcount = 0;
					while(preg_match('/(\<[^\?][^\>]*on[0-9a-zA-Z_]+[ \t]*\=[ \t]*[\"\']{0,1}[^\>]*[^a-zA-Z0-9_]+)('.$Key.')([^a-zA-Z0-9_]+)/Ui',$contents) && $tempcount<500)
					{
						$contents = preg_replace('/(\<[^\?][^\>]*on[0-9a-zA-Z_]+[ \t]*\=[ \t]*[\"\']{0,1}[^\>]*[^a-zA-Z0-9_]+)('.$Key.')([^a-zA-Z0-9_]+)/Ui','\1'.$Value.'\3', $contents);  // javascript event function parameters
						$tempcount++;
					}
				}
				}
			}
		}
	}


	// *** REPLACE JavaScript FUNCTIONS
	if ( 1 )
	{
		foreach( $JSFuncArray as $Key => $Value )
		{
			if ( strlen($Key) && strpos(strtolower($contents), strtolower($Key)) !== FALSE ) // to speed up things, check if variable name is, in any way, present in the file
			{
				if (in_array($Suffix, $JSFileExtArray))
				{
					// for JS files dont need to search for script tags
					// process javascript code
					if ( !in_array($Key, $StdExcJSFuncArray) )
					{
						$contents = preg_replace("/([^a-zA-Z0-9_]+)".$Key."[ \t]*\\(/i","\\1".$Value."(", $contents); //werkt

						if ($ReplaceObjects)
						$contents = preg_replace('/('.$Key.')::/',$Value.'::', $contents); // objects

						if ($ReplaceClasses)
						$contents = preg_replace('/([^0-9a-zA-Z_])class[ \t]*('.$Key.')([^0-9a-zA-Z_])/i','\1class '.$Value.'\3', $contents); // class declaration

						$contents = preg_replace('/([^0-9a-zA-Z_])extends[ \t]*('.$Key.')([^0-9a-zA-Z_])/i','\1extends '.$Value.'\3', $contents); // extended or derived class declaration
						$contents = preg_replace('/([^0-9a-zA-Z_])new[ \t]+('.$Key.')([^0-9a-zA-Z_(])/i','\1new '.$Value.'\3', $contents); // extended or derived class declaration
					}

					// replace javascript code in onXXX event handlers
					if ( !in_array($Key, $StdExcJSFuncArray) )
					{
						$tempcount = 0;
						while(preg_match('/(\<[^\?][^\>]*on[0-9a-zA-Z_]+[ \t]*\=[ \t]*[\"\']{0,1}[^\>]*[^a-zA-Z0-9_]+)('.$Key.')([^a-zA-Z0-9_]+)/Ui',$contents) && $tempcount<500)
						{
						$contents = preg_replace('/(\<[^\?][^\>]*on[0-9a-zA-Z_]+[ \t]*\=[ \t]*[\"\']{0,1}[^\>]*[^a-zA-Z0-9_]+)('.$Key.')([^a-zA-Z0-9_]+)/Ui','\1'.$Value.'\3', $contents);  // javascript event function parameters
						$tempcount++;
						}
					}
				}
				else
				{
				// process only code within <script></script> tags
				preg_match_all('/\<SCRIPT.*>(.*)<\/SCRIPT>/Uis',$contents,$matches);  // in case there are more <SCRIPT> sections within one file

				foreach($matches[1] as $mkey)
				{
					$tcount++;
					$orig = $mkey;

					$replaced = $orig;

					if ( !in_array($Key, $StdExcJSFuncArray) )
					{
					$contents = preg_replace("/([^a-zA-Z0-9_]+)".$Key."[ \t]*\\(/i","\\1".$Value."(", $contents); //werkt

					if ($ReplaceObjects)
						$contents = preg_replace('/('.$Key.')::/',$Value.'::', $contents); // objects

					if ($ReplaceClasses)
						$contents = preg_replace('/([^0-9a-zA-Z_])class[ \t]*('.$Key.')([^0-9a-zA-Z_])/i','\1class '.$Value.'\3', $contents); // class declaration

					$contents = preg_replace('/([^0-9a-zA-Z_])extends[ \t]*('.$Key.')([^0-9a-zA-Z_])/i','\1extends '.$Value.'\3', $contents); // extended or derived class declaration
					$contents = preg_replace('/([^0-9a-zA-Z_])new[ \t]+('.$Key.')([^0-9a-zA-Z_(])/i','\1new '.$Value.'\3', $contents); // extended or derived class declaration

					}
				}

				// replace javascript code in onXXX event handlers
				if ( !in_array($Key, $StdExcJSFuncArray) )
				{
					$tempcount = 0;
					while(preg_match('/(\<[^\?][^\>]*on[0-9a-zA-Z_]+[ \t]*\=[ \t]*[\"\']{0,1}[^\>]*[^a-zA-Z0-9_]+)('.$Key.')([^a-zA-Z0-9_]+)/Ui',$contents) && $tempcount<500)
					{
					$contents = preg_replace('/(\<[^\?][^\>]*on[0-9a-zA-Z_]+[ \t]*\=[ \t]*[\"\']{0,1}[^\>]*[^a-zA-Z0-9_]+)('.$Key.')([^a-zA-Z0-9_]+)/Ui','\1'.$Value.'\3', $contents);  // javascript event function parameters
					$tempcount++;
					}
				}

				}
			}
		}
	}


	// *** REPLACE CONSTANTNAMES
	if ( $ReplaceConstants )
	{
		foreach( $ConstArray as $Key => $Value )
		{
			if ( strlen($Key) && strpos(strtolower($contents), strtolower($Key)) !== FALSE ) // to speed up things, check if variable name is, in any way, present in the file
			{
				$contents = preg_replace('/([^a-zA-Z0-9_\$])('.$Key.')([^a-zA-Z0-9_])/', '\1'.$Value.'\3', $contents );
				// special handling for arrays like HTTP_SERVER_VARS
				foreach($StdExcKeyArray as $KeyArray)
				{
					// check, if currently replaced variable is in this field
					if(preg_match('/(\$'.$KeyArray.'\[[ \t]*[\\\'\"]+)'.$Value.'([\\\'\"]+[ \t]*\])/', $contents))
					{
						// restore previous value of the key
						$contents = preg_replace('/(\$'.$KeyArray.'\[[ \t]*[\\\'\"]+)'.$Value.'([\\\'\"]+[ \t]*\])/', '\1'.$Key.'\2', $contents );
					}
				}
			}
		}
	}

	if(!$RemoveComments)
	{
		$ch->SetKeepFirst(99999);
	}

	//restore the first $KeptCommentCount comments
	$ch->RestoreComments($contents);


	if ($ConcatenateLines)
	{
		$contents = preg_replace( '/\n/sme', "___POBS_NEWLINE___", $contents);
		$contents = Concatenate($contents);
	}

	// replace placeholders with excluded lines
	if($LineExclude != '' && count($ExcludedLines)>0)
	{
		for($i=0; $i<count($ExcludedLines); $i++)
		{
		$contents = str_replace('__POBS_@LINE@_EXCLUDED_'.$i, $ExcludedLines[$i], $contents);
		}
	}

	// 
	// by nux: remove empty and semi-empty lines after removing comments
	if ($RemoveIndents)
	{
		$contents = preg_replace( "/[\r\n][\t ]+(?=[\r\n])/m", "", $contents ); // remove semi EMPTY LINES
		$contents = preg_replace( "/[\r\n]{2,}/m", "\n", $contents ); // remove EMPTY LINES
	}
	//
	//

	// now add copyright text
	if($CopyrightJS == true && in_array($Suffix, $JSFileExtArray))
	{
		$contents = $CopyrightText."\n".$contents;
	}
	else if($CopyrightPHP == true && in_array($Suffix, $FileExtArray))
	{
		$contents = "<?\n$CopyrightText\n?>\n".$contents;
	}

	$FdWrite = fopen( $FileWrite, 'w' );
	$NumberOfChars = fwrite( $FdWrite, $contents );
	fclose( $FdWrite );
	clearstatcache();
	$GLOBALS['TotalFileSizeWrite'] += filesize( $FileWrite );
}

function DisplayArray($ArrayName, $HeaderText="", $BgColor="FFF0D0")
{
	global $TableColumns;

	$sizeOf = sizeOf( $ArrayName );

	echo	'<br>'."\n".
				'<TABLE WIDTH="100%" BORDER=0 CELLSPACING=1 CELLPADDING=3 BGCOLOR="#000000"><TR><TD><FONT COLOR=#FFFFFF><b>'.$HeaderText.'</b></FONT></TD></TR></TABLE>';
	if ( $sizeOf )
	{
		if ( $sizeOf > $TableColumns ) $width = $TableColumns; else $width = $sizeOf;
		$width = 100 / $width;

		echo '<TABLE WIDTH="100%" BORDER=0 CELLSPACING=1 CELLPADDING=3 BGCOLOR="#000000"><TR>';

		$Cnt = 0;
		$Line = 0;
		foreach( $ArrayName as $Key => $Value )
		{
			$Cnt++;
			echo '<TD WIDTH="'.$width.'%" BGCOLOR="#'.$BgColor.'"><b>'.$Key.'</b><br>'.$Value.'</TD>';
			if ( ( $Cnt % $TableColumns) == 0  && ( $Cnt != $sizeOf ) )
			{
				echo '</TR>';
				echo '<TR>';
				$Line ++;
			}
		}
		$i = $Cnt % $TableColumns;
		if ( $i && $Line ) for ( ; $i < $TableColumns; $i++ ) echo '<TD BGCOLOR=#'.$BgColor.'>&nbsp;</TD>';

		echo '</TR></TABLE>'."\n";
		flush();
	}
		else echo '<i>No match or no replace requested</i><br>';
}

function CheckSafeMode()
{
	global $TimeOut;

	$SafeMode = strtolower(get_cfg_var("safe_mode"));
	if (!$SafeMode) set_time_limit($TimeOut);
		else echo "<b><FONT COLOR=orange>Warning: SafeMode is on. Can not set timeout.</b></FONT><br>";
}

?>

</BODY>
</HTML>
