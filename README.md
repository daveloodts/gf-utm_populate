# Gravity Forms UTM tracker
This plugin is a Gravity Forms add-on that allows you to automatically cookie parameters passed in via the querystring and preserve them until a lead form is submitted. It's especially useful if you're tracking UTM Parameters and hoping to store the originating UTM params even if the user signs up on a subsequent page (or even subsequent visit). Cookied params are passed as hidden fields via Gravity Forms. This will also capture 2 fields implicitly on every form submit: originating HTTP Referer and the Request_URI (page from which the form was submitted).

Basically you just need to specify under Plugins > Gravity Prepopulate the comma-delim list of querystring params you're capturing and passing via hidden fields. 

Workflow.
1- under Plugins > Gravity Prepopulate; enter the parameters you want to measure
2- so mostly: utm_source,utm_campaign,utm_medium
3- add hidden fields to your form, same name as the tracking name (utm_source)
4- for every hidden field: choose the option: allow field to be populated dynamically; and set the same name (utm_source)
