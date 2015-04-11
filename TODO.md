Major
=====

- stop using access callback, as they're used for visibility as well as access 
- cache should clear when changing the url prefix via the admin UI
- find a way to auto add the request ID to all watchdog calls.
? add debug info when debug flag is set
? provide more info when called in a browser context.

- add timing info on how hooks are handled?

Minor
=====

- do we need a wrapper around restapi_access_callback()?
- add a hook for after AbstractResource::access()?
