<?php
namespace In2code\FalGallery\Property\TypeConverter;

/**
 * Class AbstractFileFolderConverter
 *
 * @package In2code\FalGallery\Property\TypeConverter
 */
abstract class AbstractFileFolderConverter extends \TYPO3\CMS\Extbase\Property\TypeConverter\AbstractFileFolderConverter
{
    /**
     * @param int|string $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration
     * @return \TYPO3\CMS\Extbase\Domain\Model\AbstractFileFolder|void
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     */
    public function convertFrom(
        $source,
        $targetType,
        array $convertedChildProperties = array(),
        \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration = null
    ) {
        $object = $this->getOriginalResource($source);
        if (empty($this->expectedObjectType) || !$object instanceof $this->expectedObjectType) {
            throw new \TYPO3\CMS\Extbase\Property\Exception(
                'Expected object of type "' . $this->expectedObjectType . '" but got ' . get_class($object), 1342895975
            );
        }
        return $object;
    }
}
