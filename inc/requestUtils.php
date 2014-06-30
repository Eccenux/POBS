<?php

/**
 * requestUtils
 *
 * @author Maciej Nux Jaros
 */
class requestUtils
{
	/**
	 * Zwraca wartość z POST lub GET
	 *
	 * TODO: Możliwość podania kolejności POST/GET/COOKIE
	 *
	 * @note $pf_defaultValue zwracane jest bez zmiany typu.
	 * 
	 * @param string $pf_variableName Nazwa zmiennej.
	 * @param mixed $pf_defaultValue Wartość domyślna (jeśli POST/GET puste).
	 * @return string
	 */
	public static
			function pf_getString($pf_variableName, $pf_defaultValue = '')
	{
		if (empty($_POST[$pf_variableName]) && empty($_GET[$pf_variableName]))
		{
			return $pf_defaultValue;
		}
		else if (!empty($_POST[$pf_variableName]))
		{
			$pv_ret = $_POST[$pf_variableName];
		}
		else //(!empty($_GET[$pf_variableName]))
		{
			$pv_ret = $_GET[$pf_variableName];
		}

		return $pv_ret;
	}

	/**
	 * Zwraca wartość z POST lub GET
	 *
	 * @note $pf_defaultValue zwracane jest bez zmiany typu.
	 *
	 * @param string $pf_variableName Nazwa zmiennej.
	 * @param mixed $pf_defaultValue Wartość domyślna (jeśli POST/GET puste).
	 * @return int
	 */
	public static
			function pf_getInt($pf_variableName, $pf_defaultValue = '')
	{
		$pv_ret = self::pf_getString($pf_variableName, null);
		if (is_null($pv_ret))
		{
			return $pf_defaultValue;
		}

		return intval($pv_ret);
	}

}

?>
