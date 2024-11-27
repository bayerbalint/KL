<?php

// /**
//  * @author Bayer Bálint
//  * 
//  * Projekt feladat
//  */

session_start();

require_once("./classroom-data.php");
require_once("./projekt-html.php");

ShowHTMLHead();
ShowButtons();

if (empty($_SESSION["Classes"])) {
    CreateClasses();
}

ShowClassesOnButtonPress();
SaveDataBySelectedClass();
RegenerateClassData();

// Osztaly(ok) letrehozasa
function CreateClasses()
{
    $Classes = [];
    foreach (DATA["classes"] as $Class){
        $Classes[$Class] = CreateClass($Class);
    }
    $_SESSION["Classes"] = $Classes;
}

function CreateClass($ClassNumber){
    $Class = [];
    $ClassSize = rand(10,15);
    for ($i = 0; $i < $ClassSize; $i++){
        $Gender = rand(0,2) == 0 ? "men" : "women";
        $LastName = DATA["lastnames"][rand(0,count(DATA["lastnames"])-1)];
        $FirstName = DATA["firstnames"][$Gender][rand(0, count(DATA["firstnames"][$Gender])-1)];

        $Grades = [];
        foreach (DATA["subjects"] as $Subject){
            $NumberOfGrades = rand(0, 5);
            $Grades[$Subject] = [];
            for ($j = 0; $j < $NumberOfGrades; $j++){
                $Grades[$Subject][$j] = rand(1, 5);
            }
        }
        $Class[$LastName . ' ' . $FirstName] = $Grades;
    }
    return $Class;
}

// Gombok
function ShowClassesOnButtonPress(){
    foreach (DATA["classes"] as $Class){
        if (isset($_POST[$Class])){
            ShowClass($Class);
            $_SESSION["CurrentClass"] = $Class;
        }
    }
    if (isset($_POST["teljesIskola"])){
        ShowClass("*");
        $_SESSION["CurrentClass"] = "*";
    }
}

function SaveDataBySelectedClass(){
    if (isset($_POST["save"]) && !empty($_SESSION["CurrentClass"])){
        SaveData($_SESSION["CurrentClass"]);
        ShowClass($_SESSION["CurrentClass"]);
    }
}

function RegenerateClassData(){
    if (isset($_POST["regenerate"])){
        unset($_SESSION["Classes"]);
    }
}

// Megjelenites (lehetne a projekt-html-ben talan)
function ShowClass($ClassNumber){
    $Header = "<tr><td>Name</td><td>math</td><td>history</td><td>biology</td><td>chemistry</td><td>physics</td><td>informatics</td><td>alchemy</td><td>astrology</td></tr>";
    echo '<div id="container">';

    foreach ($_SESSION["Classes"] as $Class => $Students){
        if ($ClassNumber == "*" || $Class == $ClassNumber){
            $Table = "<table id='t" . $Class . "'>";
            $Table .= '<th colspan="9">' . $Class . '</th>' . $Header;
            foreach ($Students as $Student => $Subjects){
                $Table .= '<tr><td>' . $Student . '</td>';
                foreach ($Subjects as $Subject => $Grades){
                    $Table .= '<td>';
                    foreach ($Grades as $Grade){
                        $Table .= '<div ';
                        switch ($Grade){
                            case 1:
                                $Table .= "class='f'>" . $Grade;
                                break;
                            case 2:
                                $Table .= 'class="d">' . $Grade;
                                break;
                            case 3:
                                $Table .= 'class="c">' . $Grade;
                                break;
                            case 4:
                                $Table .= 'class="b">' . $Grade;
                                break;
                            case 5:
                                $Table .= 'class="a">' . $Grade;
                                break;
                        }
                        $Table .= '</div>';
                    }
                    $Table .= '</td>';
                }
                $Table .= '</tr>';
            }
            $Table .= '</table>';
            echo $Table;
        }
    }
    echo '</div></body></html>';
}

// Mentes
function SaveData($ClassName){
    if (!is_dir("./export")){
        mkdir("./export");
    }

    foreach ($_SESSION["Classes"] as $Class => $Students){
        if ($ClassName == $Class || $ClassName == "*"){
            $FileName = "./export/{$Class}" . date("-Y-m-d_His") . ".csv";
            $File = fopen($FileName, "w");

            // ekezetek kezelese
            $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
            fputs($File, $bom);

            // fejlec
            fputcsv($File, ["ID", "Name", "Firstname", "Lastname", "Gender", "Subject", "Grades"], ";");
            foreach ($Students as $Student => $Subjects){
                $StudentName = explode(" ", $Student);
                $StudentId = array_search($Student, array_keys($Students));
                $Gender = in_array($StudentName[1], DATA["firstnames"]["men"]) ? "M" : "F";
                $StudentData = "$Class-$StudentId,$Student,$StudentName[1],$StudentName[0],$Gender";
    
                foreach ($Subjects as $Subject => $Grades){
                    $StudentsGrades = "";
                    for ($i = 0; $i < count($Grades); $i++){
                        if ($i != count($Grades)-1){
                            $StudentsGrades .= $Grades[$i] . ", ";
                        }
                        else {
                            $StudentsGrades .= $Grades[$i];
                        }
                    }
                    $DataOut = explode(",", $StudentData);
                    $DataOut[] = $Subject;
                    $DataOut[] = $StudentsGrades;
                    fputcsv($File, $DataOut, ";");
                }
            }
            fclose($File);
        }
    }
}