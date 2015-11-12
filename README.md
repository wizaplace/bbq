# Install
`composer install`

# Run tests
* Create the file `tests/config.php` with :
```
<?php
return [
    'key' => 'aws-key',
    'secret' => 'aws-secret',
    'region' => 'eu-west-1',
    'sqs_url' => 'sqs-url'
];
```
* run `tests/run.sh`