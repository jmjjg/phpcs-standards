<?php
/**
 *
 */
App::uses( 'Component', 'Controller' );
App::uses( 'MyPluginController', 'MyPlugin.Controller' );

/**
 *
 */
class AppUsesCorrectType1Component extends Component
{
}

/**
 *
 */
class AppUsesCorrectType1Controller extends MyPluginController
{
}