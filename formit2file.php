<?php
/*
	Formit hook to upload a file with certain restrictions:
	
	path - path to upload files to from root
	extensions - comma seperated list with extensions to allow uploading
	maxsize - maximum filesize in bytes allowed
	minsize - minimum filesize in bytes allowed
	
*/

//function to clean a string
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

$path; // Path from root that user specifies
$extensions; // allow file extensions
$minsize; //minimum filesize in bytes
$maxsize; // maximum filesize in bytes

//define subdirectory by chose theme
$thema = strtolower($hook->getValue('thema'));

//naam ophalen en netjes maken
$naam = strtolower($hook->getValue('naam'));
$naam = str_replace(' ', '_', $naam);
$naam = clean($naam);

$thema = $thema.'/';

$ext_array = explode(',', $extensions);

// Create path
$basepath = $modx->config['base_path']; // Site root
$target_path = $basepath . $path; // root /assets/upload

// Get Filename and make sure its good.
$filename = basename( $_FILES['bestand']['name'] );

// Get files extension
$ext = pathinfo($filename, PATHINFO_EXTENSION);
//make lowercase for comparison
$ext = strtolower($ext);

if($filename != '')
{
    // Make filename a good unique filename.
    // Make lowercase
    $filename = mb_strtolower($filename);
    // Replace spaces with _
    $filename = str_replace(' ', '_', $filename);
    // Add timestamp
    $filename = date("Ymdgi") .'_'. $filename;
	// Add name
	$filename = $naam.'_'.$filename;

    // Set final path
    $target_path = $target_path . $thema . $filename;
	if($_FILES['bestand']['size'] > $minsize){
		if($_FILES['bestand']['size'] < $maxsize){
			if(in_array($ext, $ext_array))
			{
				if(move_uploaded_file($_FILES['bestand']['tmp_name'], $target_path))
				{
				  // Upload successful
				  $hook->setValue('bestand',$filename);
				  return true;
				}
				else
				{
				  // File not uploaded
				  $errorMsg = 'Er is een fout opgetreden, kan bestand niet uploaden';
				  $hook->addError('bestand',$errorMsg);
				  return false;
				}
			  }
			  else
			  {
				// File type not allowed
				$errorMsg = 'Dit bestand is niet in JPEG of TIFF formaat';
				$hook->addError('bestand',$errorMsg);
				return false;
			  }
		}else{
			//filesize to big
			$errorMsg = 'Bestand mag niet groter zijn dan 7MB ';
			$hook->addError('bestand',$errorMsg);
			return false;
		}
	}else{
			//filesize too low
			$errorMsg = 'Bestand mag niet kleiner zijn dan 2MB';
			$hook->addError('bestand',$errorMsg);
			return false;
	}
}
else
{
	//update de hook met de nieuwe bestandsnaam voor later gebruik
    $hook->setValue('bestand',$filename);
    return true;
}

return true;