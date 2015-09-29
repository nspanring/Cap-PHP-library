<?php 
/*
 *  Copyright (c) 2015  Niklas Spanring   <n.spanring@backbone.co.at>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file      	index.php
 *  \ingroup   	main
 */
 
/**
 * Front end of the Cap-php-library
 */
	
	require_once 'class/cap.form.class.php';
	require_once 'class/cap.create.class.php';
	require_once 'class/cap.write.class.php';
	require_once 'class/translate.class.php';
	
	if(file_exists('source/conf/conf.php'))
	{
		include 'source/conf/conf.php';
	
		$langs = new Translate();		
		$langs->setDefaultLang($conf->user->lang);		
		$langs->load("main");	
	}
	
	if(!file_exists('source/conf/conf.php'))
	{
		$cap = new CAP_Form();			
		print $cap->install();
	}
	elseif(empty($_POST['action']) && $_POST['send'] != 1)
	{
			$form = new CAP_Form();
			print $form->Form();
	}
	elseif($_POST['action'] == "create" && $_GET['conf'] != 1)
	{
		$form = new CAP_Form();
		$_POST = $form->MakeIdentifier($_POST);
		
		$cap = new CAP_Class($_POST);
		
		if(!empty($_GET['cap']))
		{
			// Used for the Cap preview
			$cap->buildCap();
			print $cap->cap;
		}
		else
		{
			// Used to build the cap and save it at $cap->destination
			$cap->buildCap();
			if($conf->cap->save == 1)	$path = $cap->createFile();
			
			$conf->identifier->ID_ID++;
			$form->WriteConf();
			
			// show the path to the file as link
			//print $langs->trans("Download").': <a href="'.$path.'" download>'.$cap->identifier.'.cap</a>';
			//print '<p>'.$langs->trans("viaSoap").':<br>';
			//include ('cap.webservices.php');
			print $form->CapView($cap->cap, $_POST[identifier]);
		}
	}
	elseif($_POST['send'] == 1)
	{
		// start webservices
		include ('cap.webservices.php');
	}
	elseif($_GET['conf'] == "1")
	{
		$form = new CAP_Form();		
		$form->PostToConf($_POST['conf']);		
		$form->WriteConf();
		return true;
	}
	
?>