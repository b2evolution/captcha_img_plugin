# captcha_img Plugin

Use generated images to tell humans and spam-bots apart.

**Additional System Requirements**:
GD Image and FreeType libraries for PHP


## Detailed Description##

This plugin requires users to enter a string of characters displayed in an image before posting a comment.
This is designed to reduce automated comment spam by requiring human interaction.

While some captchas have been broken by character recognition bots this plugin takes pains to reduce this possibility.

Additional security (over some other existing image captcha programs) is provided by: variable character size, rotation and fonts, backgrond noise characters, variable captcha length and support for characters other than A-Z and 0-9.


## Installation

- Copy the `captcha_img_plugin` folder into the `plugins` folder of your b2evolution installation.
- You should have an empty `fonts` folder inside of it.
- Put any number of TrueType fonts inside of the `fonts` folder, these fonts will be randomly selected by the captcha plugin. Fonts MUST end in `.ttf` to be recognized. For free font suggestions please see the "Additional Resources" section.
- Login to the administrative interface for your blog.
- Install the "Captcha images" plugin from the Settings::Plug-ins::Available plugins table using the [Install] link.
- Edit the settings for the "Captcha images" plugin for your personal preferences.
For additional information see the "Plugin Settings" section of this README.
- Save plugin settings. The Captcha images plugin is now active.


## Plugin Settings

**Fonts folder**:
Path to the folder where captcha fonts are stored relative to the plugin folder.
The fonts therein have to be TrueType fonts (*.ttf).

**Timeout for keys**:
Each time a captcha is created it can only be used for this many minutes.

**Noise**:
When on (default) use character noise in the background instead of a grid.

**Noise factor**:
The length of the captcha multiplied by this number will be used to create background noise.
Increasing this number creates more background noise.

**Min chars/Max chars**:
The captcha will be a random length between these two numbers of characters.
For a fixed length captcha set both of these the same.

**Font sizes**:
Each character will use a different font size between the maximum and minimum font sizes specified.
If the maximum and minimum are equal all characters will be that size.

**Max rotation**:
Each character in the captcha may be rotated up to this number of degrees in either direction.

**Valid Characters**:
This is a list of all the characters that can be used in a captcha.
By default it contains a list of ASCII characters that are easily distinguished.
Characters such as iIlL1!^oO0-_=+ have not been included because they could easily be mistaken for other characters.
Numbers and extra characters are listed twice to increase the probability they show up in a captcha.

**Case sensitive**:
This option is turned off by default so users are more likely to successfully enter the captcha,
case sensitive captchas are not normally used because they increase end user frustration.


### Use for</h3>

**Use for anonymous**:
If the user is anonymous (not registered), this setting applies.

**Use for members**:
If the user is a member of the target blog, this setting applies. If his user level is below the one
you've configured, he has to pass the Captcha. "0" means all members won't have to pass the Captcha.

**Use for registered**:
If the user is registered, but not a member of the target blog, this setting applies.
If his user level is below the one you've configured, he has to pass the Captcha.
"0" means all registered users won't have to pass the Captcha.


### Additional Resources

Selecting Fonts: This plugin requires at least one TrueType font file (*.TTF) in the specified fonts folder. While many free fonts are available online it would be best to select fonts which are not difficult for the average person to read. Remember that people will not be able to post comments unless they correctly enter the captcha, so use of a difficult to read font may limit participation.

Below you will find a list of sites offering free fonts, remember to unzip (if required) and upload the TTF file to the correct "fonts" folder inside of your "plugins" folder. These fonts may have some use restrictions (no commercial use, etc.) so please make sure your use is allowed.


### Free Font Sites:
- http://fonts.tom7.com/
- http://www.astigmatic.com/free_fonts.html
- http://www.swank.ca/caffeen/fonts/
- http://www.core.nu/freefonts.html
- http://www.webpagepublicity.com/free-fonts.html
- http://www.grsites.com/fonts/
- http://moorstation.org/typoasis/designers/steffmann/index.htm
- http://www.typenow.net/