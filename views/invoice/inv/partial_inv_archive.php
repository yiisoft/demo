<?php
declare(strict_types=1);

/**
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 */

?>

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th><?= $s->trans('invoice'); ?></th>
            <th><?= $s->trans('created'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($invoices_archive as $invoice) {
            ?>
            <tr>
                <td>
                    <a href="<?= $urlGenerator->generate('inv/download', ['invoice' => basename($invoice)]); ?>"
                       title="<?= $s->trans('invoice'); ?>" style="text-decoration: none">
                        <?= basename($invoice); ?>
                    </a>
                </td>
                <td>
                    <?= date("F d Y H:i:s.", filemtime($invoice)); ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
