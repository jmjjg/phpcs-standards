<?php
/**
 *
 */
App::uses( 'AbstractAppUsesWrongTypeDifferent1Component', 'Controller/Component' );
App::uses( 'AbstractAppUsesWrongTypeDifferent1Component', 'Plugin.Controller/Component' );
App::uses( 'AbstractAppUsesWrongTypeDifferent1Component', 'Controller' );

/**
 *
 */
class AppUsesWrongTypeDifferent1Component extends AbstractAppUsesWrongTypeDifferent1Component
{
}