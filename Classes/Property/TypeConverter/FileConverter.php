<?php
namespace In2code\FalGallery\Property\TypeConverter;

/**
 * Class FileConverter
 *
 * @package In2code\FalGallery\Property\TypeConverter
 */
class FileConverter  extends AbstractFileFolderConverter implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var array<string>
	 */
	protected $sourceTypes = array('integer', 'string');

	/**
	 * @var string
	 */
	protected $targetType = 'TYPO3\\CMS\\Core\\Resource\\File';

	/**
	 * @var string
	 */
	protected $expectedObjectType = 'TYPO3\\CMS\\Core\\Resource\\File';

	/**
	 * @param string|integer $source
	 * @return \TYPO3\CMS\Core\Resource\FileInterface|\TYPO3\CMS\Core\Resource\Folder
	 */
	protected function getOriginalResource($source) {
		return $this->fileFactory->retrieveFileOrFolderObject($source);
	}

}
