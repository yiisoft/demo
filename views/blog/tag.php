<?php
/**
 * @var \App\Entity\Tag $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;
?>

<h1><?php echo Html::encode($item->getLabel()) ?></h1>
<?php
echo Html::beginTag('ul');
foreach ($item->getPosts() as $post) {
    echo Html::beginTag('li');
    echo Html::a(Html::encode($post->getTitle()), $urlGenerator->generate('blog/page', ['slug' => $post->getSlug()]));
    echo Html::endTag('li');
}
echo Html::endTag('ul');
