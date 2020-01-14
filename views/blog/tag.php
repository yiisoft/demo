<?php
/**
 * @var \App\Entity\Tag $item
 * @var \App\Entity\Post[] $posts
 * @var \Spiral\Pagination\Paginator $paginator
 * @var \Closure $pageUrlGenerator
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;
?>

<h1><?php echo Html::encode($item->getLabel()) ?></h1>
<?php
echo Html::beginTag('ul');
foreach ($posts as $post) {
    echo Html::beginTag('li', ['class' => 'text-muted']);
    echo Html::a(Html::encode($post->getTitle()), $urlGenerator->generate('blog/page', ['slug' => $post->getSlug()]));
    echo ' by ';
    $userLogin = $post->getUser()->getLogin();
    echo Html::a(Html::encode($userLogin), $urlGenerator->generate('user/profile', ['login' => $userLogin]));
    echo ' at ';
    echo Html::tag('span', $post->getPublishedAt()->format('H:i d.m.Y'));
    echo Html::endTag('li');
}
echo Html::endTag('ul');

echo $this->render(
    '_pagination',
    [
        'paginator' => $paginator,
        'pageUrlGenerator' => $pageUrlGenerator,
    ]
);

