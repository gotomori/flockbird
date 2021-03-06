<?php

return array(

	/**
	 * This will allow the same user to be logged in multiple times.
	 *
	 * Note that this is less secure, as session hijacking countermeasures have to
	 * be disabled for this to work!
	 */
	'multiple_logins' => SITE_MULTIPLE_LOGINS,

	/**
	 * Remember-me functionality
	 */
	'remember_me' => array(
		/**
		 * Whether or not remember me functionality is enabled
		 */
		'enabled' => true,

		/**
		 * Name of the cookie used to record this functionality
		 */
		'cookie_name' => 'remember_checked_on',

		/**
		 * Remember me expiration (default: 31 days)
		 */
		'expiration' => 86400 * 31,
	),

	/**
	 * Salt for the login hash
	 */
	'login_hash_salt' => FBD_ENCRYPTION_KEY,

	/**
	 * $_POST key for login email
	 */
	'username_post_key' => 'email',

	/**
	 * $_POST key for login password
	 */
	'password_post_key' => 'password',

	/**
	 * setting for account lock
	 */
	'accountLock' => array(
		'isEnabled' => true,
		'loginFailAcceptCount' => 2,
		'recoveryTime' => 60 * 1,
		'isLogging' => true,
		'isSentNotificationMail' => array(
			'member' => true,
			'admin' => array(FBD_ADMIN_MAIL),
		),
	),
);
