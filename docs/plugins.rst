Plugins with Leum reference
===========================

If the ``foobar`` plugin was to be loaded leum will look in ``leum/plugins/foobar/`` for a php file called ``foobar.php``. Leum will import that file and create a new instance of the ``foobar`` class.


The best place/time to register hooks is in the ``__construct()`` method of the class.