<?php

namespace Entner\PhpPackageBuilder\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class PhpPackageBuilderCommand extends Command {

  protected $gits = [];
  protected $packageInfo = [];
  protected $questions   = [];

  protected $packageDirectory;
  protected $stubsDirectory;

  public function __construct(Array $configs = [])
  {
    parent::__construct();

    $this->fileSystem = $this->getFileSystem();
    $this->gits       = $this->getGitConfigs();

    $this->destructDefaultConfigs($configs);
  }

  protected function destructDefaultConfigs(Array $configs)
  {
    $this->questions = $configs['questions'];
    $this->packageDirectory = $configs['packageDirectory'];
    $this->stubsDirectory   = $configs['stubsDirectory'];
  }

  protected function configure()
  {
    $this
    ->setName('build')
    ->setDescription('Build package')
    ->addArgument(
      'directory',
      InputArgument::OPTIONAL,
      'Directory name for composer-driven project'
    );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->ask($input, $output)
    ->generateDirAndFile()
    ->customize();
    
    $output->writeln('Congrautions!');

    return 0;
  }

  protected function getFileSystem()
  {
    return new Filesystem();
  }

  protected function getGitConfigs()
  {
    $gits = [];
    $segments = preg_split("/\n[\r]?/", trim(shell_exec('git config --list --global')));
    foreach ($segments as $segment) {
      list($key, $value) = explode('=', $segment);
      $gits[$key] = $value;
    }

    return $gits;
  }

  protected function getQuestionHelper()
  {
    return $this->getHelper('question');
  }

  protected function ask(InputInterface $input, OutputInterface $output)
  {
    $question_helper = $this->getQuestionHelper();

    foreach ($this->questions as $question_key => $question_value) {
      $question = match ($question_key) {
        'PACKAGE_NAME' => new Question($question_value, 'foo/bar'),
        'NAMESPACE'    => new Question($question_value, 'DEAFAULT_NAMESPACE'),
        'DESCRIPTION'  => new Question($question_value, 'DEAFAULT_DESC'),
        'AUTHOR_NAME'  => new Question($question_value, $this->gits['user.name']),
        'AUTHOR_EMAIL' => new Question($question_value, $this->gits['user.email']),
        'LICENSE'      => new Question($question_value, 'MIT'),
      };
      $this->packageInfo[$question_key] = $question_helper->ask($input, $output, $question);
    }
    
    $this->packageInfo['VENDOR']  = 'VENDOR';
    $this->packageInfo['PACKAGE'] = 'PACKAGE';

    $this->packageDirectory = $this->packageDirectory . '/' . $input->getArgument('directory');

    return $this;
  }

  protected function generateDirAndFile()
  {
    $this->fileSystem->mkdir($this->packageDirectory.'/src/', 0755);
    $this->fileSystem->touch($this->packageDirectory.'/src/.gitkeep');
    $this->copyFile('gitattributes', '.gitattributes');
    $this->copyFile('gitignore', '.gitignore');
    $this->copyFile('editorconfig', '.editorconfig');
    $this->copyFile('README.md');

    return $this;
  }

  protected function customize()
  {
    $this->setComposerJsonInfo()->setNamespace();

    return $this;
  }

  /**
   * Copy file.
   *
   * @param string $file
   * @param string $filename
   *
   * @internal param string $directory
   */
  protected function copyFile($file, $filename = '')
  {
      $target = $this->packageDirectory.'/'.($filename ?: $file);
      $content = str_replace(array_keys($this->packageInfo), array_values($this->packageInfo), file_get_contents($this->stubsDirectory.$file));

      $this->fileSystem->dumpFile($target, $content);
  }

  protected function setComposerJsonInfo()
  {
    $author = !empty($this->packageInfo['AUTHOR_EMAIL'])
    ? sprintf('--author "%s <%s>"', $this->packageInfo['AUTHOR_NAME'] ?? 'yourname', $this->packageInfo['AUTHOR_EMAIL'] ?? 'you@example.com')
    : '';

    exec(sprintf(
        'composer init --no-interaction --name "%s" %s --description "%s" --license %s --working-dir %s',
        $this->packageInfo['PACKAGE_NAME'],
        $author,
        $this->packageInfo['DESCRIPTION'] ?? 'Package description here.',
        $this->packageInfo['LICENSE'],
        $this->packageDirectory
    ));

    return $this;
  }

  protected function setNamespace()
  {
    $composerJson = $this->packageDirectory.'/composer.json';
    $composer = \json_decode(\file_get_contents($composerJson));

    $composer->autoload = [
      'psr-4' => [
        $this->packageInfo['NAMESPACE'].'\\' => 'src',
      ],
    ];

    \file_put_contents($composerJson, \json_encode($composer, \JSON_PRETTY_PRINT|\JSON_UNESCAPED_UNICODE));

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
    
}