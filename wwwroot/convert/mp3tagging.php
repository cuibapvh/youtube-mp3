<?php



$songtext = <<<END
(Instrumental)

Tsunami, drop!

(Instrumental)
Drop, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami

Tsunami, drop!

Nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, drop!

(Instrumental)
END;






$back = system("/usr/bin/eyeD3 -2 --to-v2.4 -a \"DVBBS Borgeous\" -t \"DVBBS Borgeous - TSUNAMI\" --lyrics=\"eng:DVBBS Borgeous - TSUNAMI:$songtext\" test.mp3");
echo $back;


?>