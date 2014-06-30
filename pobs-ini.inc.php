<?
    /*
    POBS - INI FILE
    
    This file controls many aspects of POBS' behavior. POBS will try 
    to include it and looks for it in the same directory as pobs.php is
    located. Instead of a real ini file it's just a plain PHP script 
    file. It is assumed you are familiar with PHP code syntax if you 
    use POBS, so configuring this file won't give you too much trouble
    */
    
    $SourceDir = "e:/work/pobs/test/old";
    $TargetDir = "e:/work/pobs/test/new";

    $FontSize = 8;
    $TableColumns = 5;
    $TimeOut = 5000;
    $MaxFiles = 2000;       // Maximum of processed files
    $_POBSMaxRepeats = 100; // Maximum cycle repeats - protects against unlimited cycles in case
                            // of condition error

    // only files with defined extensions will be processed
    // if you want to process also files without any suffix, add "." to the array
    // example: $FileExtArray = array("php","php3","php4","php5","inc",".");
    $FileExtArray = array("php", "ee");
    
    // if JavaScript replacement is checked, then files with extensions 
    // specified below will be processed as well, and they will be considered 
    // to contain pure JavaScript code (no PHP tags)
    // this is useful if you have your JavaScript functions stored in an external files
    $JSFileExtArray = array("js");

    $StdExcFileArray = array('Dummy Entry');


    $LineExclude = '';  // do not obfuscate lines that contain specified patters
    // be careful using this pattern, dont specify any string that can be accidentally 
    // a part of some of your code. It is matched as a string, not as regular expression.
    // Also consider all the dependencies of non-obfuscated lines.
    // Example of use:
    // $LineExclude = '__POBS_EXCLUDE__';
    // then put comment containing __POBS_EXCLUDE__ to every line you dont want to obfuscate 
    // like: $val = myfunction($a, $b); // __POBS_EXCLUDE__ (this line wil be not obfuscated) 
    
    
    // javascript variables that should not be replaced
    $StdExcJSVarArray = array('Dummy Entry',
    "value",
    "selectedIndex",
    "text",
    "name",
    "color",
    "style",
    "length",
    "selection",
    "new",
    "var",
    "editObject",
    "head",
    "base",
    "keywords",
    "description",
    "src",
    "cont",
    "html",
    "forms",
    "head",
    "row",
    "i",
    "j",
    "k",
    "title",
    "content",
    "type"
    );
    
    // javascript functions that should not be replaced
    $StdExcJSFuncArray = array('Dummy Entry'
    );
    
    // standard variables that should not be replaced
    $StdExcVarArray = array('Dummy Entry',
    "GLOBALS",
    "GATEWAY_INTERFACE",
    "SERVER_NAME",
    "SERVER_SOFTWARE",
    "SERVER_PROTOCOL",
    "REQUEST_METHOD",
    "QUERY_STRING",
    "DOCUMENT_ROOT",
    "HTTP_ACCEPT",
    "HTTP_ACCEPT_CHARSET",
    "HTTP_ACCEPT_ENCODING",
    "HTTP_ENCODING",
    "HTTP_ENV_VARS",
    "_ENV",
    "HTTP_ACCEPT_LANGUAGE",
    "HTTP_CONNECTION",
    "HTTP_HOST",
    "HOST",
    "HTTP_REFERER",
    "HTTP_SERVER_VARS",
    "_SERVER",
    "HTTP_USER_AGENT",
    "REMOTE_ADDR",
    "REMOTE_PORT",
    "SCRIPT_FILENAME",
    "SERVER_ADMIN",
    "SERVER_PORT",
    "SERVER_SIGNATURE",
    "PATH_TRANSLATED",
    "SCRIPT_NAME",
    "REQUEST_URI",
    "argv",
    "argc",
    "PHPSESSID",
    "SID",
    "PHP_SELF",
    "HTTP_COOKIE_VARS",
    "_COOKIE",
    "HTTP_GET_VARS",
    "_GET",
    "HTTP_POST_VARS",
    "_POST",
    "HTTP_SESSION_VARS",
    "_SESSION",
    "HTTP_POST_FILES",
    "_FILES",
    "userfile",
    "userfile_name",
    "userfile_size",
    "userfile_type",
    "this",
    "_REQUEST",
    "__FILE__",
    "__LINE__"
    );

    // variables, for which their key will be not replaced
    // for exaplle for HTTP_SERVER_VARS['REMOTE_ADDR'], the REMOTE_ADDR string will be not replaced
    $StdExcKeyArray = array('Dummy Entry',
    "_SERVER",
    "HTTP_SERVER_VARS",
    "_ENV",
    "HTTP_ENV_VARS"
    );
    
    // all functions, that return objects (require special handling)
    $StdObjRetFunctionsArray = array('Dummy Entry',
    "mysql_fetch_object",
    "pg_fetch_object"
    );

    // types of comments that have to be replaced
    // available types are: '/**/','//' and '#'
    $StdReplaceComments = array('Dummy Entry',
    "/**/",
    "//"
    );

    // variables in this array will be not replaced
    $UdExcVarArray = array('Dummy Entry');

    // constants in this array will be not replaced
    $UdExcConstArray = array('Dummy Entry');

    // functions in this array will be not replaced
    $UdExcFuncArray = array('Dummy Entry');
    
    // files that will be excluded from obfuscation
    // you can use start convertion, like '*cat_*.php'
    // the files will be copied to the target directory
    $UdExcFileArray = array('Dummy Entry');

    // directories that will be excluded from obfuscation
    // you can use star convention, like '/*mydirname*'
    // it is recommended to use '/' in the beginning of directory name if you want to filter directory beginning with specified string
    // WARNING: specified directories with all its content will be NOT processed and NOT copied to the target directory
    // if you are using them in your PHP code, you have to copy them by hand
    $UdExcDirArray = array('Dummy Entry');
?>