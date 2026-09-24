<?php

/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2025 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

$html = [];
$attr = '';

$nameAttr = $name . (!empty($multiple) ? '[]' : '');

$attr .= !empty($size)     ? ' size="' . (int) $size . '"' : '';
$attr .= !empty($multiple) ? ' multiple' : '';
$attr .= ' name="' . htmlspecialchars($nameAttr, ENT_COMPAT, 'UTF-8') . '"';
$attr .= !empty($id)       ? ' id="' . htmlspecialchars($id, ENT_COMPAT, 'UTF-8') . '"' : '';

if (!empty($required)) {
    $attr .= ' required class="required"';
}

// Render the <select>; Joomla handles arrays for selected values
$html[] = HTMLHelper::_('select.genericlist', $options, $nameAttr, trim($attr), 'value', 'text', $value, $id);

Text::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');
Text::script('JGLOBAL_SELECT_PRESS_TO_SELECT');

$doc = Factory::getDocument();
$hasWebAssetManager = method_exists($doc, 'getWebAssetManager');

if ($hasWebAssetManager) {
    $wam = $doc->getWebAssetManager();
    $wam->usePreset('choicesjs')
        ->useScript('webcomponent.field-fancy-select');
} else {
    try {
        HTMLHelper::_('formbehavior.chosen', 'select');
    } catch (\Exception $e) {
        // Fallback gracefully
    }
}

$wrapAttrs = [];
if (!empty($class))        $wrapAttrs[] = 'class="' . htmlspecialchars($class, ENT_COMPAT, 'UTF-8') . '"';
if (!empty($allowCustom))  $wrapAttrs[] = 'allow-custom';
if (!empty($customPrefix)) $wrapAttrs[] = 'new-item-prefix="' . htmlspecialchars($customPrefix, ENT_COMPAT, 'UTF-8') . '"';

if ($hasWebAssetManager) : ?>
<joomla-field-fancy-select <?php echo implode(' ', $wrapAttrs); ?>>
    <?php echo implode("\n", $html); ?>
</joomla-field-fancy-select>
<?php else : ?>
    <?php echo implode("\n", $html); ?>
<?php endif; ?>
