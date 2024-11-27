<?php

function ShowHTMLHead(){
echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ProjektMunka</title>
        <link rel="stylesheet" href="projekt.css">
    </head>';
}

function ShowButtons(){
echo '<body>
        <form id="form" method="POST" action="">
            <ul>    
                <li><button name="teljesIskola">*</button></li>
                <li><button name="11a">11.a</button></li>
                <li><button name="11b">11.b</button></li>
                <li><button name="11c">11.c</button></li>
                <li><button name="12a">12.a</button></li>
                <li><button name="12b">12.b</button></li>
                <li><button name="12c">12.c</button></li>
                <li><button name="save">Save</button></li>
                <li><button name="regenerate">Regenerate</button></li>
                
            </ul>
        </form>';
}