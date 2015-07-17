<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'In2code.' . $_EXTKEY,
	'Pi1',
	array(
		'Gallery' => 'show, list, category',
	),
	// non-cacheable actions
	array(
		'Gallery' => '',
	)
);
