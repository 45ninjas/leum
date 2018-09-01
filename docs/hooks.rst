Hooks
=====

An object subscribes to a hook event.
When the hook is invoked, every subscriber to that hook is invoked.

Anything can invoke, create or add to a hook event. Generally, if you did not create the hook, don't invoke it.

Other than a few important hooks, hooks are formatted as ``[source].[type].[context]``. For example ``leum.media.scan-new`` means leum found new media to scan.

Internally leum stores hooks names as the key to an array (just a string). However later this might change and require more precise control. Hence the future proofing.

Hook events do not currently pass arguments.

To register a hook use ``LeumCore\:\:AddHook()`` and to Invoke a hook call ``LeumCore\:\:InvokeHook()``.


------------------------

Leum Hooks
----------

initialize
	Invoked after leum is up and running. **Note:** this is after routes are created and resolved.
leum.front.footer
	Invoked after the footer is outputted.
leum.front.header
	Invoked after the header is outputted.