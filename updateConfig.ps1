cp .\setup\laradock.env .\laradock\.env
cp .\setup\default.conf .\laradock\nginx\sites\default.conf
cp .\setup\mailaway.env .\.env
cp .\setup\laravel-worker.conf .\laradock\php-worker\supervisord.d\laravel-worker.conf
cp .\setup\docker-compose.yml .\laradock\docker-compose.yml
cp .\setup\traefik.toml .\laradock\traefik\traefik.toml
