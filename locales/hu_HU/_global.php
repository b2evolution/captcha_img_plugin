<?php
/*
 * Global lang file
 * This file was generated automatically from messages.po
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


$trans['hu_HU'] = array('' => 'Project-Id-Version: captcha_img_plugin 2.0
Report-Msgid-Bugs-To: http://daniel.hahler.de/
POT-Creation-Date: 2007-09-10 22:20+0200
PO-Revision-Date: 2007-09-11 09:01+0100
Last-Translator: Imre Balla <ballaimre@gmail.com>
Language-Team: Imre Balla <ballaimre@gmail.com>
MIME-Version: 1.0
Content-Type: text/plain; charset=iso-8859-2
Content-Transfer-Encoding: 8bit
X-Poedit-Language: Hungarian
X-Poedit-Country: HUNGARY
X-Poedit-SourceCharset: iso-8859-2
',
'Captcha images' => 'Captcha k�pek',
'Use generated images to tell humans and robots apart.' => 'Gener�lt k�pek haszn�lata, hogy az embereket �s a robotokat meg tudjuk k�l�nb�ztetni.',
'Protect trackback URLs' => 'Trackback URL-ek v�delme',
'Fonts folder' => 'Bet�k�szlet k�nyvt�r',
'Path to a folder with TrueType fonts for captcha text, relative to the plugin file.' => 'A TrueType bet�k�szletek k�nyvt�ra, relat�v a plugin �llom�nyhoz k�pest.',
'Timeout for keys' => 'A kulcsok lej�rata',
'in minutes. When does the generated captcha expire?' => 'perc. Ennyi id� ut�n a captcha lej�r.',
'Websafe colors' => 'Biztons�gos web-sz�nek',
'Use web safe colors (only 216 colors)?' => 'Biztons�gos web-sz�nek haszn�lata (csak 216 sz�n).',
'Noise' => 'Zaj',
'Use background noise characters instead of a grid.' => 'A r�cs helyett zaj haszn�lata a h�tt�rben.',
'Noise factor' => 'Zajfaktor',
'Noise multiplier (number of characters gets multipled by this to define noise).' => 'Zaj szorz� (a karakterek sz�ma lesz megszorozva ezzel, hogy a zaj m�rt�k�t kisz�molja)',
'Min chars' => 'Minimum karaktersz�m',
'The minimum number of characters to use.' => 'A minimum karaktersz�m, amit haszn�lni szeretn�l.',
'Max chars' => 'Maximum karaktersz�m',
'The maximum number of characters to use.' => 'Maximum karaktersz�m, amit haszn�lni szeretn�l.',
'Min font size' => 'Minimum bet�m�ret',
'The minimum font size to use.' => 'A legkisebb bet�m�ret, amit haszn�lni szeretn�l.',
'Max font size' => 'Maximum bet�m�ret',
'The maximum font size to use.' => 'A legnagyobb bet�m�ret, amit haszn�lni szeretn�l.',
'Max rotation' => 'Maximum forgat�s',
'The maximum degrees a char should be rotated. 25 means a random rotation between -25 and 25.' => 'A maximum forgat�s, amivel a bet�k el lesznek forgatva. A 25 azt jelenti, hogy v�letlenszer�en lesz forgatva -25 �s 25 fok k�z�tt.',
'JPEG quality' => 'JPEG min�s�g',
'JPEG image quality.' => 'JPEG k�pmin�s�g',
'Valid characters' => '�rv�nyes karakterek',
'Valid characters to use in generated images.' => '�rv�nyes karakterek a gener�lt k�pben.',
'Case sensitive' => 'Kisbet�-nagybet� �rz�keny',
'Use case sensitive keys?' => 'Kibet�-nagybet� �rz�kenys�g haszn�lata',
'Use for' => 'Haszn�ld a k�vetkez�kh�z:',
'Use for anonymous' => 'Haszn�ld az anonymous-hoz',
'Should this plugin be used for anonymous users?' => 'A plugin haszn�latban lesz az anonymous felhaszn�l�kn�l?',
'Use for members' => 'Haszn�ld a tagokhoz',
'Use this plugin for members of the target blog, if their level is below this.' => 'Haszn�ld a plugint a blogok tagjain�l, ha a szintj�k alacsonyabb, mint ez.',
'Use for registered' => 'Haszn�ld a regisztr�ltakhoz',
'Use this plugin for registered users, if their level is below this.' => 'Haszn�ld a plugint a regisztr�lt felhaszn�l�kn�l, ha a szintj�k alacsonyabb, mint ez.',
'Post-process' => 'Ut�feldolgoz�s',
'A command to post-process the image.' => 'A parancs, amivel ut�feldolgozzuk a k�pet.',
'You do not seem to come from the intended page!' => '�gy n�z ki, hogy nem a megfelel� lapr�l j�tt�l!',
'No stored private key has been found. You probably do not have cookies enabled or the timeout of %d minutes has expired.' => 'Nem tal�lom a kulcsot. Tal�n nincsenek a s�tik enged�lyezve a b�ng�sz�dben, vagy a %d perc id�t t�ll�pted.',
'The entered code does not match the expected one.' => 'A be�rt k�d nem egyezik a v�rt k�ddal.',
'This is a captcha-picture. It is used to prevent mass-access by robots.' => 'Ez egy captcha-k�p, ennek haszn�lat�val megakad�lyozzuk a robotok �radat�t.',
'Reload' => '�jrat�lt�s',
'Reload image!' => 'K�p �jrat�lt�se!',
'Please enter the characters from the image above.' => 'K�rlek, add meg a sz�veget a fenti k�pb�l.',
'case insensitive' => 'nem kisbet�-nagybet� �rz�keny',
'case sensitive' => 'kisbet�-nagybet� �rz�keny',
'Captcha' => 'Captcha',
'Validate me' => '�rv�nyes�t',
'The captcha code was invalid: %s' => 'A captcha k�d �rv�nytelen: %s',
'Display trackback URL' => 'Trackback URL mutat�sa',
'Invalid trackback URL!' => '�rv�nytelen trackback URL!',
'Invalid key in trackback URL!' => '�rv�nytelen kulcs a trackback URL-ben!',
'Create test image... (please save any changes before)' => 'Teszt k�p k�sz�t�se... (k�rlek, el�sz�r mentsd el az esetleges be�ll�t�s v�ltoz�sokat)',
'A generated image should show up below. The image only gets displayed once - use the test link again for a new try.' => 'Lent a gener�lt k�pet kellene l�tnod. A k�pet csak egyszer mutatjuk - haszn�ld a teszt linket, ha �jra meg akarod pr�b�lni.',
'The GD library does not seem to be installed.' => '�gy n�z ki, hogy a GD library nincs telep�tve.',
'No JPEG support. (Function imagejpeg does not exist)' => 'Nincs JPEG t�mogat�s. (Az imagejpeg elj�r�s nem l�tezik.)',
'FreeType library not available. (Function imagettftext does not exist)' => 'A FreeType library nem �rhet� el. (Az imagettfext elj�r�s nem l�tezik.)',
'Fonts folder %s is not readable or does not exist!' => 'A %s bet�k�szlet k�nyvt�r nem olvashat�, vagy nem l�tezik!',
'No Truetype fonts available!' => 'Nincsenek TruType bet�k�szletek megadva!',

);
?>