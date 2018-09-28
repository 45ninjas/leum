Leum Core
=========

Everything that makes leum leum is in (and is included from) the ``leum/core/leum-core.php`` file.

On initialization the core does the following thins (in order)

1.
	Include the config files, core classes and plugins.
2.
	Creates a PDO object connecting to the database.
3.
	Initializes each plug-in.
4.
	Invokes the initialize hook.

Everything else is managed by ``Leum`` (soon to be replaced with ``Front``) or the ``API`` classes.

The idea of LeumCore is to make the Interface, API and CLI share a common code base with the same configuration. A single bug-fix found using the API should also fix the issue for other entry points of the application. Leum's primary focus is on it's Interface. The API is far from usable and is only used to assist the Interface at the moment.