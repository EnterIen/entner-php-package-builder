<?php

class PhpPackageBuilderCommand {
  protected $configs = [];

  public function __construct(Array $configs = [])
  {
    $this->configs = $configs;
  }

  public function configure()
  {
    $this->setName('build')
    ->setDescription('Build package')
    ->addArgument(
      'directory',
      InputArgument::OPTIONAL,
      'Directory name for composer-driven project'
    );
    
  }

  public function getQuestionHelper()
  {
    return $this->getHelper('question');
  }

  public function execute(InputInterface $input, OutputInterface $output)
  {
    // $this->getLocalVariables()
    // ->ask()
    // ->init()
    // ->build();

    $output->writeln('Congrautions!');

    return 0;
  }

  protected function getLocalVariables()
  {
    $segments = preg_split("/\n[\r]?/", trim(shell_exec('git config --list --global')));
    foreach ($segments as $segment) {
      list($key, $value) = explode('=', $segment);
      $this->git_configs[$key] = $value;
    }
    
    return $this;
  }

  protected function ask()
  {
    // 1.package name
    // 2.namespace
    // 3.test?
    // 4.phpcs?
    // 
    // 
    
    // $question_helper = $this->getQuestionHelper();

    // foreach ($this->configs['question'] as $question_key => $question_value) {
    //   $question = match ($question_key) {
    //     'package_name' => new Question($question_value, 'foo/bar')
    //   }
    //   $this->answers[] = $question_helper->ask($input, $output, $question);
    // }
    // 
    
    return $this;
  }

  protected function init()
  {
    $this->initFileSystem()->initComposer()->setNamespace();
  }

  protected function initFileSystem()
  {
    // $this->file_system = new Filesystem();

    return $this;
  }

  protected function initComposer()
  {
    // $author = !empty($this->info['EMAIL'])
    // ? sprintf('--author "%s <%s>"', $this->info['NAME'] ?? 'yourname', $this->info['EMAIL'] ?? 'you@example.com')
    // : '';

    // exec(sprintf(
    //     'composer init --no-interaction --name "%s" %s --description "%s" --license %s --working-dir %s',
    //     $this->info['PACKAGE_NAME'],
    //     $author,
    //     $this->info['DESCRIPTION'] ?? 'Package description here.',
    //     $this->info['LICENSE'],
    //     $this->packageDirectory
    // ));

    return $this;
  }

  protected function setNamespace()
  {
    // $composerJson = $this->packageDirectory.'/composer.json';
    // $composer = \json_decode(\file_get_contents($composerJson));

    // $composer->autoload = [
    //   'psr-4' => [
    //     $this->info['NAMESPACE'].'\\' => 'src',
    //   ],
    // ];

    // \file_put_contents($composerJson, \json_encode($composer, \JSON_PRETTY_PRINT|\JSON_UNESCAPED_UNICODE));

    return $this;
  }

  /**
   * Create package directory and base files.
   *
   * @param array $config
   *
   * @return string
   */
  protected function build(array $config)
  {
    // $this->fs->mkdir($this->packageDirectory.'/src/', 0755);
    // $this->fs->touch($this->packageDirectory.'/src/.gitkeep');
    // $this->copyFile('gitattributes', '.gitattributes');
    // $this->copyFile('gitignore', '.gitignore');
    // $this->copyFile('editorconfig', '.editorconfig');

    // $this->copyReadmeFile($config);

    // if ($config['phpunit']) {
    //   $this->copyPHPUnitFile($config);
    // }
    // if ($config['phpcs']) {
    //   $this->createCSFixerConfiguration($config);
    // }

    // return $this->packageDirectory;

    return $this;
  }

  /**
     * @param string $string
     *
     * @return mixed
     */
    protected function studlyCase($string)
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function camelCase($string)
    {
        return lcfirst($this->studlyCase($string));
    }

    /**
     * Create README.md.
     */
    protected function copyReadmeFile()
    {
        $this->copyFile('README.md');
    }

    /**
     * Create PHPUnit files.
     */
    protected function copyPHPUnitFile()
    {
        $this->fs->dumpFile($this->packageDirectory.'/tests/.gitkeep', '');
        $this->copyFile('phpunit_config', 'phpunit.xml.dist');
    }

    /**
     * Create PHP-CS-fixer.
     */
    protected function createCSFixerConfiguration()
    {
        $this->copyFile('php_cs', '.php-cs-fixer.php');
    }
}