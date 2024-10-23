# Gravity-Forms-Prepopulate
This plugin is a Gravity Forms add-on that allows you to automatically cookie parameters passed in via the querystring and preserve them until a lead form is submitted. It's especially useful if you're tracking UTM Parameters and hoping to store the originating UTM params even if the user signs up on a subsequent page (or even subsequent visit). Cookied params are passed as hidden fields via Gravity Forms. This will also capture 2 fields implicitly on every form submit: originating HTTP Referer and the Request_URI (page from which the form was submitted).

Here is a screencast serving as loose documentation showing how to install, configure and use this plugin: http://www.screencast.com/t/KDV0Tato

Basically you just need to specify under Plugins > Gravity Prepopulate the comma-delim list of querystring params you're capturing and passing via hidden fields. 
