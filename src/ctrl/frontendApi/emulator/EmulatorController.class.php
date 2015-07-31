<?php
namespace ctrl\frontendApi\emulator;

use core\apps\FrontendApplication;
use core\http\HTTPRequest;

class EmulatorController extends \core\ApiBackController {
	protected function _emulateController($module, $action = 'index') {
		$app = new FrontendApplication;
		$emulatedController = $app->buildController($module, $action);

		return $emulatedController;
	}

	public function executeEmulate(HTTPRequest $request) {
		$emulatedController = $this->_emulateController($request->getData('emulateModule'), $request->getData('emulateAction'));

		$emulatedController->execute();

		$this->responseContent()->setData($emulatedController->responseContent()->vars());
	}

	public function executeEmulateList(HTTPRequest $request) {
		$moduleName = $request->getData('emulateModule');
		$itemsName = $request->getData('emulateListItems');

		$emulatedController = $this->_emulateController();

		$methodName = 'list' . ucfirst($itemsName);

		if (!method_exists($emulatedController, $methodName)) {
			throw new \RuntimeException('Unknown list items "'.$itemsName.'" on module "'.$moduleName.'"');
		}

		$items = $emulatedController->$methodName();

		$this->responseContent()->setData($items);
	}
}