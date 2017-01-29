# About
Titans-Inc/CORS enables you to send Cross-Origin Resource Sharing headers. This extenstion is heavily inspired by [NelmioCorsBundle](https://github.com/nelmio/NelmioCorsBundle)

# Features
+ Handles CORS preflight OPTIONS requests
+ Adds CORS headers to your responses

# How to Use
There are two menus in CORS **Paths** and **Settings**. 

### Settings
**Settings** has options for default configuration of CORS which will be applied to any Cross-Origin Resource which do not match with a path (regex) from **Paths**. 

### Paths
Each *path* has the same option exactly as **Settings** with one more option i.e., `Path` which stores a regex and during `request` event matches with the **URI**. If the match is truthy the configurations of that path override the default configuration for that *request*.

# Options
+ `Allow Credentials (boolean)`: It sets the **Access-Control-Allow-Credentials**. The **Access-Control-Allow-Credentials** response header indicates whether or not the response to the request can be exposed to the page. It can be exposed when the true value is returned; it can't in other cases. Credentials are *cookies, authorization headers or TLS client certificates*. 
+ `Origin Regex (boolean)`: With this enabled, you can add regexes to `Allow Origin` instead of plain strings.
+ `Max Age (integer)[Seconds]`: Indicates in seconds for how long the results of a preflight request can be cached. But it is not wise to rely on getting the exact duration. For more info, [Read this](http://stackoverflow.com/a/23549398/1525163)
+ `Force Allow Origin (String)`: If this is set to a particular origin, then every CORS request will be forced to send **Access-Control-Allow-Origin** header with the *Force Allow Origin* value.
+ `Allow Origin (Array)`: An array of origins that will be matched with `Origin` header of a CORS request and if any does match **Access-Control-Allow-Origin** header will be set to it, letting the browser know that the `Origin` can make api requests to it.
+ `Allow Methods (Array)`: An array of methods i.e., POST, GET, PUT ... which will set **Access-Control-Request-Methods**, which tells the browser that only request using these methods are allowed to go through.
+ `Allow Headers (Array)`: An array of standard/custom headers which will set **Access-Control-Request-Headers**, which tells the browser that only these headers are allowed to go through.
+ `Expose Headers (Array)`: An array of headers which will set **Access-Control-Expose-Headers**. **The Access-Control-Expose-Headers** response header indicates which headers can be exposed as part of the response by listing their names.
+ `Hosts (Array)`: It is a collection of domains which is matched against the requests `Host` header. This sets no respose headers but is simply an extra step of precaution and security.
