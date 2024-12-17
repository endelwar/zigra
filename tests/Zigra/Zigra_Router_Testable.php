<?php

class Zigra_Router_Testable extends \Zigra_Router
{
    public static $mockController;

    public static function callControllerAction(
        string $controllerName,
        string $action,
        Zigra_Request $request,
        array $params,
        ?Aura\Session\Session $session_manager = null,
        bool $isError = false
    ): void {
        // Usa il mock del controller se è stato assegnato
        if (self::$mockController) {
            self::$mockController->preExecute();
            self::$mockController->$action($request);
            self::$mockController->postExecute();
            return;
        }

        // Chiama la logica originale
        parent::callControllerAction($controllerName, $action, $request, $params, $session_manager, $isError);
    }
}
