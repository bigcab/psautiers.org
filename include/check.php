<?php
//fonction qui checke chaque variable post
function check_post($vars)
{
        foreach($vars as $var)
        {
                if (!isset($_POST[$var]) || (empty($_POST[$var])))
                {
                        return FALSE;
                }
        }
        return TRUE;
}


function check_files($vars)
{
        foreach($vars as $var)
        {
                 if (!check_file($var))
                {
                        return FALSE;
                }
        }
        return TRUE;
}


function check_get($vars)
{
        foreach($vars as $var)
        {
                if (!isset($_GET[$var]) || (empty($_GET[$var])))
                { 
                        return FALSE;
                }
        }
        return TRUE;
}

function check_file($var)
{
        if( !isset($_FILES[$var]))
        {
        	return FALSE;
        }
        if (($_FILES[$var]['error'] > 0) )
        {
        		return FALSE;
        }
        
        return TRUE;
}
?>
