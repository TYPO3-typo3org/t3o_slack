<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Pi1',
	'Sign up TYPO3 slack'
);

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 't3o_slack');

t3lib_extMgm::addLLrefForTCAdescr('tx_t3oslack_domain_model_slackuser', 'EXT:t3o_slack/Resources/Private/Language/locallang_csh_tx_t3oslack_domain_model_slackuser.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_t3oslack_domain_model_slackuser');
$TCA['tx_t3oslack_domain_model_slackuser'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:t3o_slack/Resources/Private/Language/locallang_db.xml:tx_t3oslack_domain_model_slackuser',
		'label' => 'uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => '',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/SlackUser.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_t3oslack_domain_model_slackuser.gif'
	),
);

?>
