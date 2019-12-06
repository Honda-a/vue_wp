=== WordPress OpenID Connect Client ===
Contributors: cyberlord92
Tags: openid, sso, openid connect, openid connect client, oidc
Requires at least: 3.0.1
Tested up to: 5.1
Stable tag: 1.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

OpenID Connect Client plugin allows Single Sign On (SSO) with any OpenID provider that conforms to the OpenID Connect 1.0 standard.

== Description ==

This plugin works with any OpenID provider that conforms to the OpenID Connect 1.0 standard. OpenID Connect 1.0 is a simple identity layer on top of the OAuth 2.0 protocol. It allows Clients to verify the identity of the End-User based on the authentication performed by an Authorization Server, as well as to obtain basic profile information about the End-User in an interoperable and REST-like manner. 
OpenID Connect identifies a set of personal attributes that can be exchanged between Identity Providers and the apps that use them, and includes an approval step so that users can consent (or deny) the sharing of this information.Because OpenID Connect standardizes mechanisms by which users can control the sharing of the identity that they use.

This plugin enables user registration and authentication against any standard OpenID Connect (OIDC) Provider. 

Login
This plugin enables a WordPress site to send users to an external OpenID Provider for login.

Registration
If the user has an existing account in the OpenID Connect Provider, but not in WordPress, this plugin will enable dynamic registration of the user in WordPress.

= List of popular OpenID Connect Providers we support =
*	AWS Cognito
*   Amazon
*	SalesForce
*	PayPal
*	Microsoft
*	Yahoo
*	Google
*   Onelogin
*   Okta
*   ADFS
*   Gigya

= List of popular OAuth Providers we support =
*	Eve Online
*	Slack
*	Discord
*	HR Answerlink / Support center
*	WSO2
*	Wechat
*	Weibo
*	AWS cognito
*	Azure AD
*	Gitlab
*	Shibboleth
*	Blizzard (Formerly Battle.net)
*	servicem8
*	Meetup

= Other OpenID Connect/Auth Providers we support =
*	Keycloak, Foursquare, Harvest, Mailchimp, Bitrix24, Spotify, Vkontakte, Huddle, Reddit, Strava, Ustream, Yammer, RunKeeper, Instagram, SoundCloud, Pocket, PayPal, Pinterest, Vimeo, Nest, Heroku, DropBox, Buffer, Box, Hubic, Deezer, DeviantArt, Delicious, Dailymotion, Bitly, Mondo, Netatmo, Amazon, WHMCS, FitBit, Clever, Sqaure Connect, Windows, Dash 10, Github, Invision Comminuty, Blizzar, authlete etc.


= FREE VERSION FEATURES =

*	Supports login(sso) with any 3rd party OpenID Connect server or custom OpenID Connect server.
*	Optionally Auto Register Users- Automatic user registration after login if the user is not already registered with your site
*	Attribute Mapping- Basic Attribute Mapping feature to map wordpress user profile attributes like email and first name. Manage username & email with data provided
*	OpenID Connect Provider Support- It Supports only one OpenID Connect Provider. (ENTERPRISE : Supports Multiple OpenID Connect Providers)
*	Redirect URL after Login- Automatically Redirect user after successful login. Note: Does not include custom redirect URL
*	Display Options- OpenID Connect Login Provides Display Option for both Login form and Registration form
*	Logging- If you run into issues it can be helpful to enable debug logging


= PREMIUM VERSION FEATURES =

*	All the Standard Version Features
*	Advanced Role Mapping- Assign roles to users registering through OpenID Connect/OAuth Login based on rules you define.
*	OAuth Support- Supports login with any 3rd party OAuth server.
*	Multiple Userinfo Endpoints Support- It Supports multiple Userinfo Endpoints.
*	App domain specific Registration Restrictions- Restricts registration on your site based on the person's email address domain
*	Multi-site Support- Unique ability to support multiple sites under one account
*	Reverse Proxy Support- Support for sites behind a reverse-proxy or on-prem instances with no internet access.
*	Email notifications- You can customize the E-mail templates used for the automatic email notifications related to user registration.
*	Account Linking- Supports the linking of user accounts from OpenID Connect/OAuth Providers to WordPress account.[ENTERPRISE]
*	Extended OpenID Connect/OAuth API support- Extend OpenID Connect/OAuth API support to extend functionality to the existing OpenID Connect/OAuth client.[ENTERPRISE]
*	BuddyPress Attribute Mapping- It allows BuddyPress Attribute Mapping.[ENTERPRISE]
*	Page Restriction according to roles- Limit Access to pages based on user status or roles. This WordPress plugin allows you to restrict access to the content of a page or post to which only certain group of users can access.[ENTERPRISE]
*	Login Reports- Creates user login and registration reports based on application used. [ENTERPRISE]


== Installation ==

= From your WordPress dashboard =
1. Visit `Plugins > Add New`
2. Search for `OpenID Connect Client`. Find and Install `OpenID Connect Client`
3. Activate the plugin from your plugins page

= From WordPress.org =
1. Download OpenID Connect Client.
2. Unzip and upload the `miniorange-openid-connect` directory to your `/wp-content/plugins/` directory.
3. Activate miniOrange OpenID Connect Client from your Plugins page.

= Once Activated =
1. Go to `Settings-> miniOrange OpenID Connect Client -> Configure OpenID Connect Client`, and follow the instructions
2. Go to `Appearance->Widgets` ,in available widgets you will find `miniOrange OpenID Connect Client` widget, drag it to chosen widget area where you want it to appear.
3. Now visit your site and you will see login with widget.


== Frequently Asked Questions ==
= I need to customize the plugin or I need support and help? =
Please email us at info@miniorange.com or <a href="http://miniorange.com/contact" target="_blank">Contact us</a>. You can also submit your query from plugin's configuration page.


= How to configure the applications? =
When you want to configure a particular application, you will see a Save Settings button, and beside that a Help button. Click on the Help button to see configuration instructions.


== Changelog ==

= 1.5.0 =
* Compatibility with WordPress 5.1 

= 1.4.0 =
* Added Feedback Form

= 1.3.0 =
* Updated Licensing Plan

= 1.2.0 =
* Updated the Default App List

= 1.1.0 =
* Updated the Redirect / Callback URL

= 1.0.2 =
* Updated version as per New Licensing Plan

= 1.0.1 =
* First version of plugin.
