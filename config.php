<?php

return [
  'questions' => [
    'Name of package (example: <fg=yellow>foo/bar</fg=yellow>): ',
    'Namespace of package [<fg=yellow>{$defaultNamespace}</fg=yellow>]: ',
    'Description of package: ',
    'Author name of package [<fg=yellow>%s</fg=yellow>]: ',
    'Author email of package [<fg=yellow>%s</fg=yellow>]: ',
    'Do you want to test this package ? [<fg=yellow>Y/n</fg=yellow>]: ', 
    'Do you want to use php-cs-fixer format your code ? [<fg=yellow>Y/n</fg=yellow>]:', 
  ],
  'packageDirectory' => __DIR__,
  'stubsDirectory' => __DIR__ . '/src/stubs/',
];