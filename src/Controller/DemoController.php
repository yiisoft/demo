<?php

namespace App\Controller;

use app\helpers\DocHelper;
use Psr\Log\LoggerInterface;
use yii\exceptions\InvalidConfigException;
use yii\helpers\Json;
use Yiisoft\Web\Controller;
use Yiisoft\Web\NotFoundHttpException;
use Yiisoft\Web\Response;
use Yiisoft\Arrays\ArrayHelper;

class DemoController extends Controller
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct($id, $module, LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct($id, $module);
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'readme' => $readme,
        ]);
    }

    public function actionDocs()
    {
        $buffer = '';
        foreach (glob($this->app->getAlias('@doc/*.md')) as $doc) {
            $buffer .= DocHelper::doc($doc);
        }
        return $this->render('docs', [
            'buffer' => $buffer
        ]);
    }

    public function actionPackages()
    {
        $this->logger->debug('yo');
        $sections = ArrayHelper::index($this->app->params['packages'], 'id', 'section');
        $dependenciesFile = $this->app->getAlias('@runtime/github/dependencies.json');
        $allComposer = Json::decode(file_get_contents($this->app->getAlias('@runtime/github/allComposer.json')));

        $hasDependencies = file_exists($dependenciesFile);

        return $this->render('packages', [
            'title' => 'New composer packages',
            'subTitle' => 'How was Yii 2 split into several packages',
            'sections' => $sections,
            'hasDependencies' => $hasDependencies,
            'allComposer' => $allComposer,
        ]);
    }

    public function actionPackage(string $package): string
    {
        $packageDir = $this->app->getAlias("@github/$package");
        $repo = $this->app->params['packages'][$package] ?? false;
        if (!$repo) {
            throw new NotFoundHttpException("The package $package does not exist");
        }

        $metrics = simplexml_load_string(file_get_contents($this->app->getAlias("@webroot/img/packages/$package/summary.xml")));

        try {
            $readme = DocHelper::doc("$packageDir/README.md");
        } catch (InvalidConfigException $e) {
            $readme = 'No README.md';
        }

        $composer = file_get_contents("$packageDir/composer.json");

        return $this->render('package', [
            'package' => $repo,
            'readme' => $readme,
            'composer' => $composer,
            'metrics' => $metrics,
            'packageDir' => $packageDir,
        ]);
    }

    public function actionDependencyGraphData()
    {
        $this->app->response->format = Response::FORMAT_RAW;

        $dependenciesFile = $this->app->getAlias('@runtime/github/dependencies.json');

        if (!file_exists($dependenciesFile)) {
            throw new InvalidConfigException("You need to compute dependencies first. See README.md");
        }

        $dependencies = json_decode(file_get_contents($dependenciesFile), true);

        echo "parent@child\n";
        foreach ($dependencies as $dep) {
            echo $dep['source'] . '@' . $dep['target'] . "\n";
        }

    }

}
