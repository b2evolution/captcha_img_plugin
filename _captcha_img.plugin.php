<?php
/**
 * This file implements the Captcha Image plugin.
 *
 * The core functionality was provided by Ben Franske and then converted and improved
 * by Daniel HAHLER into a plugin.
 *
 * Based on hn_captcha Version 1.2 by Horst Nogajski
 *     - hn_captcha is a fork of ocr_captcha by Julien Pachet
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link http://sourceforge.net/projects/evocms/}.
 *
 * @copyright 2006 by Daniel HAHLER - {@link http://daniel.hahler.de/}.
 *
 * @license http://b2evolution.net/about/license.html GNU General Public License (GPL)
 * {@internal
 * b2evolution is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * b2evolution is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with b2evolution; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * In addition, as a special exception, the copyright holders give permission to link
 * the code of this program with the PHP/SWF Charts library by maani.us (or with
 * modified versions of this library that use the same license as PHP/SWF Charts library
 * by maani.us), and distribute linked combinations including the two. You must obey the
 * GNU General Public License in all respects for all of the code used other than the
 * PHP/SWF Charts library by maani.us. If you modify this file, you may extend this
 * exception to your version of the file, but you are not obligated to do so. If you do
 * not wish to do so, delete this exception statement from your version.
 * }}
 *
 * @package plugins
 *
 * @author blueyed: Daniel HAHLER
 * @author Ben Franske, http://ben.franske.com
 *
 * @version $Id: _captcha_img.plugin.php 1196 2010-03-29 18:29:50Z blueyed $
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * The Captcha Image Plugin.
 *
 * It displays an captcha image through {@link CaptchaValidated()} and validates
 * it in {@link CaptchaValidated()}.
 *
 * The image gets served by an htsrv method ({@link htsrv_display_captcha()} and the
 * private/public keys get stored in the user's Session.
 *
 * @todo If a comment gets previewed, re-use the same captcha image and re-use eventually posted code in input field
 * @todo Make sure font files are really font files..
 */
class captcha_img_plugin extends Plugin
{
	var $version = '2.0.3';
	var $group = 'antispam';

	/**
	 * High priority: Should get called early, so other plugins can skip their
	 * action in case there are already errors from us (e.g. the OpenID plugin).
	 * @var integer
	 */
	var $priority = 20;


	/**
	 * Delete unnecessary captcha data from DB table.
	 *
	 * @todo This should be added to the "scheduler", once available
	 */
	function PluginInit( & $params )
	{
		$this->name = $this->T_('Captcha images');
		$this->short_desc = $this->T_('Use generated images to tell humans and robots apart.');

		if( $params['is_installed'] && rand(0, 100) == 42 )
		{
			register_shutdown_function( array(&$this, 'purge_obsolete_db_data') );
		}

		return true;
	}


	function GetDefaultSettings()
	{
		global $Settings;

		return array(
				'protect_trackback_url' => array(
					'label' => $this->T_('Protect trackback URLs'),
					'type' => 'checkbox',
					'defaultvalue' => '1',
				),


				'TTF_folder' => array(
					'label' => $this->T_( 'Fonts folder' ),
					'defaultvalue' => 'fonts/',
					'note' => $this->T_('Path to a folder with TrueType fonts for captcha text, relative to the plugin file.'),
					'size' => 20,
				),
				'timeout_key' => array(
					'label' => $this->T_('Timeout for keys'),
					'defaultvalue' => 120, // timeout: 2 hours
					'note' => $this->T_('in minutes. When does the generated captcha expire?'),
					'size' => 5,
					'type' => 'integer',
				),
				'use_websafecolors' => array(
					'label' => $this->T_('Websafe colors'),
					'defaultvalue' => 0,
					'note' => $this->T_('Use web safe colors (only 216 colors)?'),
					'type' => 'checkbox',
				),
				'noise' => array(
					'label' => $this->T_('Noise'),
					'defaultvalue' => 1,
					'note' => $this->T_('Use background noise characters instead of a grid.'),
					'type' => 'checkbox',
				),
				'noisefactor' => array(
					'label' => $this->T_('Noise factor'),
					'defaultvalue' => 9,
					'note' => $this->T_('Noise multiplier (number of characters gets multipled by this to define noise).'),
					'type' => 'integer',
					'size' => 5,
				),
				'minchars' => array(
					'label' => $this->T_('Min chars'),
					'defaultvalue' => 4,
					'note' => $this->T_('The minimum number of characters to use.'),
					'type' => 'integer',
					'size' => 4,
				),
				'maxchars' => array(
					'label' => $this->T_('Max chars'),
					'defaultvalue' => 6,
					'note' => $this->T_('The maximum number of characters to use.'),
					'type' => 'integer',
					'size' => 5,
				),
				'min_fontsize' => array(
					'label' => $this->T_('Min font size'),
					'defaultvalue' => 20,
					'note' => $this->T_('The minimum font size to use.'),
					'type' => 'integer',
					'size' => 5,
				),
				'max_fontsize' => array(
					'label' => $this->T_('Max font size'),
					'defaultvalue' => 30,
					'note' => $this->T_('The maximum font size to use.'),
					'type' => 'integer',
					'size' => 5,
				),
				'max_rotation' => array(
					'label' => $this->T_('Max rotation'),
					'defaultvalue' => 25,
					'note' => $this->T_('The maximum degrees a char should be rotated. 25 means a random rotation between -25 and 25.'),
					'type' => 'integer',
					'size' => 5,
				),
				'jpegquality' => array(
					'label' => $this->T_('JPEG quality'),
					'defaultvalue' => 80,
					'note' => $this->T_('JPEG image quality.'),
					'type' => 'integer',
					'size' => 5,
					'maxlength' => 3,
				),
				'validchars' => array(
					'label' => $this->T_('Valid characters'),
					'defaultvalue' => 'abcdefghjkmnpqrstuvwxyz23456789@#$%&ABCDEFGHJKLMNPQRSTUVWXYZ23456789@#$%&',
					'note' => $this->T_('Valid characters to use in generated images.'),
					'size' => 50,
				),
				'case_sensitive' => array(
					'label' => $this->T_('Case sensitive'),
					'defaultvalue' => 0,
					'note' => $this->T_('Use case sensitive keys?'),
					'type' => 'checkbox',
				),

				'use_for' => array(
					'label' => $this->T_('Use for'),
					'layout' => 'begin_fieldset',
				),
				'use_for_anonymous' => array(
					'label' => $this->T_('Use for anonymous'),
					'defaultvalue' => 1,
					'note' => $this->T_('Should this plugin be used for anonymous users?'),
					'type' => 'checkbox',
				),
				'use_for_members_level_below' => array(
					'label' => $this->T_('Use for members'),
					'defaultvalue' => 0,
					'note' => $this->T_('Use this plugin for members of the target blog, if their level is below this.'),
					'type' => 'integer',
					'size' => 3,
					'valid_range' => array( 'min'=>0, 'max'=>11),
				),
				'use_for_level_below' => array(
					'label' => $this->T_('Use for registered'),
					'defaultvalue' => ($Settings->get('newusers_level') + 1), // the default level for new users does not allow them to bypass the captcha
					'note' => $this->T_('Use this plugin for registered users, if their level is below this.'),
					'type' => 'integer',
					'size' => 3,
					'valid_range' => array( 'min'=>0, 'max'=>11),
				),
				array( 'layout' => 'end_fieldset' ),

				array( 'layout' => 'separator' ),

				'post_process_cmd' => array(
					'label' => $this->T_('Post-process'),
					'defaultvalue' => '',
					'note' => $this->T_('A command to post-process the image.'),
					'size' => 50,
					'type' => 'textarea',
				),
			);
	}


	/**
	 * We want a table to store our Captcha data (private key, timestamp, ..).
	 *
	 * @return array
	 */
	function GetDbLayout()
	{
		return array(
				'CREATE TABLE '.$this->get_sql_table('data').' (
					cpt_public VARCHAR( 32 ) NOT NULL,
					cpt_private VARCHAR( 50 ) NOT NULL,
					cpt_sess_ID INT UNSIGNED NOT NULL,
					cpt_timestamp TIMESTAMP NOT NULL,
					cpt_invalid TINYINT UNSIGNED NOT NULL DEFAULT 0,
					PRIMARY KEY( cpt_public ),
					KEY cpt_timestamp( cpt_timestamp )
				)',

				// Holds keys for whitelisted trackback URLs:
				'CREATE TABLE '.$this->get_sql_table('trackbacks_wl').' (
					tbwl_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
					tbwl_item_ID INT UNSIGNED NOT NULL,
					tbwl_key VARCHAR( 32 ) NOT NULL,
					tbwl_timestamp TIMESTAMP NOT NULL,
					PRIMARY KEY( tbwl_ID )
				)',
			);
	}


	/**
	 * We require b2evo 1.9.x.
	 */
	function GetDependencies()
	{
		return array( 'requires' => array('app_min' => '1.9.0') );
	}


	/**
	 * Gets called, when the Plugins API detects that we've been updated (version has changed).
	 * @return boolean
	 */
	function PluginVersionChanged( & $params )
	{
		if( version_compare( $params['old_version'], '0.1.2-dev', '<' ) )
		{ // Delete old-format data:
			global $DB;

			$DB->query( 'DELETE FROM '.$this->get_sql_table('data') );
		}

		return true;
	}


	/**
	 * We have one htsrv method to display the captcha image.
	 *
	 * @return array
	 */
	function GetHtsrvMethods()
	{
		return array( 'display_captcha', 'test_page', 'test_page_img' );
	}


	/**
	 * Check GD requirements and fonts path.
	 */
	function BeforeEnable()
	{
		if( $error = $this->validate_gd_requirements() )
		{
			return $error;
		}

		if( $error = $this->load_fonts() )
		{
			return $error;
		}

		return true;
	}


	/**
	 * Check various settings.
	 */
	function PluginSettingsValidateSet( & $params )
	{
		$error = '';

		if( $params['name'] == 'TTF_folder' )
		{ // check new path
			$params['value'] = trailing_slash($params['value']);
			$error = $this->load_fonts( $params['value'] );
		}

		// TODO: min-max ordering, ...
		// TODO: post-process

		if( $error )
		{
			return $error;
		}
	}


	/**
	 * We generate and save a random salt to use for testing.
	 */
	function AfterInstall()
	{
		$this->Settings->set( 'public_key_salt', md5( mt_rand() ) );
		$this->Settings->dbupdate();
	}


	/**
	 * Validate the given private key against our stored one.
	 *
	 * This event is provided for other plugins and gets used internally
	 * for other events we're hooking into.
	 *
	 * @param array Associative list of parameters.
	 *    - 'validate_error': gets set, in case of error (by reference)
	 *    - 'prefix': key/prefix to use for the form params (string, OPTIONALLY!)
	 *
	 * @return boolean|NULL
	 */
	function CaptchaValidated( & $params )
	{
		global $DB, $localtimenow, $Session;

		$prefix = isset($params['prefix']) ? $params['prefix'] : 'captcha_img_'.$this->ID;

		$this->posted_public = param( $prefix.'_key', 'string', '', true );
		if( empty($this->posted_public) )
		{
			$this->debug_log( 'CaptchaValidated: $posted_public is empty!' );
			$params['validate_error'] = $this->T_('You do not seem to come from the intended page!');
			return false;
		}

		$this->posted_private = param( $prefix.'_private', 'string', '', true );
		$saved_private = $DB->get_var( '
				SELECT cpt_private FROM '.$this->get_sql_table('data').'
				 WHERE cpt_public = '.$DB->quote($this->posted_public).'
				   AND cpt_sess_ID = '.$Session->ID.'
				   AND UNIX_TIMESTAMP(cpt_timestamp) > '.( $localtimenow - $this->Settings->get('timeout_key')*60 ) );

		if( empty($saved_private) )
		{
			$this->debug_log( 'No private key found!' );
			$params['validate_error'] = sprintf( $this->T_('No stored private key has been found. You probably do not have cookies enabled or the timeout of %d minutes has expired.'), $this->Settings->get('timeout_key')*60 );
			return false;
		}

		if( $this->Settings->get('case_sensitive') )
		{
			$posted_private = $this->posted_private;
		}
		else
		{ // case insensitive:
			$posted_private = strtoupper( $this->posted_private );
			$saved_private = strtoupper( $saved_private );
		}

		if( $posted_private != $saved_private )
		{
			$this->debug_log( 'Posted ('.$posted_private.') and saved ('.$saved_private.') private key do not match!' );
			$params['validate_error'] = $this->T_('The entered code does not match the expected one.');
			$DB->query( '
				UPDATE '.$this->get_sql_table('data').'
				   SET cpt_invalid = cpt_invalid + 1
				 WHERE cpt_public = '.$DB->quote($this->posted_public) );
			return false;
		}

		// NOTE: the captcha data gets deleted in CaptchaValidatedCleanup()!

		return true;
	}


	/**
	 * Cleanup used captcha data.
	 *
	 * @param array Associative list of parameters.
	 *    - 'prefix': key/prefix to use for the form params (string, OPTIONALLY)
	 */
	function CaptchaValidatedCleanup( $params = array() )
	{
		global $DB, $Session;

		$prefix = isset($params['prefix']) ? $params['prefix'] : 'captcha_img_'.$this->ID;

		$posted_public = param( $prefix.'_key', 'string', '', true );

		$DB->query( '
			DELETE FROM '.$this->get_sql_table('data').'
			 WHERE cpt_public = '.$DB->quote($posted_public).'
			   AND cpt_sess_ID = '.$Session->ID );
	}


	/**
	 * When a comment form gets displayed, we inject our captcha and an input field to
	 * enter the private key (displayed in the image).
	 *
	 * The private key gets saved into the user's Session.
	 *
	 * @param array Associative array of parameters
	 *   - 'Form': the form where payload should get added (by reference, OPTIONALLY!)
	 *   - 'form_use_fieldset':
	 *   - 'prefix': key/prefix to use for the form params (string, OPTIONALLY!)
	 * @return boolean|NULL true, if displayed; false, if error; NULL if it does not apply
	 */
	function CaptchaPayload( & $params )
	{
		global $DB, $Session, $localtimenow;

		if( $this->load_fonts() )
		{ // Error: no fonts available, disable the Plugin
			$this->set_status( 'needs_config' );
			return false;
		}

		$prefix = isset($params['prefix']) ? $params['prefix'] : 'captcha_img_'.$this->ID;

		$this->private_key = $this->generate_private_key();
		$this->public_key = md5( mt_rand() );
		while( $DB->get_var( 'SELECT COUNT(*) FROM '.$this->get_sql_table('data').' WHERE cpt_public = "'.$this->public_key.'"' ) )
		{ // public key is PK in the table, so make sure, it does not exist yet..
			$this->public_key = md5( mt_rand() );
		}

		// TODO: here's a minor timing issue, which _may_ resolve in a SQL error below (duplicate KEY)
		//       Use MySQL's RAND() function for the public key ("insert into foo select MD5(RAND()*100000000") and retry on duplicate key!?

		// Save the private and public key to DB
		$DB->query( 'INSERT INTO '.$this->get_sql_table('data').'
			( cpt_public, cpt_private, cpt_sess_ID, cpt_timestamp ) VALUES
			( '.$DB->quote( $this->public_key ).', '.$DB->quote( $this->private_key ).', '.$Session->ID.', FROM_UNIXTIME('.$localtimenow.') )' );

		$this->debug_log( 'Private key is: ('.$this->private_key.')' );
		$this->debug_log( 'Public key is: (DB ID '.$this->public_key.')' );


		if( ! isset( $params['Form'] ) )
		{ // there's no Form where we add to, but we create our own form:
			$Form = new Form( regenerate_url() );
			$Form->begin_form();

			// Include previous $_POST and $_GET params (to come to the same page again):
			$Form->hiddens_by_key( array_merge($_GET, $_POST),
				/* exclude, because used as hidden or input field: */
				array($prefix.'_private', $prefix.'_key') );
		}
		else
		{
			$Form = & $params['Form'];
			if( ! isset($params['form_use_fieldset']) || $params['form_use_fieldset'] )
			{
				$Form->begin_fieldset();
			}

			// Remove any hidden fields which we use ourself as hidden (especially *_key, which is an INPUT):
			// (This is required in the login form for example)
			foreach( $Form->hiddens as $k => $v )
			{
				if( strpos( $v, 'name="'.$prefix.'_' ) )
				{ // exclude own hidden field (added by Form::hiddens_by_key())
					unset($Form->hiddens[$k]);
				}
			}

		}

		$Form->hidden( $prefix.'_key', $this->public_key );

		$img_src = $this->get_htsrv_url('display_captcha', array('pubkey'=>$this->public_key));
		$captcha_img = '<img src="'.$img_src.'"  alt="'.$this->T_('This is a captcha-picture. It is used to prevent mass-access by robots.').'" id="'.$prefix.'_'.$this->public_key.'"';
		if( ! $this->post_process_alters_dimensions() )
		{
			list($width, $height) = $this->get_image_dimensions($this->private_key);
			$captcha_img .= ' width="'.$width.'" height="'.$height.'"';
		}
		$captcha_img .= ' />';

		// Javascript link to reload the image:
		$captcha_img .= '<script type="text/javascript">
			//<![CDATA[
			document.write( \' <a href="#" onclick="document.getElementById(\\\''.$prefix.'_'.$this->public_key.'\\\').src = \\\''.$img_src.'&amp;reload=\\\'+(new Date()).getTime(); return false;">'
				.get_icon('reload', 'imgtag', array('alt'=>$this->T_('Reload'), 'title'=>$this->T_('Reload image!'))).'<\/a>\' );
			//]]>
			</script>
			';
		$captcha_img .= "<br />\n";

		$note = $this->T_( 'Please enter the characters from the image above.' )
			.' '.( $this->Settings->get('case_sensitive') ? '('.$this->T_('case sensitive').')' : '('.$this->T_('case insensitive').')' );

		global $app_version;
		if( version_compare($app_version, '2.0-dev', '>=') )
		{ // b2evo 2.0 or above (API changed):
			$Form->text_input( $prefix.'_private', '', 7, $this->T_('Captcha'), $note, array( 'maxlength' => '', 'id' => $prefix.'_'.$this->public_key.'_private', 'input_prefix' => $captcha_img ) );
		}
		else
		{
			$Form->text_input( $prefix.'_private', '', 7, $this->T_('Captcha'), array( 'note' => $note, 'maxlength' => '', 'id' => $prefix.'_'.$this->public_key.'_private', 'input_prefix' => $captcha_img ) );
		}

		if( ! isset($params['Form']) )
		{ // there's no Form where we add to, but our own form:
			$Form->end_form( array( array( 'submit', 'submit', $this->T_('Validate me'), 'ActionButton' ) ) );
		}
		else
		{
			if( ! isset($params['form_use_fieldset']) || $params['form_use_fieldset'] )
			{
				$Form->end_fieldset();
			}
		}

		return true;
	}


	/**
	 * We display our captcha with comment forms.
	 */
	function DisplayCommentFormFieldset( & $params )
	{
		if( ! $this->does_apply( $params ) )
		{
			return;
		}

		$prefix = 'captcha_img_'.$this->ID;
		$params['prefix'] = & $prefix;

		if( $v = $this->session_get('validated_in_preview') )
		{ // Captcha has been validated in preview: insert just the hidden fields:
			$this->session_delete('validated_in_preview');
			$this->debug_log('DisplayCommentFormFieldset: validated in preview');
			$params['Form']->hidden($prefix.'_key', $v['posted_public']);
			$params['Form']->hidden($prefix.'_private', $v['posted_private']);
			return;
		}

		$this->CaptchaPayload( $params );
	}


	/**
	 * Validate the given private key against our stored one.
	 *
	 * In case of error we add a message of category 'error' which prevents the comment from
	 * being posted.
	 */
	function BeforeCommentFormInsert( & $params )
	{
		if( ! $this->does_apply($params) )
		{
			return;
		}

		$prefix = 'captcha_img_'.$this->ID;
		$params['prefix'] = & $prefix;

		if( ! empty($params['is_preview'])
			|| ( isset($params['action']) && $params['action'] == 'preview' ) /* b2evo 1.10+ */ )
		{
			/*
			 * If this is a comment preview, check if the captcha has been validated and remember it then, so
			 * that {@link DisplayCommentFormFieldset()} can insert it just as hidden fields.
			 */
			if( $this->CaptchaValidated($params) )
			{
				$this->debug_log( 'CommentFormSent: validated in preview' );
				$this->session_set('validated_in_preview', array(
						'posted_public' => $this->posted_public,
						'posted_private' => $this->posted_private,
					), 30 /* 30 seconds */);
			}
			return;
		}

		if( $this->CaptchaValidated($params) === false )
		{
			global $Request;

			$validate_error = $params['validate_error'];
			if( isset($Request) )
			{
				$Request->param_error( $prefix.'_private', sprintf( /* TRANS: %s gets replaced by the reason/hint */ $this->T_('The captcha code was invalid: %s'), $validate_error ) );
			}
			else
			{ // b2evo 2.0:
				param_error( $prefix.'_private', sprintf( /* TRANS: %s gets replaced by the reason/hint */ $this->T_('The captcha code was invalid: %s'), $validate_error ) );
			}
		}
	}


	/**
	 * Cleanup after a comment has been inserted.
	 */
	function AfterCommentFormInsert()
	{
		$this->CaptchaValidatedCleanup();
	}


	/**
	 * We display our captcha with the register form.
	 */
	function DisplayRegisterFormFieldset( & $params )
	{
		if( ! $this->does_apply( $params ) )
		{
			return;
		}

		$this->CaptchaPayload( $params );
	}


	/**
	 * Validate the given private key against our stored one.
	 *
	 * In case of error we add a message of category 'error' which prevents the
	 * user from being registered.
	 */
	function RegisterFormSent( & $params )
	{
		$this->BeforeCommentFormInsert( $params ); // we do the same as when validating comment forms
	}


	/**
	 * Cleanup captcha data.
	 */
	function AfterUserRegistration( & $params )
	{
		$this->CaptchaValidatedCleanup();
	}


	/**
	 * We display our captcha with the message form.
	 */
	function DisplayMessageFormFieldset( & $params )
	{
		if( ! $this->does_apply( $params ) )
		{
			return;
		}

		$this->CaptchaPayload( $params );
	}


	/**
	 * Validate the given private key against our stored one.
	 *
	 * In case of error we add a message of category 'error' which prevents the
	 * user from being registered.
	 */
	function MessageFormSent( & $params )
	{
		$this->BeforeCommentFormInsert( $params ); // we do the same as when validating comment forms
	}


	/**
	 * Cleanup.
	 */
	function MessageFormSentCleanup()
	{
		$this->CaptchaValidatedCleanup();
	}


	/**
	 * Called, if a public trackback address gets displayed: we require the user
	 * to solve a captcha first, before he sees a one-time URL.
	 *
	 * This gets checked in {@link captcha_img_plugin::BeforeTrackbackInsert()}.
	 *
	 * @return true|NULL NULL, if we do not protect trackback URLs
	 */
	function DisplayTrackbackAddr( & $params )
	{
		if( ! $this->Settings->get('protect_trackback_url') )
		{
			return;
		}

		if( ! $this->does_apply($params) || $this->CaptchaValidated($params) )
		{ // either we do not apply or the captcha has been validated:
			global $DB;

			$wl_key = generate_random_key(32);

			$DB->query('
				INSERT INTO '.$this->get_sql_table('trackbacks_wl').'
					( tbwl_key, tbwl_item_ID )
					VALUES ( "'.$wl_key.'", '.$params['Item']->ID.' )' );

			$url = url_add_param( $params['Item']->get_trackback_url(),
				'wlkey_'.$this->ID.'='.$wl_key );

			// Display URL:
			echo str_replace( '%url%', $url, $params['template'] );
		}
		else
		{
			// TODO: Provide fieldset with explanation
			$Form = new Form( regenerate_url() );
			$Form->switch_layout('inline');
			$Form->begin_form();
			$params['Form'] = & $Form;
			$params['form_use_fieldset'] = false;
			$r = $this->CaptchaPayload($params);

			$Form->button( array( 'submit', 'submit', $this->T_('Display trackback URL'), 'ActionButton' ) );
			$Form->hiddens_by_key( get_memorized() );
			$Form->hidden('redir', 'no'); // prevent canonical redirect
			$Form->end_form();
		}

		return true;
	}


	/**
	 * Check if the URL is a generated one-time address, from {@link captcha_img_plugin::DisplayTrackbackAddr()}.
	 *
	 * We add a message of category "error", in case the URL is invalid and delete
	 * the one-time key from our DB table in case of success.
	 *
	 * @return NULL
	 */
	function BeforeTrackbackInsert( & $params )
	{
		global $DB;

		if( ! $this->Settings->get('protect_trackback_url') )
		{
			return;
		}

		$wl_key = param( 'wlkey_'.$this->ID, 'string', '', true );

		if( strlen($wl_key) != 32 )
		{
			$this->msg( $this->T_('Invalid trackback URL!'), 'error' );
		}
		elseif( ! $DB->get_var( '
				SELECT COUNT(*) FROM '.$this->get_sql_table('trackbacks_wl').'
				 WHERE tbwl_key = '.$DB->quote($wl_key).'
				   AND tbwl_item_ID = '.$params['Comment']->Item->ID ) )
		{
			$this->msg( $this->T_('Invalid key in trackback URL!'), 'error' );
		}
		else
		{ // OK. Delete whitelist key:
			$DB->query( '
				DELETE FROM '.$this->get_sql_table('trackbacks_wl').'
				 WHERE tbwl_key = '.$DB->quote($wl_key).'
				   AND tbwl_item_ID = '.$params['Comment']->Item->ID );
		}
	}


	/**
	 * Display link to test the Plugin.
	 */
	function PluginSettingsEditDisplayAfter( & $params )
	{
		global $current_User;

		echo '<div class="input"><a target="b2evo_plug'.$this->ID.'_test" href="'.$this->get_htsrv_url( 'test_page',
			array( 'test_captcha_valid' => md5($this->Settings->get('public_key_salt').$current_User->ID) ) ).'">'
			.$this->T_('Create test image... (please save any changes before)').'</a></div>';
	}


	/**
	 * Delete obsolete captcha data from DB table.
	 *
	 * Gets called as a shutdown function.
	 *
	 * @todo Optimize table from time to time
	 * @see PluginInit()
	 */
	function purge_obsolete_db_data()
	{
		global $DB, $localtimenow;

		// Trackback whitelist keys:
		$DB->query( '
				DELETE FROM '.$this->get_sql_table('trackbacks_wl').'
				 WHERE UNIX_TIMESTAMP(tbwl_timestamp) < '.( $localtimenow - $this->Settings->get('timeout_key')*60 ) );

		// Captcha data:
		$DB->query( '
				DELETE FROM '.$this->get_sql_table('data').'
				 WHERE UNIX_TIMESTAMP(cpt_timestamp) < '.( $localtimenow - $this->Settings->get('timeout_key')*60 ) );
	}


	/**
	 * A htsrv method to display the captcha image.
	 *
	 * This gets included through {@link get_htsrv_url()} in {@link CaptchaPayload()}.
	 *
	 * The private key to display in the image gets retrieved from the user's Session.
	 *
	 * @param array
	 *    'pubkey': display the captcha for the given public key (where the private part is stored in the user's session)
	 */
	function htsrv_display_captcha( $params )
	{
		global $DB, $Session;

		if( ! isset($params['pubkey']) )
		{
			$this->debug_log( 'Public key not provided!' );
			return false;
		}

		$this->debug_log( 'Public key: '.$params['pubkey'] );
		$private_key = $DB->get_var( '
				SELECT cpt_private FROM '.$this->get_sql_table('data').'
				 WHERE cpt_public = '.$DB->quote( $params['pubkey'] ).'
				   AND cpt_sess_ID = '.$Session->ID );
		if( empty($private_key) )
		{
			$this->debug_log( 'Private key is empty!' );
			return false;
		}

		$image = $this->get_captcha_img( $private_key );

		if( ! $image )
		{
			$this->debug_log( 'get_captcha_img() returned '.var_export($image, true) );
			return false;
		}

		header( 'Content-Type: image/jpeg' );
		header( 'Content-Length: '.strlen($image) );
		echo $image;
		die;
	}


	/**
	 * Output the HTML page in test mode.
	 *
	 * This displays a test image and the Debuglog of it's creation.
	 *
	 * @param array
	 *    'test_captcha_valid': used to check if the user is allowed to test the captcha plugin
	 *    'test_private_key': private key to use for testing (this is passed, as sometimes a browser might request the image twice)
	 *    'test_output_ID': if given, it's used as ID to store Debuglog output into the user's session
	 */
	function htsrv_test_page( & $params )
	{
		global $current_User;

		// Display all errors:
		$old_error_reporting = error_reporting(E_ALL);
		$old_display_errors = ini_set('display_errors', 'on');

		$output_ID = mt_rand();

		if( ! isset($current_User->ID) // no user logged in
			|| empty( $params['test_captcha_valid'] ) || $params['test_captcha_valid'] != md5($this->Settings->get('public_key_salt').$current_User->ID) )
		{
			return false;
		}

		// create Debuglog anew, as it will be of class Log_noop if !$debug - do not assign by reference as it fails to overwrite it and is deprecated in PHP5 anyway
		global $Debuglog;
		$Debuglog = new Log( 'note' );

		$this->debug_log( 'params: '.var_export($params, true) );

		$private_key = $this->generate_private_key();
		$image = $this->get_captcha_img( $private_key );
		$image = base64_encode($image); // workaround, because sess_data is not binary (BLOB before b2evo 1.10)

		$this->session_set( 'test_img_'.$output_ID, $image, 30, true ); // timeout: 30s and save immediately
		unset($image);
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html>
			<head>
				<title>Test: <?php echo $this->classname ?>, ID <?php echo $this->ID ?></title>
			</head>

			<body>
				<div style="text-align:center">
					<p><?php echo $this->T_('A generated image should show up below. The image only gets displayed once - use the test link again for a new try.') ?></p>
					<?php
					// Callback to get the image (saved into session above):
					echo '<img src="'.$this->get_htsrv_url( 'test_page_img', array(
						'test_captcha_valid' => $params['test_captcha_valid'],
						'test_output_ID' => $output_ID ) )
						.'" alt="Captcha test image" style="border:2px solid black; padding:10px;" ';
					if( ! $this->post_process_alters_dimensions() )
					{
						list($width, $height) = $this->get_image_dimensions($private_key);
						echo ' width="'.$width.'" height="'.$height.'"';
					}
					echo ' />';
					?>
				</div>

				<div class="debug">
				<h2>Debuglog for image creation</h2>
				<?php
				$Debuglog->display(
					array( 'all' => array( 'string' => '<h4 id="debug_info_cat_%s">%s:</h4>', 'template' => false ) ),
					'', true, array( $this->classname.'_'.$this->ID, 'all' ) )
				?>
				</div>

				<?php
				debug_info();
				?>
			</body>
		</html>

		<?php
		error_reporting($old_error_reporting);
		ini_set('display_errors', $old_display_errors);
	}


	/**
	 * (External) helper for {@link htsrv_test_page()}.
	 *
	 * Display the captcha image in test mode. This gets requested through a HTTP callback (out of an IMG html tag).
	 *
	 * @param array Passed from {@link htsrv_test_page()}:
	 *          'test_captcha_valid': key to validate request
	 *          'test_output_ID': output ID to get the image out of session data
	 */
	function htsrv_test_page_img( $params )
	{
		global $Session, $current_User;

		// Check perms:
		if( empty($params['test_output_ID']) || empty( $params['test_captcha_valid'] ) )
		{
			debug_die( 'Invalid request!' );
		}
		if( empty($current_User)
			|| $params['test_captcha_valid'] != md5($this->Settings->get('public_key_salt').$current_User->ID) )
		{
			debug_die( 'Invalid request (not authenticated)!' );
		}

		// wait max. 5 seconds for the image to arrive in session data, normally it should be there on the first try.
		$count = 0;
		while( ! ( $image = $this->session_get( 'test_img_'.$params['test_output_ID'] ) ) && $count < 5 )
		{
			$count++;
			sleep(1);
			$Session->reload_data();
		}
		$this->session_delete( 'test_img_'.$params['test_output_ID'] ); // delete not needed data

		$image = base64_decode($image);
		if( empty($image) )
		{
			debug_die( 'Failed to get test image from session data!' );
		}

		header( 'Content-Type: image/jpeg' );
		echo $image;

		exit();
	}


	/* PRIVATE methods */

	/**
	 * Checks if we should captcha the current request, according to the settings made.
	 *
	 * @param array Associative array of parameters.
	 *        'Item': if we're in the context of an Item.
	 *        'Comment': if we're in the context of a Comment.
	 * @return boolean
	 */
	function does_apply( $params )
	{
		global $current_User;

		if( ! isset($current_User) || ! is_a( $current_User, 'user' ) )
		{ // Anonymous user:
			return $this->Settings->get( 'use_for_anonymous' );
		}

		if( isset($params['Item']) )
		{ // Item context
			$tmp_Blog = & $params['Item']->get_Blog();
		}
		elseif( isset($params['Comment']) )
		{ // Comment context (which has an Item as parent):
			$tmp_Blog = & $params['Comment']->Item->get_Blog();
		}

		if( ! empty( $tmp_Blog ) )
		{ // we're in Blog context, apply the use_for_members_level_below setting:
			if( $current_User->check_perm('blog_ismember', 'any', false, $tmp_Blog->ID) )
			{ // User is member of the blog
				return ( $current_User->level < $this->Settings->get('use_for_members_level_below') );
			}
		}

		return ( $current_User->level < $this->Settings->get('use_for_level_below') );
	}


	/**
	 * Generate a private key.
	 *
	 * @access private
	 * @return string
	 */
	function generate_private_key()
	{
		$key = '';
		$minchars = $this->Settings->get( 'minchars' );
		$maxchars = $this->Settings->get( 'maxchars' );
		$validchars = $this->Settings->get( 'validchars' );

		$number_of_chars = mt_rand( $minchars, $maxchars );
		$validchars_max_index = strlen($validchars)-1;
		for( $i = 0; $i < $number_of_chars; $i++ )
		{
			$key .= $validchars{mt_rand(0, $validchars_max_index)};
		}

		if( ! $this->Settings->get('case_sensitive') )
		{
			$key = strtoupper($key);
		}

		return $key;
	}


	/**
	 * Get image dimensions of the original image (before any post-processing!).
	 * @param string private key
	 * @return array (width, height)
	 */
	function get_image_dimensions($private_key)
	{
		$min_fontsize = $this->Settings->get('min_fontsize');
		$max_fontsize = $this->Settings->get('max_fontsize');

		// TODO: make this (more) configurable..?!
		$width = (strlen($private_key) + 1) * (int)(($max_fontsize + $min_fontsize) / 1.5);
		$height = (int)(2.4 * $max_fontsize);
		return array($width, $height);
	}


	/**
	 * Create captcha image.
	 *
	 * @access private
	 * @return string The generated captcha image (binary data).
	 */
	function get_captcha_img( $private_key )
	{
		$this->debug_log( 'Private key: ['.$private_key.']' );
		$this->load_fonts();

		$min_fontsize = $this->Settings->get('min_fontsize');
		$max_fontsize = $this->Settings->get('max_fontsize');

		// set dimension of image
		list( $this->lx, $this->ly ) = $this->get_image_dimensions($private_key);
		$this->debug_log( 'Set image dimension to: ('.$this->lx.' x '.$this->ly.')' );

		$this->use_websafecolors = $this->Settings->get('use_websafecolors');


		// set number of noise-chars for background if it is enabled
		$nb_noise = $this->Settings->get('noise') ? ( strlen($private_key) * $this->Settings->get('noisefactor')) : 0;
		$this->debug_log( 'Number of noise characters: '.$nb_noise );

		// seed the random number generator if less than php 4.2.0
		if( ! function_exists('version_compare') || version_compare( PHP_VERSION, '4.2.0', '< ') )
		{
			mt_srand((double)microtime()*1000000);
		}

		$gd_version = $this->get_gd_version();

		// create Image and set the apropriate function depending on GD-Version & websafecolor-value
		if( $gd_version >= 2 && ! $this->use_websafecolors )
		{
			$func_create = 'imagecreatetruecolor';
			$func_alloc = 'imagecolorallocate';
		}
		else
		{
			$func_create = 'imageCreate';
			$func_alloc = 'imagecolorclosest';
		}
		$image = $func_create( $this->lx, $this->ly );
		$this->debug_log( 'Generate ImageStream with: '.$func_create.'()' );
		$this->debug_log( 'For colordefinitions we use: '.$func_alloc.'()' );


		// Set Backgroundcolor
		$this->random_color(224, 255);
		$this->bg_color = array( $this->rand_R, $this->rand_G, $this->rand_B ); // used for substitution in post_process_image()
		$back =  imagecolorallocate($image, $this->rand_R, $this->rand_G, $this->rand_B);
		imagefilledrectangle($image,0,0,$this->lx,$this->ly,$back);
		$this->debug_log( 'We allocate one color for Background: ('.$this->rand_R.'-'.$this->rand_G.'-'.$this->rand_B.')' );

		// allocates the 216 websafe color palette to the image
		if($gd_version < 2 || $this->use_websafecolors)
		{
			$this->makeWebsafeColors( $image );
		}

		// fill with noise or grid
		if( $nb_noise )
		{ // random characters in background with random position, angle, color
			$this->debug_log( 'Fill background with noise' );

			$validchars = $this->Settings->get( 'validchars' );
			$validchars_max_index = strlen($validchars)-1;

			for( $i = 0; $i < $nb_noise; $i++ )
			{
				$size  = intval(mt_rand((int)($min_fontsize / 2.3), (int)($max_fontsize / 1.7)));
				$angle = intval(mt_rand(0, 360));
				$x     = intval(mt_rand(0, $this->lx));
				$y     = intval(mt_rand(0, (int)($this->ly - ($size / 5))));
				$this->random_color(160, 224);
				$color = $func_alloc($image, $this->rand_R, $this->rand_G, $this->rand_B);
				$text  = $validchars{mt_rand(0, $validchars_max_index)};
				ImageTTFText($image, $size, $angle, $x, $y, $color, $this->change_TTF(), $text);
			}
		}
		else
		{ // generate grid
			$this->debug_log( 'Fill background with x-gridlines: ('.(int)($this->lx / (int)($min_fontsize / 1.5)).')' );
			for( $i = 0; $i < $this->lx; $i += (int)($min_fontsize / 1.5) )
			{
				$this->random_color(160, 224);
				$color = $func_alloc($image, $this->rand_R, $this->rand_G, $this->rand_B);
				imageline($image, $i, 0, $i, $this->ly, $color);
			}
			$this->debug_log( 'Fill background with y-gridlines: ('.(int)($this->ly / (int)(($min_fontsize / 1.8))).')' );
			for( $i = 0 ; $i < $this->ly; $i += (int)($min_fontsize / 1.8) )
			{
				$this->random_color(160, 224);
				$color	= $func_alloc($image, $this->rand_R, $this->rand_G, $this->rand_B);
				imageline($image, 0, $i, $this->lx, $i, $color);
			}
		}

		// generate Text
		$max_rotation = $this->Settings->get('max_rotation');
		$this->debug_log( 'Fill foreground with chars and shadows.' );
		for( $i = 0, $x = intval(mt_rand($min_fontsize,$max_fontsize)); $i < strlen($private_key); $i++ )
		{
			$text  = substr($private_key, $i, 1);
			$angle = intval(mt_rand(($max_rotation * -1), $max_rotation));
			$size  = intval(mt_rand($min_fontsize, $max_fontsize));
			$y     = intval(mt_rand((int)($size * 1.5), (int)($this->ly - ($size / 7))));
			$this->random_color(0, 127);
			$color =  $func_alloc($image, $this->rand_R, $this->rand_G, $this->rand_B);
			$this->random_color(0, 127);
			$shadow = $func_alloc($image, $this->rand_R + 127, $this->rand_G + 127, $this->rand_B + 127);
			$this->change_TTF();
			$this->debug_log( 'Using font "'.basename($this->TTF_file).'" for letter "'.$text.'".' );
			ImageTTFText($image, $size, $angle, $x + (int)($size / 15), $y, $shadow, $this->TTF_file, $text);
			ImageTTFText($image, $size, $angle, $x, $y - (int)($size / 15), $color, $this->TTF_file, $text);
			$x += (int)($size + ($min_fontsize / 5));
		}

		ob_start();
		ImageJPEG($image, NULL, $this->Settings->get('jpegquality'));
		$image_data = ob_get_contents();
		ob_end_clean();
		ImageDestroy($image);

		$this->post_process_image( $image_data );

		return $image_data;
	}


	/**
	 * Post process the image, if a post_process_cmd is given.
	 *
	 * @param string (binary) Image to post-process (by reference)
	 * @return string In case of error string, NULL otherwise.
	 */
	function post_process_image( & $image )
	{
		$post_process_cmd = $this->Settings->get( 'post_process_cmd' );
		if( ! strlen($post_process_cmd) )
		{
			return;
		}
		$post_process_cmd = preg_replace_callback(
			array(
				'~%(rand)\(\s*(\d+),(\d+)\)%~',  # rand(min,max)
				'~%(arand)\(([^)]+)\)%~',        # arand(list,of,items,to,randomly,choose,from)
				'~%(rgb)\((bg)\)%~',             # rgb(bg): return background color as "R,G,B"
				),
				create_function( '$match', '
					switch( $match[1] )
					{
						case \'rand\':
							return mt_rand( $match[2], $match[3] );

						case \'arand\':
							$options = preg_split( \'~|~\', $match[2] );
							return $options[array_rand($options)];

						case \'rgb\':
							switch( $match[2] )
							{
								case \'bg\':
									return \''.implode( ',', $this->bg_color ).'\';
							}
					}' ),
				$post_process_cmd );

		$this->debug_log( 'Post-process cmd: '.$post_process_cmd );

		$post_process_cmd = escapeshellcmd( $post_process_cmd );

		$descriptor_spec = array(
				0 => array('pipe', 'r'), // child reads from stdin
				1 => array('pipe','w'),  // child writes to stdout
				2 => array('pipe','w'),  // child writes to stderr
			);
		$process = proc_open( $post_process_cmd, $descriptor_spec, $pipes );
		if( is_resource( $process ) )
		{
			fwrite( $pipes[0], $image );
			fclose( $pipes[0] );
			$this->debug_log( 'Written '.strlen($image).' bytes to post-process (stdin).' );

			$stderr = '';
			while( ! feof( $pipes[2] ) )
			{
				$stderr .= fread( $pipes[2], 8192 );
			}
			$this->debug_log( 'Stderr returned: '.$stderr );

			if( empty($stderr) )
			{
				$image = '';
				while( ! feof( $pipes[1] ) )
				{
					$image .= fread( $pipes[1], 8192 );
				}
				fclose( $pipes[1] );
				$this->debug_log( 'Read '.strlen($image).' bytes from post-process (stdout).' );
			}
			else
			{
				$error = 'Post-processing error: '.$stderr;
				$this->debug_log( $error );
				return $error;
			}
		}
		else
		{
			$error = 'Post-process did not return valid resource!';
			$this->debug_log( $error );
			return $error;
		}

		#var_dump( $stdin, $pipes, $image );
	}


	/**
	 * Get the GD version out of buffered phpinfo output.
	 *
	 * @access private
	 * @return string|0 Either the parsed version or 0 if no GD library available.
	 */
	function get_gd_version()
	{
		static $gd_version_number;

		if( ! isset($gd_version_number) )
		{
			ob_start();
			phpinfo(8);
			$module_info = ob_get_contents();
			ob_end_clean();
			if( preg_match('/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i', $module_info, $matches) )
			{
				$gd_version_number = $matches[1];
			}
			else
			{
				$gd_version_number = 0;
			}
		}
		return $gd_version_number;
	}


	/** @private **/
	function makeWebsafeColors(&$image)
	{
		//$a = array();
		for($r = 0; $r <= 255; $r += 51)
		{
			for($g = 0; $g <= 255; $g += 51)
			{
				for($b = 0; $b <= 255; $b += 51)
				{
					$color = imagecolorallocate($image, $r, $g, $b);
					//$a[$color] = array('r'=>$r,'g'=>$g,'b'=>$b);
				}
			}
		}
		$this->debug_log( 'Allocate 216 websafe colors to image: ('.imagecolorstotal($image).')' );
		//return $a;
	}


	/** @private **/
	function random_color($min,$max)
	{
		$this->rand_R = intval(mt_rand($min,$max));
		$this->rand_G = intval(mt_rand($min,$max));
		$this->rand_B = intval(mt_rand($min,$max));
		//echo ' ('.$this->rand_R.'-'.$this->rand_G.'-'.$this->rand_B.') ';
	}


	/** @private **/
	function change_TTF()
	{
		$key = array_rand($this->TTF_RANGE);
		$this->TTF_file = $this->TTF_RANGE[$key];

		return $this->TTF_file;
	}


	/**
	 * Check if GD is available with our requirements.
	 *
	 * @access private
	 */
	function validate_gd_requirements()
	{
		if( ! $this->get_gd_version() )
		{
			return $this->T_( 'The GD library does not seem to be installed.' );
		}
		if( !function_exists('imagejpeg') )
		{
			return $this->T_( 'No JPEG support. (Function imagejpeg does not exist)' );
		}
		if( !function_exists('imagettftext') )
		{
			return $this->T_( 'FreeType library not available. (Function imagettftext does not exist)' );
		}
	}


	/**
	 * Load fonts.
	 *
	 * @return string|NULL String in case of error, NULL otherwise.
	 */
	function load_fonts( $use_folder = NULL )
	{
		static $loaded = array();

		if( ! isset($use_folder) )
		{
			$use_folder = $this->Settings->get( 'TTF_folder' );
		}

		$this->TTF_folder = dirname(__FILE__).'/'.$use_folder;

		if( isset($loaded[$use_folder]) )
		{
			$this->TTF_RANGE = $loaded[$use_folder]['TTF_RANGE'];
			return $loaded[$use_folder]['return'];
		}

		$this->TTF_RANGE = array();

		if( $handle = @opendir($this->TTF_folder) )
		{
			while( false !== ($file = readdir($handle)) )
			{
				if( $file != '.' && $file != '..' && preg_match( '~\.ttf$~i', $file ) )
				{
					$this->TTF_RANGE[] = $this->TTF_folder.$file;
					$this->debug_log( 'Found font file ('.$this->TTF_folder.$file.')' );
				} else {
					$this->debug_log( 'Skipping non-font file ('.$this->TTF_folder.$file.')' );
				}
			}
			closedir($handle);
		}
		else
		{
			$error = sprintf( $this->T_( 'Fonts folder %s is not readable or does not exist!' ), rel_path_to_base($this->TTF_folder) );
			$this->debug_log( $error );
			$loaded[$use_folder]['TTF_RANGE'] = $this->TTF_RANGE;
			$loaded[$use_folder]['return'] = $error;
			return $error;
		}

		if( ! empty($this->TTF_RANGE) )
		{
			$this->debug_log( 'Checking given TrueType-Array: ('.count($this->TTF_RANGE).')' );
			$temp = array();
			foreach( $this->TTF_RANGE as $k => $v )
			{
				if( ! is_readable($v) )
				{
					$this->debug_log( 'TrueTypeFont '.$v.' is not readable!' );
					unset( $this->TTF_RANGE[$k] );
				}
			}
			$this->debug_log( 'Valid TrueType-files: ('.count($this->TTF_RANGE).')' );
		}

		if( empty( $this->TTF_RANGE ) )
		{
			$error = $this->T_('No Truetype fonts available!');
			$this->debug_log( $error );
			$loaded[$use_folder]['return'] = $error;
		}
		else
		{ // ok
			$loaded[$use_folder]['return'] = NULL;
		}

		$loaded[$use_folder]['TTF_RANGE'] = $this->TTF_RANGE;
		return $loaded[$use_folder]['return'];
	}


	/**
	 * Do we use post-processing for images?
	 * @todo Make this a setting!
	 * @return bool
	 */
	function post_process_alters_dimensions()
	{
		return strlen($this->Settings->get('post_process_cmd'));
	}
}

?>
