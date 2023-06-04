# second-bid-auction

This is a test Project

# Requirements:

PHP v8.1 with Xdebug extension

# Run project 

To run tests you can use the following commands:

```shell
make tests # it will run tests only
make tests-coverage # it will run tests and shows a test coverage report 
```

If you do not have the make command, you can use the following equivalents:

```shell
php -dxdebug.mode=develop ./tests_run.php # for tests run 
php -dxdebug.mode=coverage,develop ./tests_run.php # for tests run with coverage report 
```
