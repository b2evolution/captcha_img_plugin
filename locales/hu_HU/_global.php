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
'Captcha images' => 'Captcha képek',
'Use generated images to tell humans and robots apart.' => 'Generált képek használata, hogy az embereket és a robotokat meg tudjuk különböztetni.',
'Protect trackback URLs' => 'Trackback URL-ek védelme',
'Fonts folder' => 'Betûkészlet könyvtár',
'Path to a folder with TrueType fonts for captcha text, relative to the plugin file.' => 'A TrueType betûkészletek könyvtára, relatív a plugin állományhoz képest.',
'Timeout for keys' => 'A kulcsok lejárata',
'in minutes. When does the generated captcha expire?' => 'perc. Ennyi idõ után a captcha lejár.',
'Websafe colors' => 'Biztonságos web-színek',
'Use web safe colors (only 216 colors)?' => 'Biztonságos web-színek használata (csak 216 szín).',
'Noise' => 'Zaj',
'Use background noise characters instead of a grid.' => 'A rács helyett zaj használata a háttérben.',
'Noise factor' => 'Zajfaktor',
'Noise multiplier (number of characters gets multipled by this to define noise).' => 'Zaj szorzó (a karakterek száma lesz megszorozva ezzel, hogy a zaj mértékét kiszámolja)',
'Min chars' => 'Minimum karakterszám',
'The minimum number of characters to use.' => 'A minimum karakterszám, amit használni szeretnél.',
'Max chars' => 'Maximum karakterszám',
'The maximum number of characters to use.' => 'Maximum karakterszám, amit használni szeretnél.',
'Min font size' => 'Minimum betûméret',
'The minimum font size to use.' => 'A legkisebb betûméret, amit használni szeretnél.',
'Max font size' => 'Maximum betûméret',
'The maximum font size to use.' => 'A legnagyobb betûméret, amit használni szeretnél.',
'Max rotation' => 'Maximum forgatás',
'The maximum degrees a char should be rotated. 25 means a random rotation between -25 and 25.' => 'A maximum forgatás, amivel a betûk el lesznek forgatva. A 25 azt jelenti, hogy véletlenszerûen lesz forgatva -25 és 25 fok között.',
'JPEG quality' => 'JPEG minõség',
'JPEG image quality.' => 'JPEG képminõség',
'Valid characters' => 'Érvényes karakterek',
'Valid characters to use in generated images.' => 'Érvényes karakterek a generált képben.',
'Case sensitive' => 'Kisbetû-nagybetû érzékeny',
'Use case sensitive keys?' => 'Kibetû-nagybetû érzékenység használata',
'Use for' => 'Használd a következõkhöz:',
'Use for anonymous' => 'Használd az anonymous-hoz',
'Should this plugin be used for anonymous users?' => 'A plugin használatban lesz az anonymous felhasználóknál?',
'Use for members' => 'Használd a tagokhoz',
'Use this plugin for members of the target blog, if their level is below this.' => 'Használd a plugint a blogok tagjainál, ha a szintjük alacsonyabb, mint ez.',
'Use for registered' => 'Használd a regisztráltakhoz',
'Use this plugin for registered users, if their level is below this.' => 'Használd a plugint a regisztrált felhasználóknál, ha a szintjük alacsonyabb, mint ez.',
'Post-process' => 'Utófeldolgozás',
'A command to post-process the image.' => 'A parancs, amivel utófeldolgozzuk a képet.',
'You do not seem to come from the intended page!' => 'Úgy néz ki, hogy nem a megfelelõ lapról jöttél!',
'No stored private key has been found. You probably do not have cookies enabled or the timeout of %d minutes has expired.' => 'Nem találom a kulcsot. Talán nincsenek a sütik engedélyezve a böngészõdben, vagy a %d perc idõt túllépted.',
'The entered code does not match the expected one.' => 'A beírt kód nem egyezik a várt kóddal.',
'This is a captcha-picture. It is used to prevent mass-access by robots.' => 'Ez egy captcha-kép, ennek használatával megakadályozzuk a robotok áradatát.',
'Reload' => 'Újratöltés',
'Reload image!' => 'Kép újratöltése!',
'Please enter the characters from the image above.' => 'Kérlek, add meg a szöveget a fenti képbõl.',
'case insensitive' => 'nem kisbetû-nagybetû érzékeny',
'case sensitive' => 'kisbetû-nagybetû érzékeny',
'Captcha' => 'Captcha',
'Validate me' => 'Érvényesít',
'The captcha code was invalid: %s' => 'A captcha kód érvénytelen: %s',
'Display trackback URL' => 'Trackback URL mutatása',
'Invalid trackback URL!' => 'Érvénytelen trackback URL!',
'Invalid key in trackback URL!' => 'Érvénytelen kulcs a trackback URL-ben!',
'Create test image... (please save any changes before)' => 'Teszt kép készítése... (kérlek, elõször mentsd el az esetleges beállítás változásokat)',
'A generated image should show up below. The image only gets displayed once - use the test link again for a new try.' => 'Lent a generált képet kellene látnod. A képet csak egyszer mutatjuk - használd a teszt linket, ha újra meg akarod próbálni.',
'The GD library does not seem to be installed.' => 'Úgy néz ki, hogy a GD library nincs telepítve.',
'No JPEG support. (Function imagejpeg does not exist)' => 'Nincs JPEG támogatás. (Az imagejpeg eljárás nem létezik.)',
'FreeType library not available. (Function imagettftext does not exist)' => 'A FreeType library nem érhetõ el. (Az imagettfext eljárás nem létezik.)',
'Fonts folder %s is not readable or does not exist!' => 'A %s betûkészlet könyvtár nem olvasható, vagy nem létezik!',
'No Truetype fonts available!' => 'Nincsenek TruType betûkészletek megadva!',

);
?>