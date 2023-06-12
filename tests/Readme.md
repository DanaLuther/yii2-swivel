# Testing the package
Launch the test stack by running the docker-compose.yml stack in swarm mode.

### Deploy the stack for full testing
Deploy the stack from the root directory
```shell
cd .. && \
docker stack deploy -c docker-compose.yml swivel
```
```shell
docker exec -it $(docker ps -lq -f name=swivel_php-cli) bash
```
```
cd /var/www/html/tests
codeception/bin/yii migrate
../vendor/bin/codecept run unit --coverage --coverage-html --html
```
