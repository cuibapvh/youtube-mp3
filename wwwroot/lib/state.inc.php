<?php
 
 class State {
	
	function stateHandler($state,$language){
		if (strcasecmp($language,"DE")==0){
			if ( $state == "ready" ){
				return "<b>Dein MP3 Download ist fertig umgewandelt</b>: ";
			} elseif ( $state == "working" ){ 
				return "Dein MP3 Download wird gerade umgewandelt, warte bitte 30 Sekunden.";
			} elseif ( $state == "error" ){ 
				return "Dein MP3 Download konnte leider nicht umgewandelt werden. Es gab ein Fehler: ";
			} elseif ( $state == "else" ){ 
				return "Falsches Eingabeformat. Keine Umwandlung m&ouml;glich.";
			}
		} // if (strcasecmp($language,"DE")==0){
		if (strcasecmp($language,"EN")==0){
			if ( $state == "ready" ){
				return "<b>Your MP3 Download is ready</b>: ";
			} elseif ( $state == "working" ){ 
				return "Your Download is currently being converted, please wait 30 more seconds.";
			} elseif ( $state == "error" ){ 
				return "We had an error converting your Download: ";
			} elseif ( $state == "else" ){ 
				return "Wrong input format. Converting not possible.";
			}
		} // if (strcasecmp($language,"DE")==0){
	}
 };
 ?>