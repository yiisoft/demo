<?php

use app\helpers\DocHelper;

/** @var string $document */
/** @var string $title */
/** @var string $subTitle */

$this->title = $title;
$this->subTitle = $subTitle;

?>

<?= DocHelper::doc($document) ?>