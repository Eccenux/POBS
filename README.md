POBS
====

POBS is a PHP Obfuscator. This means it "compiles" your PHP files by making them unreadable to a human.

How unreadable the output is? You can see for yourself in the `example`.

**Note!** This is not meant to be bullet proof. The output code will not be re-usable for most people, but dedicated user will always be able to guess what you are doing in most single functions. In other words – re-using output code is hard, but not impossible.

Installation
-------------------

Installing POBS is as easy as I could think of. Just unzip the downloaded file and put it a directory that is located under your web server. POBS is a collection of files in just 1 directory.

Before executing POBS you are advised to read the manual that is provided in the `doc` folder. Also check the settings in `pobs-ini.inc` and adjust them to suit your needs. When you run POBS for the first time you should at least adjust the `$SourceDir` and the `$TargetDir` variables.

If you have a large amount of PHP source to be POBSed, check your `php.ini` and see whether it runs in "Safe mode" (also, POBS warns for it). If it does, POBS can not adjust the timeout setting as indicated in `pobs-ini.inc` and the processing might be terminated before POBS has finished the replacement of all your PHP code files. You might need to restart your web server after adjusting the `php.ini` file.

After having checked everything and having adjusted the settings in `pobs-ini.inc` you point your browser to `pobs.php` and press `<Enter>`.

Naming conventions
-------------------
In some occasions POBS might change too much. Mostly this will happen if you mix JavaScript with PHP and happen to have e.g. PHP post variable named the same as JavaScript variable. This will result in a non-working code.

You can of course ignore this and only add exceptions when it is necessary.

But to avoid this problem prematurely you should use prefixes in new projects for your PHP variables, functions and such. You can for example use below conventions.

### Variables ###
* standard variables: "pv_" ("$pv_someValue", instead of "$someValue") 
* GET/POST: "rv_" ("rv_kid", instead of "kid")

### Functions and classes ###
* functions and methods: "pf_" (`pf_someFunction`, instead of `someFunction`)
* classes: "pc_" (`pc_SomeClass`, instead of `SomeClass`)

Changes log
---------------------

### 0.99.1 ###

* Additional configuration variables:
```php
$MinimumReplaceableVarLen = 4;	// all below this will not be replaced
$ReplaceVarsInTabsAndCookies = false;
$ReplaceVarsInNameField = false;
```
* REMOVE COMMENTS <!-- ... --> (currently one line only)
* Remove empty and semi-empty lines after removing comments

### 0.99.2 ###

Additional configuration variable: `$CopyrightTextFromIni`.

### 0.99.3 ###
- removed case insensitive replace for most regexpes (PHP and JS are both mostly case sensitive)
- auto saving log file (html output)
- nux: do not show numbers in log (better for diffs)

### 0.99.4 ###

txt log file

### 0.99.5 ###

* Additional configuration:
```php
// if true then just run dummy parsing (will not change any files nor create directories)
$DoNotCopyOrCreateAnything = false;
```
* Remove elapsed time for individual files.
* Fixed ReplaceJS form option.


### 0.99.6 ###

- allow running with GET (with default options)
- passing some extra options when running with GET
- mild security: allow source and target paths to be relative *only* to current directory
- allow changing copyright year in default text taken form copyright ini file.

Example URL:

	pobs.php?getEnabled=lakslkals&inDir=in&outDir=out/test&NewCopyrightYear=2014

New configuration:
```php
$RunWithGetSecret = 'lakslkals';	// "secret" string to be passed with GET request

// things not set explicitly otherwise
$RunWithGetDefaults = array (
	'ReplaceClasses' => '1',
	'ReplaceFunctions' => '1',
	'ReplaceVariables' => '1',
	'RemoveComments' => '1',
	'KeptCommentCount' => '0',
	'RemoveIndents' => '1',
	'ReplaceNewer' => 'on',
	'RecursiveScan' => 'on',
	'CopyAllFiles' => 'on',
	'CopyrightPHP' => '1',
	'CopyrightJS' => '1',
	'OK' => 'Start processing',
);

// allow source and target paths to be relative only to current dir (or dir given below)
$AllowOnlySubDirs = true;
$SourceTargetDirsBase = "./io/";	// use "./" for base in pobs dir

// copyright replacement config (works only if NewCopyrightYear is passed with GET or POST)
$CopyrightYearPattern= "#(Copyright [0-9]+\-)([0-9]+)#";
$CopyrightYearReplacement= "\${1}%NewYear%";	// @note must containt "%NewYear%" for the replacement to work
```

### 0.99.7 ###

* `protected`, `abstract`... and other PHP 5 classes and methods obfuscation.
* default timezone