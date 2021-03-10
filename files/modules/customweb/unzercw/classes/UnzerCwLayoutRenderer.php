<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_UnzerCw
 * @version		1.0.59
 */

UnzerCwHelper::bootstrap();

require_once 'Customweb/Mvc/Layout/Renderer.php';


/**
 * @Bean
 */
class UnzerCwLayoutRenderer extends Customweb_Mvc_Layout_Renderer
{
	/**
	 * @var Customweb_Mvc_Layout_IRenderContext
	 */
	private static $context = null;

	/**
	 * @return Customweb_Mvc_Layout_IRenderContext
	 */
	public static function getContext()
	{
		return self::$context;
	}

	public function render(Customweb_Mvc_Layout_IRenderContext $context) {
		self::$context = $context;

		global $unzercwLayoutRendererActive;
		
		$unzercwLayoutRendererActive = true;
		
		ob_start();

		$shopControl = oxNew('oxShopControl');
		$shopControl->start('unzercw_layout');

		$layout = ob_get_clean();
		$layout = UnzerCwHelper::toUtf8($layout);

		$layout = str_replace('____mainContent____', $context->getMainContent(), $layout);
		$layout = str_replace('____templateTitle____', $context->getTitle(), $layout);
	
		$unzercwLayoutRendererActive = false;
		
		return $layout;
	}
}