App::uses( 'AppModel', 'Model' );
<?php
/**
 *
 */
App::uses/**/( 'Foo', 'Model' );
App::uses( 'Bar', 'Model' );
if (false) {
	App::uses( 'AppModel', 'Model' );
}

/**
 *
 */
class Boz extends Bar
{
}

/**
 *
 */
class AppUsesMissing3 extends AppModel
{
}