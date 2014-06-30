<?php

/**
 * Narzędzia do operacji na plikach.
 *
 * @author Maciej Nux Jaros
 */
class pcDirUtils
{

	const
			DIRECTORY_SEPARATOR = '\\';

	/**
	 * Zabezpieczenie danych od użytkownika (ciągu znaków)
	 * pod kątem użycia w nazwie pliku.
	 * 
	 * @param string $pv_fileName Nazwa/ścieżka do zabezpieczenia (konwersji).
	 * @param bool $pv_allowSubdirs Czy dozwolone są podfoldery (w dół, ale nadal nie w górę drzewa).
	 * @return string
	 */
	public static
			function pf_makeSafeFilename($pv_fileName, $pv_allowSubdirs = false)
	{
		$f = trim($pv_fileName, './\\');
		if (!$pv_allowSubdirs)
		{
			$f = strtr($f, array('\\' => '__', '/'	 => '__', '..' => '_', ':'	 => '_'));
		}
		else
		{
			$f = strtr($f, array('\\' => '/', '..' => '_', ':'	 => '_'));
			$f = trim($f, '/');
		}
		return $f;
	}

	/**
	 * Zwraca zawartość folderu jako tablicę względnych ścieżek.
	 *
	 * Działa jak scandir, ale pomija foldery techniczne i zwraca najpierw pliki.
	 *
	 * @param string $dir Pełna ścieżka folderu.
	 * @return array
	 */
	public static
			function pf_filteredScanDir($dir)
	{
		$pv_content = scandir($dir);
		$pv_contentFiltered = array();
		// files first
		foreach ($pv_content as $pv_entry)
		{
			if ($pv_entry === '.' || $pv_entry === '..')
			{
				continue;
			}
			if (!is_dir($dir . self::DIRECTORY_SEPARATOR . $pv_entry))
			{
				$pv_contentFiltered[] = $pv_entry;
			}
		}
		// dirs
		foreach ($pv_content as $pv_entry)
		{
			if ($pv_entry === '.' || $pv_entry === '..')
			{
				continue;
			}
			if (is_dir($dir . self::DIRECTORY_SEPARATOR . $pv_entry))
			{
				$pv_contentFiltered[] = $pv_entry;
			}
		}
		return $pv_contentFiltered;
	}

	/**
	 * Łączy dwie ścieżki dodając między nimi seprator folderu w razie potrzeby.
	 *
	 * @note Jeśli $pathAppended zawiera literę dysku, to $path nie zostanie uwzględnione.
	 *
	 * @param string $path Ścieżka względna lub pełna.
	 * @param string $pathAppended Ścieżka względna.
	 * @return string
	 */
	public static
			function pf_joinPaths($path, $pathAppended)
	{
		if (preg_match('#^[a-z]:#i', $pathAppended))
		{
			return $pathAppended;
		}
		$post = ltrim($pathAppended, self::DIRECTORY_SEPARATOR);
		if (!empty($path)) // spr. path, żeby '\\' działało prawidłowo (czyli '\\' + 'blah' = '\\blah', ale '' + 'blah' = 'blah')
		{
			$pre = rtrim($path, self::DIRECTORY_SEPARATOR);
			return $pre . self::DIRECTORY_SEPARATOR . $post;
		}
		else
		{
			return $post;
		}
	}

	/**
	 * Łączy dwie ścieżki dodając między nimi seprator folderu w razie potrzeby.
	 *
	 * @param string $path Ścieżka względna lub pełna.
	 * @param string $pathAppended Ścieżka względna.
	 * @return string
	 */

	/**
	 * Usunięcie bazowej ścieżki z podanej ścieżki.
	 *
	 * @param string $pv_basePath Bazowa ścieżka (powinna być pełna).
	 * @param string $pv_path Ścieżka do oczyszczenia (powinna być pełna).
	 * @return string Względna ścieżka.
	 */
	public static
			function pf_removeBase($pv_basePath, $pv_path)
	{
		// ujednolicenie ukośników
		$pv_basePath = strtr($pv_basePath, '/', self::DIRECTORY_SEPARATOR);
		$pv_path = strtr($pv_path, '/', self::DIRECTORY_SEPARATOR);
		// usunięcie zakończeń
		$pv_basePath = rtrim($pv_basePath, self::DIRECTORY_SEPARATOR);
		$pv_path = rtrim($pv_path, self::DIRECTORY_SEPARATOR);
		// usunięcie bazy
		$pv_path = str_replace($pv_basePath, '', $pv_path);
		$pv_path = ltrim($pv_path, self::DIRECTORY_SEPARATOR);
		return $pv_path;
	}

}

?>
