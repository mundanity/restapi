Major
=====

- switch to PSR-7
- finish tests for Api

- do we need a plugin framework? or are the hooks good enough?
- add a CORS plugin
- add a plugin to provide metadata in the response? (or should this be default?)
- stop using access callback, as they're used for visibility as well as access 
? add debug info when debug flag is set
? provide more info when called in a browser context.

- figure out versioning
- add timing info on how hooks are handled?

Minor
=====

- should AbstractResource encapsulate internal requests? e.g. AbstractResource::call($method, $path, $data) ? If so it should clone
the request to ensure the request variables / method do not change from a call to call basis.
- do we need a wrapper around restapi_access_callback()?
- add a hook for after AbstractResource::access()?
